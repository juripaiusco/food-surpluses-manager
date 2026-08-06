<?php

namespace App\Console\Commands;

use App\Models\JobSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ModJobsSettingsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mod-jobs-settings:import {--file=database/data/mod_jobs_settings.json : Path del file JSON prodotto da mod-jobs-settings:export} {--apply : Applica le modifiche (default: dry-run)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa righe di mod_jobs_settings (report/sezioni) da un JSON esportato con mod-jobs-settings:export. Upsert per uuid: crea la riga se assente, aggiorna i campi se già presente. Di default esegue solo un dry-run; usare --apply per salvare.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apply = (bool) $this->option('apply');
        $filePath = $this->option('file');

        if (!file_exists($filePath)) {
            $this->error("File non trovato: {$filePath}");
            return self::FAILURE;
        }

        $entries = json_decode(file_get_contents($filePath), true);
        if (!is_array($entries)) {
            $this->error("File JSON non valido: {$filePath}");
            return self::FAILURE;
        }

        $this->info($apply ? 'Modalità APPLY: le modifiche verranno salvate.' : 'Modalità DRY-RUN: nessuna modifica verrà salvata (usa --apply per applicare).');

        $rows = [];
        $created = 0;
        $updated = 0;

        foreach ($entries as $entry) {
            if (empty($entry['uuid'])) {
                $this->warn('Riga senza uuid ignorata: ' . ($entry['title'] ?? '(senza titolo)'));
                continue;
            }

            $existing = JobSettings::where('uuid', $entry['uuid'])->first();
            $action = $existing ? 'update' : 'create';

            if ($apply) {
                DB::transaction(function () use ($existing, $entry) {
                    $data = $existing ?: new JobSettings();
                    $data->fill([
                        'type' => $entry['type'] ?? null,
                        'title' => $entry['title'] ?? null,
                        'description' => $entry['description'] ?? null,
                        'query' => $entry['query'] ?? null,
                        'schema' => $entry['schema'] ?? null,
                        'dynamic' => $entry['dynamic'] ?? null,
                        'uuid' => $entry['uuid'],
                    ]);
                    $data->save();
                });
            }

            $action === 'create' ? $created++ : $updated++;
            $rows[] = [$action, $entry['uuid'], $entry['type'] ?? '', $entry['title'] ?? ''];
        }

        if (empty($rows)) {
            $this->info('Nessuna riga valida trovata nel file.');
            return self::SUCCESS;
        }

        $this->table(['azione', 'uuid', 'type', 'title'], $rows);
        $this->info("Da creare: {$created}, da aggiornare: {$updated}.");

        if (!$apply) {
            $this->comment('Nessuna modifica salvata (dry-run). Rilanciare con --apply per applicare.');
        }

        return self::SUCCESS;
    }
}
