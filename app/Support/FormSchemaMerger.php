<?php

if (!function_exists('mergeFormSchema')) {
    function mergeFormSchema(array $modelSchema, array $clientSchema): array
    {
        $result = [];

        foreach ($modelSchema as $modelEl) {
            // Campo formKit singolo
            if (isset($modelEl['$formkit']) && $modelEl['$formkit'] !== 'group') {
                $clientEl = findByName($clientSchema, $modelEl['name']);

                if ($clientEl) {
                    $merged = array_merge($clientEl, $modelEl);
                    $merged['classes'] = array_merge(
                        $clientEl['classes'] ?? [],
                        $modelEl['classes'] ?? []
                    );
                    $result[] = $merged;
                } else {
                    $result[] = $modelEl;
                }
            }

            // Campo group (dinamico o no)
            elseif (isset($modelEl['$formkit']) && $modelEl['$formkit'] === 'group') {
                // Trova tutti i gruppi cliente che iniziano con il nome del modello
                $clientGroups = findGroupsByPrefix($clientSchema, $modelEl['name']);

                if ($clientGroups) {
                    // Aggiorna tutti i gruppi dinamici
                    foreach ($clientGroups as $group) {
                        $mergedChildren = mergeFormSchema(
                            $modelEl['children'] ?? [],
                            $group['children'] ?? []
                        );

                        $merged = array_merge($group, $modelEl);
                        $merged['children'] = $mergedChildren;
                        $result[] = $merged;
                    }
                } else {
                    // Nessun gruppo dinamico → aggiungo quello base
                    $result[] = $modelEl;
                }
            }

            // Wrapper generico con children
            elseif (isset($modelEl['children'])) {
                $clientEl = findByClass($clientSchema, $modelEl['attrs']['class'] ?? null);

                $mergedChildren = mergeFormSchema(
                    $modelEl['children'],
                    $clientEl['children'] ?? []
                );

                $merged = $modelEl;
                $merged['children'] = $mergedChildren;
                $result[] = $merged;
            } else {
                $result[] = $modelEl;
            }
        }

        // Campi extra del cliente non presenti nel modello
        $extraFields = collect(flattenChildren($clientSchema))
            ->filter(fn($el) => isset($el['$formkit']) && !findByName($modelSchema, $el['name']))
            ->values()
            ->toArray();

        return array_merge($result, $extraFields);
    }

    // === Helpers ===

    function findByName(array $schema, ?string $name): ?array
    {
        foreach ($schema as $el) {
            if (($el['$formkit'] ?? null) && ($el['name'] ?? null) === $name) {
                return $el;
            }
            if (isset($el['children'])) {
                $found = findByName($el['children'], $name);
                if ($found) return $found;
            }
        }
        return null;
    }

    function findByClass(array $schema, ?string $class): ?array
    {
        foreach ($schema as $el) {
            if (($el['attrs']['class'] ?? null) === $class) {
                return $el;
            }
        }
        return null;
    }

    function flattenChildren(array $schema): array
    {
        $flat = [];
        foreach ($schema as $el) {
            $flat[] = $el;
            if (isset($el['children'])) {
                $flat = array_merge($flat, flattenChildren($el['children']));
            }
        }
        return $flat;
    }

    // Trova tutti i gruppi cliente con prefisso nome (es. mod_jobs_bisprob, mod_jobs_bisprob_1, mod_jobs_bisprob_2)
    function findGroupsByPrefix(array $schema, string $prefix): array
    {
        $groups = [];
        foreach ($schema as $el) {
            if (($el['$formkit'] ?? null) === 'group' && str_starts_with($el['name'] ?? '', $prefix)) {
                $groups[] = $el;
            }
            if (isset($el['children'])) {
                $groups = array_merge($groups, findGroupsByPrefix($el['children'], $prefix));
            }
        }
        return $groups;
    }
}
