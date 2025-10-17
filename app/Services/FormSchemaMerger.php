<?php

namespace App\Services;

class FormSchemaMerger
{
    /**
     * Esegue il merge tra lo schema del modello e quello del cliente.
     *
     * @param array|string $modelSchema
     * @param array|string|null $customerSchema
     * @return array
     */
    public static function merge($modelSchema, $customerSchema): array
    {
        // Se arrivano stringhe JSON, convertili in array
        $model = is_string($modelSchema) ? json_decode($modelSchema, true) : $modelSchema;
        $customer = is_string($customerSchema) ? json_decode($customerSchema, true) : $customerSchema;

        if (!is_array($model)) $model = [];
        if (!is_array($customer)) $customer = [];

        // Esegui merge ricorsivo
        return self::deepMerge($model, $customer);
    }

    /**
     * Merge ricorsivo tra due schemi di form.
     * - Mette insieme i campi in base al "name"
     * - Se sono group, mergia anche i children
     * - Aggiorna label e metadati base
     * - Evita duplicati
     *
     * @param array $model
     * @param array $customer
     * @return array
     */
    protected static function deepMerge(array $model, array $customer): array
    {
        $merged = $customer;

        foreach ($model as $modelField) {
            // 1️⃣ Se è un gruppo dinamico ($formkit = group)
            if (isset($modelField['$formkit']) && $modelField['$formkit'] === 'group') {
                $existingGroup = collect($merged)->firstWhere('name', $modelField['name']);

                if ($existingGroup) {
                    // Merge dei children
                    $existingGroup['children'] = self::deepMerge(
                        $modelField['children'] ?? [],
                        $existingGroup['children'] ?? []
                    );

                    // Aggiorna metadati di gruppo
                    $merged = self::replaceField($merged, $modelField['name'], array_merge($modelField, $existingGroup));
                } else {
                    $merged[] = $modelField;
                }

                continue;
            }

            // 2️⃣ Wrapper tipo <div class="row"> con children
            if (!isset($modelField['$formkit']) && isset($modelField['children'])) {
                $existingWrapper = collect($merged)->first(function ($f) use ($modelField) {
                    return !isset($f['$formkit']) &&
                        isset($f['attrs']['class'], $modelField['attrs']['class']) &&
                        $f['attrs']['class'] === $modelField['attrs']['class'];
                });

                if ($existingWrapper) {
                    $existingWrapper['children'] = self::deepMerge(
                        $modelField['children'] ?? [],
                        $existingWrapper['children'] ?? []
                    );
                    $merged = self::replaceWrapper($merged, $modelField, $existingWrapper);
                } else {
                    $merged[] = $modelField;
                }

                continue;
            }

            // 3️⃣ Campo normale
            if (isset($modelField['name'])) {
                $existingFieldIndex = collect($merged)->search(fn($f) => ($f['name'] ?? null) === $modelField['name']);

                if ($existingFieldIndex !== false) {
                    // Aggiorna i metadati del campo
                    $merged[$existingFieldIndex] = self::updateFieldAttributes($merged[$existingFieldIndex], $modelField);
                } else {
                    $merged[] = $modelField;
                }
            }
        }

        // Evita duplicati
        $merged = collect($merged)->unique(function ($item) {
            return $item['name'] ?? json_encode($item);
        })->values()->toArray();

        return $merged;
    }

    /**
     * Aggiorna gli attributi descrittivi di un campo (label, placeholder, classi, ecc.)
     */
    protected static function updateFieldAttributes(array $customerField, array $modelField): array
    {
        $keysToUpdate = ['label', 'placeholder', 'outerClass', 'help', 'hint', 'validation'];

        foreach ($keysToUpdate as $key) {
            if (isset($modelField[$key])) {
                $customerField[$key] = $modelField[$key];
            }
        }

        // Aggiorna anche le classi e i config se presenti nel modello
        if (isset($modelField['classes'])) {
            $customerField['classes'] = array_merge($customerField['classes'] ?? [], $modelField['classes']);
        }

        if (isset($modelField['config'])) {
            $customerField['config'] = array_merge($customerField['config'] ?? [], $modelField['config']);
        }

        return $customerField;
    }

    /**
     * Sostituisce un campo esistente in base al name
     */
    protected static function replaceField(array $merged, string $name, array $newField): array
    {
        foreach ($merged as $i => $f) {
            if (($f['name'] ?? null) === $name) {
                $merged[$i] = $newField;
                break;
            }
        }
        return $merged;
    }

    /**
     * Sostituisce un wrapper (div.row)
     */
    protected static function replaceWrapper(array $merged, array $modelField, array $newField): array
    {
        foreach ($merged as $i => $f) {
            if (
                !isset($f['$formkit']) &&
                isset($f['attrs']['class'], $modelField['attrs']['class']) &&
                $f['attrs']['class'] === $modelField['attrs']['class']
            ) {
                $merged[$i] = $newField;
                break;
            }
        }
        return $merged;
    }
}
