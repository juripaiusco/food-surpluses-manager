<?php

namespace App\Console\Commands;

use App\Models\JobSettings;
use Illuminate\Console\Command;

class ModJobsSettingsExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mod-jobs-settings:export {--id=* : ID delle righe da esportare (ripetibile). Omesso insieme a --all e --refresh richiede almeno un id} {--all : Esporta tutte le righe} {--refresh : Rilegge gli uuid già presenti nel file --out e ne aggiorna il contenuto con i dati correnti dal DB locale (utile dopo aver risincronizzato il DB locale da un dump di produzione, quando gli id locali cambiano)} {--out=database/data/mod_jobs_settings.json : Path del file JSON di output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esporta righe di mod_jobs_settings (report/sezioni) in un JSON versionabile in git, per sincronizzarle fra le installazioni delle varie sedi tramite mod-jobs-settings:import.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ids = $this->option('id');
        $all = (bool) $this->option('all');
        $refresh = (bool) $this->option('refresh');

        if ($refresh && ($all || !empty($ids))) {
            $this->error('--refresh non può essere combinato con --id o --all.');
            return self::FAILURE;
        }

        if (!$all && !$refresh && empty($ids)) {
            $this->error('Specificare almeno un --id=<n>, oppure --all, oppure --refresh.');
            return self::FAILURE;
        }

        $outPath = $this->option('out');
        $existing = [];
        if (file_exists($outPath)) {
            $existing = json_decode(file_get_contents($outPath), true) ?: [];
            $existing = collect($existing)->keyBy('uuid')->all();
        }

        if ($refresh) {
            if (empty($existing)) {
                $this->error("Il file {$outPath} non esiste o è vuoto: niente da aggiornare con --refresh.");
                return self::FAILURE;
            }

            $query = JobSettings::query()->whereIn('uuid', array_keys($existing))->orderBy('id');
        } else {
            $query = JobSettings::query()->orderBy('id');
            if (!$all) {
                $query->whereIn('id', $ids);
            }
        }

        $rows = $query->get();

        if ($rows->isEmpty()) {
            $this->error('Nessuna riga trovata per i criteri indicati.');
            return self::FAILURE;
        }

        $requestedUuids = $refresh ? array_keys($existing) : [];
        $foundUuids = [];

        $tableRows = [];
        foreach ($rows as $row) {
            if (empty($row->uuid)) {
                $row->save();
            }

            $existing[$row->uuid] = [
                'uuid' => $row->uuid,
                'type' => $row->type,
                'title' => $row->title,
                'description' => $row->description,
                'query' => $row->query,
                'schema' => $row->schema,
                'dynamic' => $row->dynamic,
            ];

            $tableRows[] = [$row->id, $row->uuid, $row->type, $row->title];
            $foundUuids[] = $row->uuid;
        }

        foreach (array_diff($requestedUuids, $foundUuids) as $missingUuid) {
            $this->warn("uuid non trovato nel DB locale, voce lasciata invariata nel file: {$missingUuid}");
        }

        if (!is_dir(dirname($outPath))) {
            mkdir(dirname($outPath), 0755, true);
        }

        file_put_contents(
            $outPath,
            json_encode(array_values($existing), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n"
        );

        $this->table(['id locale', 'uuid', 'type', 'title'], $tableRows);
        $this->info(count($rows) . " righe esportate in {$outPath} (totale righe nel file: " . count($existing) . ').');
        $this->comment('Ricordati di committare il file e lanciare mod-jobs-settings:import --apply nelle altre sedi.');

        return self::SUCCESS;
    }
}
