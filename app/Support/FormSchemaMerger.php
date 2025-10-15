<?php

if (!function_exists('mergeFormSchema')) {
    function mergeFormSchema(array $modelSchema, array $clientSchema): array
    {
        $result = [];

        foreach ($modelSchema as $modelEl) {
            // Normal field (non-group)
            if (isset($modelEl['$formkit']) && $modelEl['$formkit'] !== 'group') {
                $clientEl = findByName($clientSchema, $modelEl['name']);

                if ($clientEl) {
                    $merged = array_merge_depth($clientEl, $modelEl);
                    $result[] = $merged;
                } else {
                    $result[] = $modelEl;
                }
            }

            // Group field (può avere istanze dinamiche)
            elseif (isset($modelEl['$formkit']) && $modelEl['$formkit'] === 'group') {
                // Trova tutti i gruppi cliente che corrispondono al prefisso del modello
                $clientGroups = findGroupsByPrefix($clientSchema, $modelEl['name']);

                if (!empty($clientGroups)) {
                    // Se il client ha istanze multiple, aggiorna ciascuna
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
                    // Non ci sono istanze cliente: aggiungi il gruppo base dal modello
                    $result[] = $modelEl;
                }
            }

            // Wrapper generico con children (es. div container)
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

        // Aggiungi i campi cliente "extra" che non sono stati trovati nel modello
        $extraFields = collect(flattenChildren($clientSchema))
            ->filter(fn($el) => isset($el['$formkit']) && !findByName($modelSchema, $el['name']))
            ->values()
            ->toArray();

        // Manteniamo ordine: modello prima, poi campi extra
        return array_merge($result, $extraFields);
    }

    // ========= Helpers ==========

    // Cerca ricorsivamente per name
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

    // Cerca un nodo con attrs.class
    function findByClass(array $schema, ?string $class): ?array
    {
        foreach ($schema as $el) {
            if (($el['attrs']['class'] ?? null) === $class) {
                return $el;
            }
            if (isset($el['children'])) {
                $found = findByClass($el['children'], $class);
                if ($found) return $found;
            }
        }
        return null;
    }

    // Appiattisce ricorsivamente children
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

    // Trova tutti i gruppi in $schema il cui name è il prefisso o è prefisso + separatore + suffisso
    // Esempi catturati: 'mod_jobs_bisprob', 'mod_jobs_bisprob_1', 'mod_jobs_bisprob-123', 'mod_jobs_bisprob-uuid'
    function findGroupsByPrefix(array $schema, string $prefix): array
    {
        $groups = [];

        // pattern: match base OR base followed by '_' or '-' or '.' then anything
        $pattern = '/^' . preg_quote($prefix, '/') . '($|[_\-.].+)/';

        foreach ($schema as $el) {
            if (($el['$formkit'] ?? null) === 'group' && isset($el['name'])) {
                if (preg_match($pattern, $el['name'])) {
                    $groups[] = $el;
                }
            }
            if (isset($el['children'])) {
                $groups = array_merge($groups, findGroupsByPrefix($el['children'], $prefix));
            }
        }

        // Se il client ha sia il gruppo base che le istanze numerate, mantieni l'ordine originale del client
        return $groups;
    }

    /**
     * Merge "profondo" con priorità: mantiene i valori cliente dove esistono
     * e aggiorna/sovrascrive con gli elementi del modello dove serve.
     * Nota: array_merge ricorsivo personalizzato semplice.
     */
    function array_merge_depth(array $a, array $b): array
    {
        $r = $a;
        foreach ($b as $k => $v) {
            if (is_array($v) && isset($r[$k]) && is_array($r[$k])) {
                $r[$k] = array_merge_depth($r[$k], $v);
            } else {
                // preferiamo il valore del modello per le chiavi del modello,
                // ma manteniamo le chiavi cliente che non sono nel modello
                $r[$k] = $v;
            }
        }
        return $r;
    }
}
