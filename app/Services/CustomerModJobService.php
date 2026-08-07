<?php

namespace App\Services;

use App\Models\JobSettings;
use Illuminate\Support\Collection;

class CustomerModJobService
{
    /**
     * Sezioni dinamiche (mod_jobs_settings.type = 'section') visibili nel modulo indicato.
     */
    public static function sectionsForModule(string $moduleKey): Collection
    {
        return JobSettings::query()
            ->where('type', 'section')
            ->where('json_modules->' . $moduleKey, true)
            ->orderBy('title')
            ->get();
    }

    /**
     * Estrae ricorsivamente tutti i "name" presenti in uno schema FormKit
     * (nodi diretti + children), usato sia come default vuoto per un cliente
     * senza valori salvati, sia per determinare quali chiavi di
     * customers_mod_jobs.values appartengono a un dato modulo.
     */
    public static function extractNames(array $items): array
    {
        $names = [];
        foreach ($items as $item) {
            if (isset($item['name'])) {
                $names[$item['name']] = '';
            }
            if (isset($item['children']) && is_array($item['children'])) {
                $names = array_merge($names, self::extractNames($item['children']));
            }
        }
        return $names;
    }

    /**
     * Nomi di tutti i campi dichiarati nelle sezioni di un modulo (array di
     * righe mod_jobs_settings con 'schema' come stringa JSON) — decodifica
     * ogni sezione e poi cammina l'albero FormKit con extractNames().
     */
    public static function declaredFieldNames(array $moduleSchema): array
    {
        $names = [];
        foreach ($moduleSchema as $section) {
            $decoded = json_decode($section['schema'] ?? '', true);
            if (is_array($decoded)) {
                $names = array_merge($names, self::extractNames($decoded));
            }
        }
        return $names;
    }

    /**
     * Compatta i valori delle sezioni dinamiche (es. componenti famiglia,
     * attestazioni ISEE) in base allo schema inviato dal client, che è la
     * fonte di verità di quali istanze esistono davvero in questo momento
     * (aggiunte/rimosse dall'utente in FormModJobs.vue tramite addSchema()/
     * removeSchema()). Lato client non è affidabile rinumerare direttamente
     * i valori: FormKit lega ogni nodo al proprio "name" solo alla creazione
     * e non lo ri-lega mai a caldo, quindi qualunque tentativo di
     * rinominare/spostare le chiavi mentre i nodi sono ancora montati
     * produce dati mescolati o persi. Qui invece è puro PHP su dati statici:
     * per ogni sezione dinamica si prende, nell'ordine dichiarato dallo
     * schema, il valore già presente sotto ciascun nome corrente, e lo si
     * riassegna alle chiavi canoniche (base, _1, _2, ...) — qualunque altra
     * chiave della stessa famiglia non più referenziata dallo schema
     * (es. una riga appena eliminata) viene scartata.
     */
    public static function compactDynamicGroups(array $schema, ?array $values): array
    {
        if (!is_array($values)) {
            return $values ?? [];
        }

        foreach ($schema as $section) {
            if (empty($section['dynamic'])) {
                continue;
            }

            $schemaEntries = json_decode($section['schema'] ?? '', true);
            if (!is_array($schemaEntries) || empty($schemaEntries)) {
                continue;
            }

            $names = array_filter(array_column($schemaEntries, 'name'));
            if (empty($names)) {
                continue;
            }

            $baseName = preg_replace('/_\d+$/', '', $names[0]);

            // valori "voluti", nell'ordine in cui lo schema li dichiara adesso
            $wanted = [];
            foreach ($names as $name) {
                if (array_key_exists($name, $values)) {
                    $wanted[] = $values[$name];
                }
            }

            // rimuove tutte le chiavi esistenti di questa famiglia dinamica
            foreach (array_keys($values) as $key) {
                if ($key === $baseName || preg_match('/^' . preg_quote($baseName, '/') . '_\d+$/', $key)) {
                    unset($values[$key]);
                }
            }

            // le riassegna in sequenza: la prima diventa la chiave base, le altre _1, _2, ...
            foreach ($wanted as $i => $value) {
                $values[$i === 0 ? $baseName : "{$baseName}_{$i}"] = $value;
            }
        }

        return $values;
    }

    /**
     * Espande lo schema di ogni sezione dinamica al numero di istanze
     * effettivamente presenti nei valori salvati, così il client monta
     * subito il numero corretto di gruppi ripetuti (es. componenti famiglia).
     */
    public static function expandDynamicSchemaForValues(array $moduleSchema, array $values): array
    {
        $result = $moduleSchema;

        foreach ($moduleSchema as $section) {
            if (empty($section['dynamic'])) {
                continue;
            }

            $schemaArray = json_decode($section['schema'], true);
            if (!is_array($schemaArray) || empty($schemaArray)) {
                continue;
            }

            $dynamicName = $schemaArray[0]['name'];

            // Conto quanti gruppi dinamici ci sono nei dati
            $c = 0;
            foreach (array_keys($values) as $keyName) {
                if (substr($keyName, 0, strlen($dynamicName)) === $dynamicName) {
                    $c++;
                }
            }

            foreach ($result as $k => $sectionToEdit) {
                if ($sectionToEdit['id'] != $section['id']) {
                    continue;
                }

                $expanded = json_decode($sectionToEdit['schema'], true);
                for ($i = 1; $i < $c; $i++) {
                    $expanded[$i] = $expanded[0];
                    $expanded[$i]['name'] = $expanded[0]['name'] . '_' . $i;
                    $expanded[$i]['_id'] = uniqid();
                }

                $result[$k]['schema'] = json_encode($expanded);
            }
        }

        return $result;
    }

    /**
     * Prepara schema e valori da passare al client in edit(): se il cliente
     * non ha ancora nessun valore salvato per questo modulo, ritorna i
     * default vuoti (nome => '') e lo schema del modulo così com'è;
     * altrimenti espande lo schema delle sezioni dinamiche al numero di
     * istanze presenti nei valori e passa i valori invariati.
     *
     * @return array{schema: array, values: array}
     */
    public static function hydrateEdit(array $moduleSchema, ?array $persistedValues): array
    {
        if (empty($persistedValues)) {
            return [
                'schema' => $moduleSchema,
                'values' => self::declaredFieldNames($moduleSchema),
            ];
        }

        return [
            'schema' => self::expandDynamicSchemaForValues($moduleSchema, $persistedValues),
            'values' => $persistedValues,
        ];
    }

    /**
     * Scrive i valori postati da un modulo dentro il blob customers_mod_jobs.values
     * del cliente, toccando SOLO le chiavi dei campi dichiarati nello schema
     * di questo modulo. customers_mod_jobs ha una riga per cliente condivisa
     * fra tutti i moduli (una sezione può essere visibile in più moduli
     * contemporaneamente), quindi un replace totale del blob cancellerebbe i
     * valori di sezioni visibili solo in un altro modulo. Qui invece:
     * 1) le famiglie dinamiche dichiarate nel modulo vengono ripulite per
     *    intero (pattern base/_N) e ricostruite in base ai valori postati,
     *    così un'istanza cancellata dall'utente sparisce davvero;
     * 2) ogni altro campo dichiarato nel modulo viene sovrascritto con il
     *    valore postato (o rimosso se assente, cioè svuotato);
     * 3) qualunque chiave non dichiarata nello schema di questo modulo
     *    (campi di sezioni di altri moduli) resta intatta.
     */
    public static function mergeModuleValues(array $existingValues, array $moduleSchema, array $postedValues): array
    {
        $compacted = self::compactDynamicGroups($moduleSchema, $postedValues);

        $final = $existingValues;

        foreach ($moduleSchema as $section) {
            if (empty($section['dynamic'])) {
                continue;
            }

            $schemaEntries = json_decode($section['schema'] ?? '', true);
            $names = array_filter(array_column($schemaEntries ?? [], 'name'));
            if (empty($names)) {
                continue;
            }

            $baseName = preg_replace('/_\d+$/', '', $names[0]);

            foreach (array_keys($final) as $key) {
                if ($key === $baseName || preg_match('/^' . preg_quote($baseName, '/') . '_\d+$/', $key)) {
                    unset($final[$key]);
                }
            }
        }

        $declaredNames = array_keys(self::declaredFieldNames($moduleSchema));
        foreach ($declaredNames as $name) {
            if (array_key_exists($name, $compacted)) {
                $final[$name] = $compacted[$name];
            } else {
                unset($final[$name]);
            }
        }

        return $final;
    }
}
