<?php

if (!function_exists('mergeFormSchema')) {
    function mergeFormSchema(array $modelSchema, array $clientSchema): array
    {
        $result = [];

        foreach ($modelSchema as $modelEl) {
            // Caso: campo formKit
            if (isset($modelEl['$formkit'])) {
                $clientEl = findByName($clientSchema, $modelEl['name']);

                if ($clientEl) {
                    // Merge intelligente
                    $merged = array_merge($clientEl, $modelEl);
                    $merged['classes'] = array_merge(
                        $clientEl['classes'] ?? [],
                        $modelEl['classes'] ?? []
                    );

                    $result[] = $merged;
                } else {
                    // Campo nuovo: aggiungilo
                    $result[] = $modelEl;
                }
            }

            // Caso: wrapper (es. div con children)
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
                // Nodo generico
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

    // Helpers
    function findByName(array $schema, string $name = null): ?array
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
}
