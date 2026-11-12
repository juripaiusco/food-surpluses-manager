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

            $baseName = preg_replace('/(_\d+)+$/', '', $names[0]);

            // valori "voluti", nell'ordine in cui lo schema li dichiara adesso
            $wanted = [];
            foreach ($names as $name) {
                if (array_key_exists($name, $values)) {
                    $wanted[] = $values[$name];
                }
            }

            // rimuove tutte le chiavi esistenti di questa famiglia dinamica
            // (anche quelle legacy a suffisso multiplo, es. base_2_1)
            foreach (array_keys($values) as $key) {
                if (self::isDynamicFamilyKey($key, $baseName)) {
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

            // Conto quanti gruppi dinamici ci sono nei dati (match esatto
            // base/base_N, non prefisso di stringa: altrimenti chiavi non
            // correlate che condividono il prefisso, es. campi flat legacy
            // dello stesso gruppo, vengono contate come istanze fantasma).
            $c = 0;
            foreach (array_keys($values) as $keyName) {
                if ($keyName === $dynamicName || preg_match('/^' . preg_quote($dynamicName, '/') . '_\d+$/', $keyName)) {
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

        // self-heal: riporta eventuali istanze legacy a suffisso multiplo
        // (es. mod_jobs_famiglia_comp_2_1, generate da una vecchia versione di
        // addSchema()) negli slot canonici, così sono visibili in UI e al
        // prossimo salvataggio vengono persistite correttamente. Solo per le
        // famiglie dinamiche di questo modulo, senza rilevamento generico.
        [$persistedValues] = self::repairDynamicGroups(
            $persistedValues,
            self::moduleDynamicBaseNames($moduleSchema),
            false
        );

        $expandedSchema = self::expandDynamicSchemaForValues($moduleSchema, $persistedValues);

        return [
            'schema' => $expandedSchema,
            'values' => self::sanitizeDynamicGroupValues($expandedSchema, $persistedValues),
        ];
    }

    /**
     * Forza a [] qualunque valore non-array delle istanze di sezioni
     * dinamiche (es. null residuo da dati legacy): i nodi FormKit 'group'
     * lato client richiedono un valore oggetto/array, altrimenti l'hydrate
     * lancia eccezione e l'intero form non monta.
     */
    private static function sanitizeDynamicGroupValues(array $moduleSchema, array $values): array
    {
        foreach ($moduleSchema as $section) {
            if (empty($section['dynamic'])) {
                continue;
            }

            $schemaEntries = json_decode($section['schema'] ?? '', true);
            if (!is_array($schemaEntries)) {
                continue;
            }

            foreach (array_column($schemaEntries, 'name') as $name) {
                if (!is_array($values[$name] ?? null)) {
                    $values[$name] = [];
                }
            }
        }

        return $values;
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

            $baseName = preg_replace('/(_\d+)+$/', '', $names[0]);

            foreach (array_keys($final) as $key) {
                if (self::isDynamicFamilyKey($key, $baseName)) {
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

    /**
     * True se $key appartiene alla famiglia dinamica $baseName: la chiave
     * base stessa, un'istanza canonica "base_N" o un'istanza legacy a
     * suffisso multiplo "base_N_M..." (es. mod_jobs_famiglia_comp_2_1).
     */
    public static function isDynamicFamilyKey(string $key, string $baseName): bool
    {
        return (bool) preg_match('/^' . preg_quote($baseName, '/') . '(_\d+)*$/', $key);
    }

    /**
     * Nomi base delle sezioni dinamiche (dynamic = '1') di un modulo
     * (array di righe mod_jobs_settings con 'schema' come stringa JSON).
     */
    public static function moduleDynamicBaseNames(array $moduleSchema): array
    {
        $names = [];

        foreach ($moduleSchema as $section) {
            if (empty($section['dynamic'])) {
                continue;
            }

            $name = self::schemaBaseName($section['schema'] ?? null);
            if ($name !== null) {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * Nomi base di tutte le sezioni marcate come dinamiche/ripetibili
     * (mod_jobs_settings.dynamic = '1'), es. "mod_jobs_famiglia_comp".
     */
    public static function dynamicBaseNames(): array
    {
        $names = [];

        foreach (JobSettings::where('dynamic', '1')->pluck('schema') as $schemaJson) {
            $name = self::schemaBaseName($schemaJson);
            if ($name !== null) {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    private static function schemaBaseName($schemaJson): ?string
    {
        $schema = is_string($schemaJson) ? json_decode($schemaJson, true) : $schemaJson;
        if (!is_array($schema)) {
            return null;
        }
        if (isset($schema[0])) {
            $schema = $schema[0];
        }
        if (empty($schema['name']) || !is_string($schema['name'])) {
            return null;
        }

        return preg_replace('/(_\d+)+$/', '', $schema['name']);
    }

    /**
     * Individua le famiglie di campi dinamici la cui sequenza di chiavi non è
     * quella canonica (base, _1, _2, ...) e le compatta: entry vuote ("[]")
     * scartate, entry identiche a una già tenuta scartate come duplicati,
     * le restanti rinumerate in ordine.
     *
     * - Basi in $dynamicBaseNames (whitelist mod_jobs_settings.dynamic): la
     *   famiglia include anche le chiavi legacy a suffisso multiplo
     *   (base_2_1, ...), ordinate per tupla numerica: base, _1, _2, _2_1, _3.
     * - $genericDetection: rileva anche basi non in whitelist che hanno almeno
     *   un sibling "base_N" (solo suffisso singolo). Utile nel comando di
     *   riparazione (dry-run revisionato), da NON usare a runtime.
     *
     * @return array{0: array, 1: array} [nuovo array values, log modifiche]
     *         log: [['family', 'before' => [chiavi], 'after' => [chiavi], 'dropped' => [descrizioni]]]
     */
    public static function repairDynamicGroups(array $values, array $dynamicBaseNames = [], bool $genericDetection = true): array
    {
        $keys = array_keys($values);

        // base => true se ammessi suffissi multipli (whitelist), false se solo singolo
        $bases = [];

        if ($genericDetection) {
            foreach ($keys as $key) {
                if (preg_match('/^(.+)_(\d+)$/', (string) $key, $m)) {
                    $base = $m[1];
                    if (!preg_match('/_\d+$/', $base) && array_key_exists($base, $values)) {
                        $bases[$base] = false;
                    }
                }
            }
        }

        foreach ($dynamicBaseNames as $base) {
            foreach ($keys as $key) {
                if (self::isDynamicFamilyKey((string) $key, $base)) {
                    $bases[$base] = true;
                    break;
                }
            }
        }

        $changes = [];

        foreach ($bases as $base => $multiSuffix) {
            $base = (string) $base;
            $pattern = '/^' . preg_quote($base, '/') . ($multiSuffix ? '((?:_\d+)*)' : '((?:_\d+)?)') . '$/';

            $entries = [];
            foreach (array_keys($values) as $key) {
                if (preg_match($pattern, (string) $key, $m)) {
                    $index = $m[1] === '' ? [] : array_map('intval', explode('_', ltrim($m[1], '_')));
                    $entries[] = ['key' => (string) $key, 'index' => $index, 'value' => $values[$key]];
                }
            }

            if (empty($entries)) {
                continue;
            }

            usort($entries, fn ($a, $b) => self::compareIndex($a['index'], $b['index']));

            $before = array_column($entries, 'key');

            $kept = [];
            $dropped = [];
            foreach ($entries as $entry) {
                if (is_array($entry['value']) && array_is_list($entry['value'])) {
                    $dropped[] = "{$entry['key']} (vuoto)";
                    continue;
                }

                $normalized = self::normalizeForCompare($entry['value']);
                foreach ($kept as $k) {
                    if ($k['normalized'] === $normalized) {
                        $dropped[] = "{$entry['key']} (duplicato di {$k['key']})";
                        continue 2;
                    }
                }

                $kept[] = $entry + ['normalized' => $normalized];
            }

            $after = [];
            foreach ($kept as $i => $entry) {
                $after[] = $i === 0 ? $base : "{$base}_{$i}";
            }

            // niente da fare: la sequenza è già quella canonica (base, _1, _2, ...)
            if ($before === $after) {
                continue;
            }

            foreach ($entries as $entry) {
                unset($values[$entry['key']]);
            }

            foreach ($kept as $i => $entry) {
                $values[$after[$i]] = $entry['value'];
            }

            $changes[] = [
                'family' => $base,
                'before' => $before,
                'after' => $after,
                'dropped' => $dropped,
            ];
        }

        return [$values, $changes];
    }

    /**
     * Confronto lessicografico fra tuple di indici: [] < [1] < [2] < [2,1] < [3].
     */
    private static function compareIndex(array $a, array $b): int
    {
        $n = min(count($a), count($b));
        for ($i = 0; $i < $n; $i++) {
            if ($a[$i] !== $b[$i]) {
                return $a[$i] <=> $b[$i];
            }
        }

        return count($a) <=> count($b);
    }

    /**
     * Rappresentazione canonica per il confronto stretto fra istanze
     * (ordine chiavi ignorato, tipi e stringhe confrontati in modo stretto:
     * niente "01" == "1").
     */
    private static function normalizeForCompare($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        if (!array_is_list($value)) {
            ksort($value);
        }

        foreach ($value as $k => $v) {
            $value[$k] = self::normalizeForCompare($v);
        }

        return $value;
    }
}
