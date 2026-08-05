<?php

namespace App\Console\Commands;

use App\Models\CustomerModJob;
use App\Models\JobSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixModJobsDynamicGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mod-jobs:fix-dynamic-groups {--customer=} {--apply}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ripara i gruppi dinamici (es. componenti famiglia) di customers_mod_jobs.values la cui chiave base è rimasta un array vuoto "[]" invece di essere eliminata o rimpiazzata dal primo componente valido. Di default esegue solo un dry-run (nessuna scrittura); usare --apply per salvare.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apply = (bool) $this->option('apply');
        $customerId = $this->option('customer');

        $this->info($apply ? 'Modalità APPLY: le modifiche verranno salvate.' : 'Modalità DRY-RUN: nessuna modifica verrà salvata (usa --apply per applicare).');

        $dynamicBaseNames = $this->getDynamicBaseNames();

        $rows = [];
        $totalFamilies = 0;

        $query = CustomerModJob::query();
        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        $query->orderBy('id')->chunkById(100, function ($chunk) use (&$rows, &$totalFamilies, $apply, $dynamicBaseNames) {
            foreach ($chunk as $customerModJob) {
                $values = $customerModJob->values;

                if (!is_array($values) || empty($values)) {
                    continue;
                }

                [$newValues, $changes] = $this->repair($values, $dynamicBaseNames);

                if (empty($changes)) {
                    continue;
                }

                $totalFamilies += count($changes);

                foreach ($changes as $change) {
                    $rows[] = [
                        $customerModJob->customer_id,
                        $change['family'],
                        implode(', ', $change['before']),
                        implode(', ', $change['after']) ?: '(rimossa)',
                    ];
                }

                if ($apply) {
                    DB::transaction(function () use ($customerModJob, $newValues) {
                        $customerModJob->values = $newValues;
                        $customerModJob->save();
                    });
                }
            }
        });

        if (empty($rows)) {
            $this->info('Nessun record da correggere trovato.');
            return self::SUCCESS;
        }

        $this->table(['customer_id', 'gruppo', 'chiavi prima', 'chiavi dopo'], $rows);
        $this->info("Trovate {$totalFamilies} famiglie di campi dinamici da correggere.");

        if (!$apply) {
            $this->comment('Nessuna modifica salvata (dry-run). Rilanciare con --apply per applicare.');
        }

        return self::SUCCESS;
    }

    /**
     * Recupera i nomi base delle sezioni marcate come dinamiche/ripetibili
     * (mod_jobs_settings.dynamic = '1'), es. "mod_jobs_famiglia_comp".
     * Necessario per riconoscere anche il caso di una chiave base vuota "[]"
     * senza alcun sibling "_N": senza questa whitelist non c'è modo di distinguere
     * un gruppo dinamico svuotato da un qualunque altro campo che contenga "[]".
     */
    private function getDynamicBaseNames(): array
    {
        $names = [];

        foreach (JobSettings::where('dynamic', '1')->pluck('schema') as $schemaJson) {
            $schema = json_decode($schemaJson, true);
            if (isset($schema[0])) {
                $schema = $schema[0];
            }
            if (!empty($schema['name'])) {
                $names[] = $schema['name'];
            }
        }

        return $names;
    }

    /**
     * Individua le famiglie di campi dinamici (chiave base + chiavi "base_N") la cui
     * sequenza di chiavi non è già quella canonica (base, _1, _2, ...) — perché la
     * base manca del tutto, o è una lista vuota "[]", o c'è un buco in mezzo — e le
     * compatta rimuovendo le entry vuote/mancanti e rinumerando le restanti in ordine.
     *
     * @param array $dynamicBaseNames nomi base delle sezioni dinamiche note (da mod_jobs_settings)
     * @return array{0: array, 1: array} [nuovo array values, log delle modifiche]
     */
    private function repair(array $values, array $dynamicBaseNames = []): array
    {
        $keys = array_keys($values);
        $bases = [];

        // caso 1: base con almeno un sibling "_N" (rilevamento generico, robusto
        // anche se mod_jobs_settings non è più allineato/disponibile).
        // Esclude candidati che terminano essi stessi con "_N": una vera chiave base
        // non ha mai quel suffisso, evita di scambiare per base una chiave già
        // doppiamente suffissata da una corruzione precedente (es. "..._1_2").
        foreach ($keys as $key) {
            if (preg_match('/^(.+)_(\d+)$/', $key, $m)) {
                $base = $m[1];
                if (!preg_match('/_\d+$/', $base) && array_key_exists($base, $values)) {
                    $bases[$base] = true;
                }
            }
        }

        // caso 2: base nota come dinamica (whitelist mod_jobs_settings.dynamic),
        // anche quando la chiave base non esiste affatto e resta solo qualche
        // sibling "_N" (es. eliminata manualmente/da una UI corrotta) — qui ci si
        // può fidare della whitelist per evitare falsi positivi su campi non
        // correlati che terminano casualmente con "_N".
        foreach ($dynamicBaseNames as $base) {
            if (array_key_exists($base, $values)) {
                $bases[$base] = true;
                continue;
            }
            foreach ($keys as $key) {
                if (preg_match('/^' . preg_quote($base, '/') . '_\d+$/', $key)) {
                    $bases[$base] = true;
                    break;
                }
            }
        }

        $changes = [];

        foreach (array_keys($bases) as $base) {
            // ordina le entry della famiglia: base (indice -1, solo se la chiave esiste
            // davvero), poi _1, _2, ... in ordine numerico (solo chiavi ancora presenti,
            // per sicurezza nel caso una famiglia precedente ne abbia già consumata una)
            $entries = [];
            if (array_key_exists($base, $values)) {
                $entries[] = ['key' => $base, 'index' => -1, 'value' => $values[$base]];
            }
            foreach ($keys as $key) {
                if (preg_match('/^' . preg_quote($base, '/') . '_(\d+)$/', $key, $m) && array_key_exists($key, $values)) {
                    $entries[] = ['key' => $key, 'index' => (int) $m[1], 'value' => $values[$key]];
                }
            }

            if (empty($entries)) {
                continue;
            }

            usort($entries, fn ($a, $b) => $a['index'] <=> $b['index']);

            $before = array_column($entries, 'key');

            $populated = array_values(array_filter($entries, function ($entry) {
                return !(is_array($entry['value']) && array_is_list($entry['value']));
            }));

            $after = [];
            foreach ($populated as $i => $entry) {
                $after[] = $i === 0 ? $base : "{$base}_{$i}";
            }

            // niente da fare: la sequenza è già quella canonica (base, _1, _2, ...)
            if ($before === $after) {
                continue;
            }

            // rimuove tutte le chiavi originali della famiglia dal risultato
            foreach ($entries as $entry) {
                unset($values[$entry['key']]);
            }

            foreach ($populated as $i => $entry) {
                $values[$after[$i]] = $entry['value'];
            }

            $changes[] = [
                'family' => $base,
                'before' => $before,
                'after' => $after,
            ];
        }

        return [$values, $changes];
    }
}
