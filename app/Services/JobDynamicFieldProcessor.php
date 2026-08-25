<?php

namespace App\Services;



use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use NigroSimone\CodiceFiscale;

class JobDynamicFieldProcessor
{
    /**
     * @param Request $request
     * @param int|null $customerId null in creazione, id cliente in modifica
     * @return string|false
     */
    public static function exe(Request $request, ?int $customerId = null)
    {
        $field_with_FncPhp_array = JobDynamicFieldProcessor::search_field_with_FncPhp(
            $request->input('customers_mod_jobs_schema')
        );

        foreach ($field_with_FncPhp_array as $section) {
            foreach ($section as $field) {

                $method = $field['field']['fnc_php'];

                if (method_exists(JobDynamicFieldProcessor::class, $method)) {

                    $result = call_user_func(
                        [JobDynamicFieldProcessor::class, $method],
                        $field,
                        $request,
                        $customerId
                    );

                    if ($result) {
                        return $result;
                    }

                }

            }
        }

        return false;
    }

    /**
     * Cerca ricorsivamente tutti i campi che contengono 'fnc_php', tracciando
     * la catena dei gruppi FormKit ($formkit === 'group') attraversati, per
     * poter risolvere il valore reale anche quando il campo sta annidato
     * dentro una sezione dinamica/ripetuta (es. componenti famiglia).
     */
    public static function findFieldsWithFncPhp(array $fields, &$result = [], $path = [], $groupPath = [])
    {
        foreach ($fields as $key => $value) {
            $currentPath = array_merge($path, [$key]);

            // Se questo elemento ha la chiave fnc_php → lo aggiungo ai risultati
            if (is_array($value) && array_key_exists('fnc_php', $value)) {
                $result[] = [
                    'path' => $currentPath,
                    'groupPath' => $groupPath,
                    'field' => $value
                ];
            }

            // Se figlio è un array, continua la ricerca
            if (is_array($value)) {
                $nextGroupPath = $groupPath;
                if (($value['$formkit'] ?? null) === 'group' && !empty($value['name'])) {
                    $nextGroupPath[] = $value['name'];
                }
                JobDynamicFieldProcessor::findFieldsWithFncPhp($value, $result, $currentPath, $nextGroupPath);
            }
        }

        return $result;
    }

    /**
     * Risolve il valore di un campo in customers_mod_jobs_values, seguendo
     * eventuale catena di gruppi FormKit (campo piatto se $groupPath è vuoto).
     */
    public static function resolveFieldValue(array $groupPath, string $fieldName, Request $request)
    {
        return JobDynamicFieldProcessor::resolveFieldValueFromArray(
            $groupPath,
            $fieldName,
            $request->input('customers_mod_jobs_values') ?? []
        );
    }

    /**
     * Come resolveFieldValue(), ma legge da un array di valori già in memoria
     * (es. customers_mod_jobs.values persistiti) invece che dalla request.
     */
    public static function resolveFieldValueFromArray(array $groupPath, string $fieldName, array $values)
    {
        $value = $values;

        foreach ($groupPath as $group) {
            $value = is_array($value) ? ($value[$group] ?? null) : null;
        }

        return is_array($value) ? ($value[$fieldName] ?? null) : null;
    }

    /**
     * Cerca tutti i campi con fnc_php partendo dall'input passato dal form
     *
     * @param $input
     * @return array
     */
    public static function search_field_with_FncPhp($input)
    {
        $field_with_FncPhp_array = [];

        foreach ($input as $section) {
            $schema = json_decode($section['schema'], true);

            $findFieldsWithFncPhp = JobDynamicFieldProcessor::findFieldsWithFncPhp($schema);

            if (count($findFieldsWithFncPhp) > 0) {
                $field_with_FncPhp_array[] = $findFieldsWithFncPhp;
            }
        }

        return $field_with_FncPhp_array;
    }

    /**
     * Risolve "Nome Cognome" della persona a cui appartiene un campo CF, per
     * riferirlo nel messaggio di errore. Campo assistito (groupPath vuoto):
     * nome/cognome sono le colonne customers.name/surname, inviate come
     * campi piatti nella stessa request. Campo componente famiglia
     * (dentro un gruppo dinamico): nome/cognome sono campi fratelli con lo
     * stesso prefisso del campo CF (mod_jobs_famiglia_comp_cf → _nome/_cognome).
     */
    public static function resolvePersonLabel(array $groupPath, string $fieldName, Request $request): ?string
    {
        $base = preg_replace('/_cf$/', '', $fieldName);

        if (!empty($groupPath)) {
            $nome = JobDynamicFieldProcessor::resolveFieldValue($groupPath, $base . '_nome', $request);
            $cognome = JobDynamicFieldProcessor::resolveFieldValue($groupPath, $base . '_cognome', $request);
        } else {
            $nome = $request->input('name');
            $cognome = $request->input('surname');
        }

        $label = trim(($nome ?? '') . ' ' . ($cognome ?? ''));

        return $label !== '' ? $label : null;
    }

    /**
     * @param $field
     * @param Request $request
     * @param int|null $customerId
     * @return string|false
     *
     * Validazione del Codice Fiscale: formato, univocità nella stessa
     * submission (es. capofamiglia e componente famiglia con lo stesso CF) e
     * univocità cross-cliente (stesso CF già presente su un'altra anagrafica).
     */
    public static function validation_cf($field, Request $request, ?int $customerId = null)
    {
        $groupPath = $field['groupPath'] ?? [];
        $fieldName = $field['field']['name'];
        $value = JobDynamicFieldProcessor::resolveFieldValue($groupPath, $fieldName, $request);

        if (!$value) {
            return false;
        }

        $value = strtoupper(trim($value));

        $cf = new CodiceFiscale();
        if (!$cf->validaCodiceFiscale($value)) {
            $label = JobDynamicFieldProcessor::resolvePersonLabel($groupPath, $fieldName, $request);
            return $label ? "Codice Fiscale non valido: {$label}" : "Codice Fiscale non valido";
        }

        // Univocità nella stessa submission
        $siblingSections = JobDynamicFieldProcessor::search_field_with_FncPhp(
            $request->input('customers_mod_jobs_schema')
        );
        foreach ($siblingSections as $siblingSection) {
            foreach ($siblingSection as $sibling) {
                if (($sibling['field']['fnc_php'] ?? null) !== 'validation_cf') {
                    continue;
                }
                if (($sibling['groupPath'] ?? []) === $groupPath && $sibling['field']['name'] === $fieldName) {
                    continue; // è il campo stesso
                }

                $siblingValue = JobDynamicFieldProcessor::resolveFieldValue(
                    $sibling['groupPath'] ?? [],
                    $sibling['field']['name'],
                    $request
                );

                if ($siblingValue && strtoupper(trim($siblingValue)) === $value) {
                    return "Codice Fiscale duplicato nella stessa scheda";
                }
            }
        }

        // Univocità nella stessa scheda anche per le sezioni NON incluse nel
        // modulo attualmente in uso (es. modifica da 'customers', che mostra
        // solo la sezione Anagrafica, mentre il CF di un componente famiglia
        // è stato inserito in precedenza da 'jobs_listen', che mostra anche
        // la sezione Famiglia componenti): senza questo controllo il campo
        // resterebbe invisibile al confronto "stessa submission" sopra,
        // perché quel campo non fa parte dello schema postato da questo
        // modulo — si confronta quindi col valore già persistito.
        if ($customerId) {
            $currentSectionIds = array_column($request->input('customers_mod_jobs_schema', []), 'id');

            $otherSections = \App\Models\JobSettings::query()
                ->where('type', 'section')
                ->whereNotIn('id', $currentSectionIds)
                ->get(['id', 'schema'])
                ->map(fn ($s) => ['schema' => $s->schema])
                ->all();

            if (!empty($otherSections)) {
                $persistedValues = optional(
                    \App\Models\CustomerModJob::query()->where('customer_id', $customerId)->first()
                )->values ?? [];

                $otherFieldsWithFncPhp = JobDynamicFieldProcessor::search_field_with_FncPhp($otherSections);

                foreach ($otherFieldsWithFncPhp as $otherSection) {
                    foreach ($otherSection as $sibling) {
                        if (($sibling['field']['fnc_php'] ?? null) !== 'validation_cf') {
                            continue;
                        }

                        $siblingValue = JobDynamicFieldProcessor::resolveFieldValueFromArray(
                            $sibling['groupPath'] ?? [],
                            $sibling['field']['name'],
                            $persistedValues
                        );

                        if ($siblingValue && strtoupper(trim($siblingValue)) === $value) {
                            return "Codice Fiscale duplicato nella stessa scheda";
                        }
                    }
                }
            }
        }

        // Univocità cross-cliente
        $query = \App\Models\CustomerModJob::query()->with('customer:id,cod,number,name,surname');
        if ($customerId) {
            $query->where('customer_id', '!=', $customerId);
        }

        $duplicate = $query->whereRaw("JSON_SEARCH(`values`, 'one', ?) IS NOT NULL", [$value])->first();
        if ($duplicate && $duplicate->customer) {
            $c = $duplicate->customer;
            return sprintf(
                "Codice Fiscale già presente in un'altra anagrafica: %s - %s - %s %s",
                $c->cod,
                $c->number,
                $c->name,
                $c->surname
            );
        }

        return false;
    }
}
