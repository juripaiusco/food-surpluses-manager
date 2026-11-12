<?php

namespace App\Console\Commands;

use App\Models\CustomerModJob;
use App\Services\CustomerModJobService;
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
    protected $description = 'Ripara i gruppi dinamici (es. componenti famiglia) di customers_mod_jobs.values: chiave base rimasta "[]", buchi nella numerazione, e chiavi legacy a suffisso multiplo (es. mod_jobs_famiglia_comp_2_1) riportate negli slot canonici (base, _1, _2, ...). Le istanze identiche a una già presente vengono scartate come duplicati. Stampa solo nomi chiave, mai valori. Di default esegue solo un dry-run (nessuna scrittura); usare --apply per salvare.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apply = (bool) $this->option('apply');
        $customerId = $this->option('customer');

        $this->info($apply ? 'Modalità APPLY: le modifiche verranno salvate.' : 'Modalità DRY-RUN: nessuna modifica verrà salvata (usa --apply per applicare).');

        $dynamicBaseNames = CustomerModJobService::dynamicBaseNames();

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

                [$newValues, $changes] = CustomerModJobService::repairDynamicGroups($values, $dynamicBaseNames);

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
                        implode(', ', $change['dropped']) ?: '-',
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

        $this->table(['customer_id', 'gruppo', 'chiavi prima', 'chiavi dopo', 'scartati (vuoti/duplicati)'], $rows);
        $this->info("Trovate {$totalFamilies} famiglie di campi dinamici da correggere.");

        if (!$apply) {
            $this->comment('Nessuna modifica salvata (dry-run). Rilanciare con --apply per applicare.');
        }

        return self::SUCCESS;
    }
}
