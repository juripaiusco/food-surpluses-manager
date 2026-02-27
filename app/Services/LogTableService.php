<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LogTableService
{
    public static function make(
        Collection|array $data,
        array $columns,
        string $level = 'info',
        ?string $title = null
    ): string
    {

        if ($data instanceof Collection) {
            $data = $data->values();
        } else {
            $data = collect($data);
        }

        if ($data->isEmpty()) {
            Log::$level('Tabella vuota');
            return '';
        }

        // Determina se $columns è associativo (con label personalizzate)
        $isAssociative = array_keys($columns) !== range(0, count($columns) - 1);

        $fields = [];
        $headers = [];

        if ($isAssociative) {
            foreach ($columns as $field => $label) {
                $fields[] = $field;
                $headers[] = $label;
            }
        } else {
            $fields = $columns;
            $headers = $columns;
        }

        // Calcolo larghezza colonne
        $widths = [];

        foreach ($fields as $i => $field) {
            $widths[$i] = strlen($headers[$i]);

            foreach ($data as $row) {

                if (is_object($row)) {
                    $value = $row->getAttribute($field);
                } else {
                    $value = $row[$field] ?? '';
                }

                $value = (string) $value;

                $widths[$i] = max($widths[$i], strlen($value));
            }
        }

        // Separatore
        $line = '+';
        foreach ($widths as $width) {
            $line .= str_repeat('-', $width + 2) . '+';
        }

        $output = '';

        if ($title) {
            $output .= strtoupper($title) . PHP_EOL;
        }

        $output .= $line . PHP_EOL . '|';

        // Header
        foreach ($headers as $i => $header) {
            $output .= ' ' . str_pad($header, $widths[$i]) . ' |';
        }

        $output .= PHP_EOL . $line . PHP_EOL;

        // Righe
        foreach ($data as $row) {

            $output .= '|';

            foreach ($fields as $i => $field) {

                if (is_object($row)) {
                    $value = $row->getAttribute($field);
                } else {
                    $value = $row[$field] ?? '';
                }

                $value = (string) $value;

                $output .= ' ' . str_pad($value, $widths[$i]) . ' |';
            }

            $output .= PHP_EOL;
        }

        $output .= $line;

//        Log::$level($output);
        return $output;
    }
}
