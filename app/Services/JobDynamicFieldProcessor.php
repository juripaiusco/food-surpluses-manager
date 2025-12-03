<?php

namespace App\Services;



use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use NigroSimone\CodiceFiscale;

class JobDynamicFieldProcessor
{
    /**
     * @param Request $request
     * @return false
     */
    public static function exe(Request $request)
    {
        $field_with_FncPhp_array = JobDynamicFieldProcessor::search_field_with_FncPhp(
            $request->input('customers_mod_jobs_schema')
        );

        foreach ($field_with_FncPhp_array as $section) {
            foreach ($section as $field) {

                $method = $field['field']['fnc_php'];

                if (method_exists(JobDynamicFieldProcessor::class, $method)) {

                    return call_user_func(
                        [JobDynamicFieldProcessor::class, $method],
                        $field,
                        $request
                    );

                }

            }
        }

        return false;
    }

    /**
     * Cerca ricorsivamente tutti i campi che contengono 'fnc_php'
     */
    public static function findFieldsWithFncPhp(array $fields, &$result = [], $path = [])
    {
        foreach ($fields as $key => $value) {
            $currentPath = array_merge($path, [$key]);

            // Se questo elemento ha la chiave fnc_php → lo aggiungo ai risultati
            if (is_array($value) && array_key_exists('fnc_php', $value)) {
                $result[] = [
                    'path' => $currentPath,
                    'field' => $value
                ];
            }

            // Se figlio è un array, continua la ricerca
            if (is_array($value)) {
                JobDynamicFieldProcessor::findFieldsWithFncPhp($value, $result, $currentPath);
            }
        }

        return $result;
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
     * @return bool
     *
     * Validazione del Codice Fiscale
     */
    public static function validation_cf($field, Request $request)
    {
        $path = $field['path'];
        $field = $field['field'];
        $value = $request->input('customers_mod_jobs_values')[$field['name']] ?? isset($request->input('customers_mod_jobs_values')[$field['name']]);

        if ($value) {

            $cf = new CodiceFiscale();

            if (!$cf->validaCodiceFiscale($value)) {
                return "Codice Fiscale non valido";
            }
        }

        return false;
    }
}
