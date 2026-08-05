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
        $value = $request->input('customers_mod_jobs_values');

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
            return "Codice Fiscale non valido";
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

        // Univocità cross-cliente
        $query = \App\Models\CustomerModJob::query();
        if ($customerId) {
            $query->where('customer_id', '!=', $customerId);
        }

        $duplicate = $query->whereRaw("JSON_SEARCH(`values`, 'one', ?) IS NOT NULL", [$value])->exists();
        if ($duplicate) {
            return "Codice Fiscale già presente in un'altra anagrafica";
        }

        return false;
    }
}
