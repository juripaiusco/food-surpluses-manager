<?php

namespace App\Http\Controllers;

use App\Models\CustomerModJob;
use App\Services\CustomerModJobService;
use App\Services\JobDynamicFieldProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use function PHPUnit\Framework\isNull;

class Job extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->import_excel();

        $request_validate_array = [
            'cod',
            'number',
            'customer_name',
            'name',
            'surname',
            'address',
            'family_number',
            'points',
            'phone',
        ];

        // Query data
        $data = \App\Models\Customer::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:' . implode(',', $request_validate_array)],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $data->where(function ($q) use ($request_validate_array) {

                foreach ($request_validate_array as $field) {
                    if ($field != 'customer_name') {
                        $q->orWhere($field, 'like', '%' . request('s') . '%');
                    }
                }

            });
        }

        // Filtro RICERCA Numero Assistito
        if (request('number')) {
            $data->where('number', request('number'));
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->with('order');

        if (request('filters') == 'no-order-3-months') {

            $data = $data->leftJoin(
                'orders', 'customers.id', '=', 'orders.customer_id'
            );

            $data = $data->select([
                'customers.id',
                'customers.active',
                'customers.view_reception',
                'customers.cod',
                'customers.number',
                'customers.name',
                'customers.surname',
                'customers.name_delegato',
                'customers.address',
                'customers.city',
                'customers.provincia',
                'customers.phone',
                'customers.family_number',
                'customers.points',
                'customers.points_renew',
                'customers.note',
                'customers.note_alert',
                'customers.b1',
                'customers.b2',
                'customers.b3',
                'customers.char1',
                'customers.char2',
                'customers.char3',
                'customers.c_group',
                'customers.channel',
                'customers.created_at',
                'customers.updated_at',
                'orders.reference AS order_reference',
                'orders.date AS order_date',
            ]);

            $data = $data->addSelect(DB::raw('
                CONCAT(surname, \' \', name) AS customer_name
            '));

            $data = $data->where(
                'orders.date',
                '>=',
                date('Y-m-d 00:00:00', strtotime('-3 months'))
            );

            $data = $data->groupBy('customers.id');

            $array_id_not = array();
            foreach ($data->get() as $d) {
                $array_id_not[] = $d->id;
            }

            $data = \App\Models\Customer::query();
            $data = $data->whereNotIn('id', $array_id_not);
            $data = $data->select();

            // Filtro ORDINAMENTO
            if (request('orderby') && request('ordertype')) {
                $data->orderby(request('orderby'), strtoupper(request('ordertype')));
            }

        } else {

            $data = $data->select();
        }

        $data = $data->addSelect(DB::raw('
            CONCAT(surname, \' \', name) AS customer_name
        '));
        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return Inertia::render('Jobs/List', [
            'data' => $data,
            'filters' => request()->all(['number', 's', 'orderby', 'ordertype', 'filters'])
        ]);
    }

    public function import_excel()
    {
        $import_data = true;

        $path = Storage::disk('public')->path('assistiti.xlsx');

        if (file_exists($path)) {

            $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $path);

            /*
                  21 => "ALLEGATO    1"
                  22 => "HUB INCLUSIONE"
                  23 => "PRIVACY EMPORIO"
                  24 => "ASSOCIAZIONE"
                  25 => "NOTE"
                  26 => "GIORNO"
                  27 => "MESE"
                  28 => "ANNO (fiscale)"
                  29 => "ANNO NASCITA CORRETTO"
                  30 => "DATA"
                  31 => "ETA'"
             */
            $array_field = [
                null,
                [
                    'field' => 'mod_jobs_gruppo',
                    'pre' => 'Gruppo '
                ], [
                    'field' => 'number',
                    'type' => 'database',
                ], [
                    'field' => 'active',
                    'type' => 'database',
                ], [
                    'field' => 'cognome_nome',
                    'type' => 'database',
                ], [
                    'field' => 'mod_jobs_anagrafica_cf',
                ], [
                    'field' => 'mod_jobs_anagrafica_assistente_sociale_tel',
                ], [
                    'field' => 'mod_jobs_anagrafica_domicilio_comune',
                ], [
                    'field' => 'family_number',
                    'type' => 'database',
                ],
                null,
                [
                    'field' => 'cod',
                    'type' => 'database',
                ],
                null,
                null,
                null,
                [
                    'field' => 'mod_jobs_altri_note_personali_note',
                    'father' => 'mod_jobs_altri_note_personali'
                ], [
                    'field' => 'mod_jobs_isee_allegato_15',
                    'father' => 'mod_jobs_isee',
                    'field_type' => 'bool'
                ],
                null,
                null,
                null,
                null,
                null,
                [
                    'field' => 'mod_jobs_doc_allegato1',
                    'field_type' => 'bool'
                ],
                null,
                [
                    'field' => 'mod_jobs_doc_privacy',
                    'field_type' => 'bool'
                ]
            ];

            /*
             {
                "mod_jobs_isee":[],
                "mod_jobs_famiglia_comp":[],
                "mod_jobs_altri_note_personali":{
                    "mod_jobs_altri_note_personali_note":"test"
                },
                "mod_jobs_altri_note_personali_1":{
                    "mod_jobs_altri_note_personali_note":"test2"
                }
                "mod_jobs_altri_segnalazioni":[]
             }
             */

            $c = 0;
            $array_values = [];

            // Predispongo il dato
            foreach (array_slice($data[0], 1) as $d) {

                foreach ($array_field as $k => $field_options) {

                    if (isset($d[$k]) && isset($field_options)) {

                        if (!isset($field_options['type'])) {

                            $array_values[$c]['json'][$field_options['field']] = $d[$k];

                            if (isset($field_options['field_type']) && $field_options['field_type'] == 'bool' && $d[$k] == 'SI') {
                                $array_values[$c]['json'][$field_options['field']] = 'yes';
                            }

                            if (isset($field_options['pre'])) {
                                $array_values[$c]['json'][$field_options['field']] = $field_options['pre'] . $d[$k];
                            }

                            if (isset($field_options['father'])) {
                                unset($array_values[$c]['json'][$field_options['field']]);
                                $array_values[$c]['json'][$field_options['father']][$field_options['field']] = $d[$k];

                                if (isset($field_options['field_type']) && $field_options['field_type'] == 'bool' && $d[$k] == 'SI') {
                                    $array_values[$c]['json'][$field_options['father']][$field_options['field']] = 'yes';
                                }
                            }

                        } else if ($field_options['type'] == 'database') {

                            $array_values[$c]['db'][$field_options['field']] = $d[$k];
                        }
                    }
                }

                $c++;
            }

            foreach ($array_values as $k => $v) {

                // Inserimento capifamiglia
                if (isset($v['db']['cod']) &&
                    isset($v['db']['number'])) {

                    $customer = \App\Models\Customer::query();
                    $customer = $customer->where('cod', $v['db']['cod']);
                    $customer = $customer->where('number', $v['db']['number']);
                    $customer = $customer->first();

                    if ($customer) {

                        $customer_mod_jobs = CustomerModJob::query()
                            ->where('customer_id', $customer->id)
                            ->first();

                        if (!$customer_mod_jobs) {
                            $customer_mod_jobs = new CustomerModJob();
                            $customer_mod_jobs->customer_id = $customer->id;

                            $customer_mod_jobs->values = $v['json'];

                            if ($import_data) {

                                $customer_mod_jobs->save();
                            }
                        }
                    }

                }

                // Inserimento componenti
                if ((isset($v['db']['cognome_nome']) && $v['db']['cognome_nome'] != '') &&
                    !isset($v['db']['cod']) &&
                    isset($v['db']['number'])) {

                    $customer = \App\Models\Customer::query();
                    $customer = $customer->where('number', $v['db']['number']);
                    $customer = $customer->first();

                    if ($customer) {

                        $customer_mod_jobs = CustomerModJob::query()
                            ->where('customer_id', $customer->id)
                            ->first();

                        if (!$customer_mod_jobs) {
                            $customer_mod_jobs = new CustomerModJob();
                            $customer_mod_jobs->customer_id = $customer->id;
                        }

                        // Separazione Nome e Cognome
                        try {
                            $array_cognome_nome = explode(' ', $v['db']['cognome_nome']);

                        } catch (\Exception $e) {
                            dd($v['db']);
                            echo $e->getMessage();
                        }

                        $nome = implode(array_splice($array_cognome_nome, -1));
//                        $nome = $array_cognome_nome[1];
                        $cognome = end($array_cognome_nome);
//                        $cognome = $array_cognome_nome[0];

                        /*if (count($array_cognome_nome) == 1 && $customer->id == 573) {
//                            dd($v['db']['cognome_nome']);
                            $cognome = $array_cognome_nome[count($array_cognome_nome) - 2];
                        }*/

                        // Prendo i valori e aggiungo il componente famiglia
                        $json_values = $customer_mod_jobs->values;


                        // - - - - -
                        if ($json_values) {

                            $baseKey = 'mod_jobs_famiglia_comp';

                            if (!array_key_exists($baseKey, $json_values)) {

                                $json_values[$baseKey] = [
                                    'mod_jobs_famiglia_comp_nome' => $nome,
                                    'mod_jobs_famiglia_comp_cognome' => $cognome,
                                    'mod_jobs_famiglia_comp_cf' => $v['json']['mod_jobs_anagrafica_cf'] ?? '',
                                ];

                            } else {

                                $maxIndex = 0;

                                foreach ($json_values as $key => $value) {
                                    if (preg_match('/^' . $baseKey . '_(\d+)$/', $key, $matches)) {
                                        $index = (int) $matches[1];
                                        if ($index > $maxIndex) {
                                            $maxIndex = $index;
                                        }
                                    }
                                }

                                $nextIndex = $maxIndex + 1;

                                $json_values["{$baseKey}_{$nextIndex}"] = [
                                    'mod_jobs_famiglia_comp_nome' => $nome,
                                    'mod_jobs_famiglia_comp_cognome' => $cognome,
                                    'mod_jobs_famiglia_comp_cf' => $v['json']['mod_jobs_anagrafica_cf'] ?? '',
                                ];
                            }
                        }
                        // - - - - -

                        $customer_mod_jobs->values = $json_values;

                        if ($import_data) {
                            $customer_mod_jobs->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Creo un oggetto di dati vuoto
        $columns = Schema::getColumnListing('customers');

        $customers_array = array();
        foreach ($columns as $customers_field) {
            $customers_array[$customers_field] = null;
        }

        unset($customers_array['id']);
        unset($customers_array['deleted_at']);
        unset($customers_array['created_at']);
        unset($customers_array['updated_at']);

        $customers_array['number'] = \App\Models\Customer::nextSuggestedNumber();
        $customers_array['saveRedirect'] = Redirect::back()->getTargetUrl();

        $job_settings = CustomerModJobService::sectionsForModule('jobs_listen');
        $customers_array['customers_mod_jobs_schema'] = $job_settings;
        $customers_array['customers_mod_jobs_values'] = [];

        $data = json_decode(json_encode($customers_array), true);

        return Inertia::render('Jobs/Form', [
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'number'        => ['required', 'unique:customers'],
            'cod'           => ['required', 'unique:customers'],
            'name'          => ['required'],
            'surname'       => ['required'],
            'family_number' => ['required'],
            'points'        => ['required'],
            'points_renew'  => ['required'],
        ]);

        $jobError = JobDynamicFieldProcessor::exe($request);
        if ($jobError) {
            $request->session()->flash('flash.error', $jobError);
            return to_route('jobs_listen.create');
        }

        $saveRedirect = $request['saveRedirect'];

        $customers_mod_jobs_schema = $request['customers_mod_jobs_schema'];
        $customers_mod_jobs_values = CustomerModJobService::mergeModuleValues(
            [],
            $customers_mod_jobs_schema,
            $request['customers_mod_jobs_values'] ?? []
        );

        unset($request['saveRedirect']);
        unset($request['customers_mod_jobs_schema']);
        unset($request['customers_mod_jobs_values']);

        $customer = new \App\Models\Customer();

        $customer->fill($request->except([
            'order',
            'created_at',
            'updated_at',
            'saveRedirect',
            'redirect'
        ]));

        $customer->save();

        $customer_mod_jobs = new CustomerModJob();
        $customer_mod_jobs->customer_id = $customer->id;
        $customer_mod_jobs->schema = $customers_mod_jobs_schema;
        $customer_mod_jobs->values = $customers_mod_jobs_values;
        $customer_mod_jobs->save();

        if ($request['redirect']) {

            if (to_route('jobs_listen.edit', $customer->id)->getTargetUrl() == $saveRedirect) {
                return Inertia::location(to_route('jobs_listen.index')->getTargetUrl());
            }

            return Redirect::to($saveRedirect);

        } else {

            return Inertia::location(to_route('jobs_listen.edit', [
                'id' => $customer->id,
                'saveRedirectURL' => $saveRedirect
            ])->getTargetUrl());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $data = \App\Models\Customer::with(['order' => function ($q) {
            $q->select([
                'id',
                'reference',
                'user_id',
                'customer_id',
                'retail_id',
                'points',
                'date',
            ]);
        }])->find($id);

        $data->saveRedirect = Redirect::back()->getTargetUrl();

        if ($request['saveRedirectURL']) {
            $data->saveRedirect = $request['saveRedirectURL'];
        }

        $job_settings = CustomerModJobService::sectionsForModule('jobs_listen');
        $mod_jobs_schema_model = json_decode(json_encode($job_settings), true);

        $customer_mod_jobs = CustomerModJob::query()
            ->where('customer_id', $id)
            ->first();

        $hydrated = CustomerModJobService::hydrateEdit(
            $mod_jobs_schema_model,
            optional($customer_mod_jobs)->values
        );
        $data->customers_mod_jobs_schema = $hydrated['schema'];
        $data->customers_mod_jobs_values = $hydrated['values'];

        return Inertia::render('Jobs/Form', [
            'data' => $data,
            'error' => request()->session()->get('flash.error')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'number'        => ['required', 'unique:customers,number,' . $id],
            'cod'           => ['required', 'unique:customers,cod,' . $id],
            'name'          => ['required'],
            'surname'       => ['required'],
            'family_number' => ['required'],
            'points'        => ['required'],
            'points_renew'  => ['required'],
        ]);

        $jobError = JobDynamicFieldProcessor::exe($request, (int) $id);
        if ($jobError) {
            $request->session()->flash('flash.error', $jobError);
            return to_route('jobs_listen.edit', ['id' => $id]);
        }

        $saveRedirect = $request['saveRedirect'];

        /*unset($request['order']);
        unset($request['created_at']);
        unset($request['updated_at']);
        unset($request['saveRedirect']);*/

        $customer_mod_jobs = CustomerModJob::query()->where('customer_id', $id)->first();
        $existingValues = optional($customer_mod_jobs)->values ?? [];

        if ($customer_mod_jobs == null) {
            $customer_mod_jobs = new CustomerModJob();
        }

        $customer_mod_jobs->customer_id = $id;
        $customer_mod_jobs->schema = $request['customers_mod_jobs_schema'];
        $customer_mod_jobs->values = CustomerModJobService::mergeModuleValues(
            $existingValues,
            $request['customers_mod_jobs_schema'],
            $request['customers_mod_jobs_values'] ?? []
        );
        $customer_mod_jobs->save();

        unset($request['customers_mod_jobs_schema']);
        unset($request['customers_mod_jobs_values']);

        $customer = \App\Models\Customer::find($id);

        $customer->fill($request->except([
            'order',
            'created_at',
            'updated_at',
            'saveRedirect',
            'redirect'
        ]));

        $customer->save();

        if ($request['redirect']) {

            if (to_route('jobs_listen.edit', $id)->getTargetUrl() == $saveRedirect) {
                return Inertia::location(to_route('jobs_listen.index')->getTargetUrl());
            }

            return Redirect::to($saveRedirect);

        } else {

            return Inertia::location(to_route('jobs_listen.edit', [
                'id' => $id,
                'saveRedirectURL' => $saveRedirect
            ])->getTargetUrl());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Customer::destroy($id);

        return to_route('jobs_listen.index');
    }
}
