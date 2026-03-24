<?php

namespace App\Http\Controllers;

use App\Models\JobSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

class JobReports extends Controller
{
    private function isSafeQuery(string $query): bool
    {
        $forbidden = ['insert', 'update', 'delete', 'drop', 'truncate', 'alter', 'grant', 'create'];
        $queryLower = strtolower($query);

        foreach ($forbidden as $keyword) {
            if (str_contains($queryLower, $keyword)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $id = null)
    {
        $data = [];
        $report = [];

        $reports = JobSettings::query()
            ->where('type', 'report');

        if ($id) {

            // Recupero lo schema json con il valore per generare la query corretta
            $report = (clone $reports)->where('id', $id)
                ->first();

            // Recupero i campi della query per poter ricercare
            foreach (json_decode($report->schema)->table as $d_field) {
                $fields_search_array[] = $d_field->field;
            }

            // Se esiste la query manuale usala
            if (isset($report->query)) {

                if (!$this->isSafeQuery($report->query)) {
                    abort(403, 'Query non consentita');
                }

                $data = DB::query()->fromSub("({$report->query})", 'main_result');

                // Filtro RICERCA
                if (request('s')) {
                    $data->where(function ($q) use ($fields_search_array) {

                        foreach ($fields_search_array as $field) {
                            $q->orWhere($field, 'like', '%' . request('s') . '%');
                        }

                    });
                }

                // Filtro ORDINAMENTO
                if (request('orderby') && request('ordertype')) {
                    $data->orderby(request('orderby'), strtoupper(request('ordertype')));
                }

                $data = $data->get();

            } else {

                $customers = \App\Models\Customer::query()
                    ->leftJoin(
                        'customers_mod_jobs',
                        'customers.id',
                        '=',
                        'customers_mod_jobs.customer_id'
                    );

                // Genero la query in base al filtro impostato dallo schema json
                foreach (json_decode($report->schema, true)['filter'] as $q) {

                    if (substr($q['field'], 0, strlen('mod_jobs')) == 'mod_jobs') {

                        $queryField = "JSON_UNQUOTE(JSON_EXTRACT(customers_mod_jobs.values, '$." . $q['field'] . "'))";

                    } else {

                        $queryField = $q['field'];
                    }

                    $queryCondition = array(
                        $queryField . " " . $q['operator'] . " ?",
                        $q['operator'] == 'like' ? '%' . $q['value'] . '%' : $q['value']
                    );

                    if (isset($q['add_operator'])) {

                        switch ($q['add_operator']) {
                            case 'or':
                                $customers = $customers->orWhereRaw(
                                    $queryCondition[0],
                                    $queryCondition[1]
                                );
                                break;

                            default:
                                $customers = $customers->whereRaw(
                                    $queryCondition[0],
                                    $queryCondition[1]
                                );
                        }

                    } else {

                        $customers = $customers->whereRaw(
                            $queryCondition[0],
                            $queryCondition[1]
                        );
                    }
                }

                $data = $customers->select([
                    'customers.*',
                    'customers_mod_jobs.values',
                ]);

//            dd($data->toSql());

                // Filtro RICERCA
                if (request('s')) {
                    $data->where(function ($q) use ($fields_search_array) {

                        foreach ($fields_search_array as $field) {
                            $q->orWhere($field, 'like', '%' . request('s') . '%');
                        }

                    });
                }

                // Filtro ORDINAMENTO
                if (request('orderby') && request('ordertype')) {
                    $data->orderby(request('orderby'), strtoupper(request('ordertype')));
                }

                $data = $data->get();
                $data = $data->map(function ($customer) {

                    $values = is_array($customer->values)
                        ? $customer->values
                        : json_decode($customer->values ?? '{}', true);

                    unset($customer->values);

                    foreach ($values as $key => $value) {
                        $customer->{$key} = $value;
                    }

                    return $customer;
                });
            }
        }

        $reports = $reports->get();

        return Inertia::render('JobsReports/List', [
            'data' => $data,
            'report' => $report,
            'reportSchema' => isset($report->schema) ? json_decode($report->schema, true) : [],
            'reports' => $reports,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function export(string $id)
    {
        $reports = JobSettings::query()
            ->where('type', 'report');

        $report = (clone $reports)->where('id', $id)
            ->first();

        $data = DB::query()->fromSub("({$report->query})", 'main_result');

        $results = $data->get();

        return Excel::download(new ReportExport($results), $report->title . '-' . date('YmdHis') . '.xlsx');
    }
}
