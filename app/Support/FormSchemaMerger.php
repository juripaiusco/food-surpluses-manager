<?php

function mergeFormSchema(array $modelSchema, array $clientSchema): array
{
    $merged = $clientSchema;

    foreach ($modelSchema as $mGroup) {
        if (($mGroup['$formkit'] ?? '') !== 'group') continue;

        $groupName = $mGroup['name'] ?? null;
        if (!$groupName) continue;

        // Trova tutti i gruppi cliente che corrispondono (anche con suffissi _1, _2, ecc.)
        $matchingGroups = array_filter($clientSchema, function ($cGroup) use ($groupName) {
            return isset($cGroup['name']) && str_starts_with($cGroup['name'], $groupName);
        });

        // Se il cliente non ha ancora nessun gruppo simile → aggiungiamo quello base del modello
        if (empty($matchingGroups)) {
            $merged[] = $mGroup;
            continue;
        }

        // Altrimenti mergiamo ogni gruppo cliente simile con il modello
        foreach ($merged as &$cGroup) {
            if (
                isset($cGroup['$formkit']) &&
                $cGroup['$formkit'] === 'group' &&
                isset($cGroup['name']) &&
                str_starts_with($cGroup['name'], $groupName)
            ) {
                $cGroup = mergeSingleGroup($mGroup, $cGroup);
            }
        }
    }

    return $merged;
}

function mergeSingleGroup(array $modelGroup, array $clientGroup): array
{
    $merged = $clientGroup;

    // Prende i figli reali del modello e del cliente
    $modelChildren = flattenChildren($modelGroup['children'] ?? []);
    $clientChildren = flattenChildren($clientGroup['children'] ?? []);

    // Verifica che esista almeno un div.row
    if (!isset($merged['children'][0]['children'])) {
        $merged['children'][0]['children'] = [];
    }

    foreach ($modelChildren as $mChild) {
        if (!isset($mChild['name'])) continue;

        $exists = findByName($clientChildren, $mChild['name']);

        // Aggiungi solo se non esiste già
        if (!$exists) {
            $merged['children'][0]['children'][] = $mChild;
        }
    }

    // Rimuove eventuali duplicati per nome
    $merged['children'][0]['children'] = collect($merged['children'][0]['children'])
        ->unique('name')
        ->values()
        ->toArray();

    return $merged;
}

// --- funzioni helper ---

function flattenChildren(array $children): array
{
    $result = [];
    foreach ($children as $child) {
        if (isset($child['$formkit'])) {
            $result[] = $child;
        }
        if (isset($child['children']) && is_array($child['children'])) {
            $result = array_merge($result, flattenChildren($child['children']));
        }
    }
    return $result;
}

function findByName(array $elements, string $name): ?array
{
    foreach ($elements as $el) {
        if (($el['name'] ?? null) === $name) {
            return $el;
        }
    }
    return null;
}
