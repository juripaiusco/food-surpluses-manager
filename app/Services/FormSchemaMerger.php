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
            // 1️⃣ Se è un gruppo (campo dinamico)
            if (isset($modelField['$formkit']) && $modelField['$formkit'] === 'group') {
                $existingGroup = collect($merged)->firstWhere('name', $modelField['name']);

                if ($existingGroup) {
                    // Merge ricorsivo dei children
                    $existingGroup['children'] = self::deepMerge(
                        $modelField['children'] ?? [],
                        $existingGroup['children'] ?? []
                    );

                    // Aggiorna nel risultato finale
                    foreach ($merged as $k => $field) {
                        if (isset($field['name']) && $field['name'] === $modelField['name']) {
                            $merged[$k] = array_merge($modelField, $existingGroup);
                            break;
                        }
                    }
                } else {
                    $merged[] = $modelField;
                }

                continue;
            }

            // 2️⃣ Se è un wrapper (es. div.row con children)
            if (!isset($modelField['$formkit']) && isset($modelField['children'])) {
                $existingWrapper = collect($merged)->first(function ($f) use ($modelField) {
                    return !isset($f['$formkit']) &&
                        isset($f['attrs']['class'], $modelField['attrs']['class']) &&
                        $f['attrs']['class'] === $modelField['attrs']['class'];
                });

                if ($existingWrapper) {
                    foreach ($merged as $k => $field) {
                        if (
                            !isset($field['$formkit']) &&
                            isset($field['attrs']['class'], $modelField['attrs']['class']) &&
                            $field['attrs']['class'] === $modelField['attrs']['class']
                        ) {
                            $merged[$k]['children'] = self::deepMerge(
                                $modelField['children'] ?? [],
                                $field['children'] ?? []
                            );
                            break;
                        }
                    }
                } else {
                    $merged[] = $modelField;
                }

                continue;
            }

            // 3️⃣ Campi normali
            if (isset($modelField['name'])) {
                $exists = collect($merged)->contains(fn($f) => ($f['name'] ?? null) === $modelField['name']);

                if (!$exists) {
                    $merged[] = $modelField;
                }
            }
        }

        // Evita duplicati esatti
        $merged = collect($merged)->unique(function ($item) {
            return $item['name'] ?? json_encode($item);
        })->values()->toArray();

        return $merged;
    }
}
