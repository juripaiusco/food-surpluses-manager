<?php

namespace App\Http\Controllers;

use App\Models\JobSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ModJobsSettings extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexSections()
    {
        $request_validate_array = [
            'title',
        ];

        // Query data
        $data = \App\Models\JobSettings::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:' . implode(',', $request_validate_array)],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $data->where(function ($q) use ($request_validate_array) {

                foreach ($request_validate_array as $field) {
                    $q->orWhere($field, 'like', '%' . request('s') . '%');
                }

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->where('type', 'section');
        $data = $data->select();
        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return Inertia::render('JobsSettings/Sections/List', [
            'data' => $data,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createSections()
    {
        // Creo un oggetto di dati vuoto
        $table_columns = Schema::getColumnListing('mod_jobs_settings');

        $data_array = array();
        foreach ($table_columns as $data_field) {
            $data_array[$data_field] = null;
        }

        unset($data_array['id']);
        unset($data_array['deleted_at']);
        unset($data_array['created_at']);
        unset($data_array['updated_at']);

        $data = json_decode(json_encode($data_array), true);

        $data['schema'] = '';
        $data['type'] = 'section';

        return Inertia::render('JobsSettings/Sections/Form', [
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeSections(Request $request)
    {
        $data = new \App\Models\JobSettings();

        $data->fill($request->all());

        $data->save();

        return to_route('jobs_settings.index');
    }

    /**
     * Display the specified resource.
     */
    public function showSections(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editSections(string $id)
    {
        $data = \App\Models\JobSettings::find($id);

        return Inertia::render('JobsSettings/Sections/Form', [
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateSections(Request $request, string $id)
    {
        $redirect = $request->input('redirect');

        $data = \App\Models\JobSettings::find($id);

        $data->fill($request->all());

        $data->save();

        if ($redirect) {
            return to_route('jobs_settings.index');
        } else {
            return Inertia::location(to_route('jobs_settings.sections.edit', $id)->getTargetUrl());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroySections(string $id)
    {
        \App\Models\JobSettings::destroy($id);

        return to_route('jobs_settings.index');
    }


    public function indexReports()
    {
        $request_validate_array = [
            'title',
        ];

        // Query data
        $data = \App\Models\JobSettings::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:' . implode(',', $request_validate_array)],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $data->where(function ($q) use ($request_validate_array) {

                foreach ($request_validate_array as $field) {
                    $q->orWhere($field, 'like', '%' . request('s') . '%');
                }

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->where('type', 'report');
        $data = $data->select();
        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return Inertia::render('JobsSettings/Reports/List', [
            'data' => $data,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }

    private function fieldsReports()
    {
        $columns = Schema::getColumnListing('customers');

        $customers_array = array();
        foreach ($columns as $customers_field) {
            $customers_array[$customers_field] = null;
        }

        unset($customers_array['id']);
        unset($customers_array['deleted_at']);
        unset($customers_array['created_at']);
        unset($customers_array['updated_at']);

        foreach ($customers_array as $k => $v) {
            $report_fields_customer[] = array(
                'name' => $k,
                'label' => '',
            );
        }

        $JobSettings = JobSettings::query()
            ->where('type', 'section')
            ->orderBy('title')
            ->get();

        $report_fields[] = array(
            'name' => 'Campi default anagrafica',
            'fields' => $report_fields_customer
        );
        foreach ($JobSettings as $JobSetting) {
            $report_fields[] = array(
                'name' => $JobSetting->title,
                'fields' => $this->extractNameAndLabel(json_decode($JobSetting->schema, true)),
            );
        }

        return $report_fields;
    }

    public function createReports()
    {
        // Creo un oggetto di dati vuoto
        $table_columns = Schema::getColumnListing('mod_jobs_settings');

        $data_array = array();
        foreach ($table_columns as $data_field) {
            $data_array[$data_field] = null;
        }

        unset($data_array['id']);
        unset($data_array['deleted_at']);
        unset($data_array['created_at']);
        unset($data_array['updated_at']);

        $data = json_decode(json_encode($data_array), true);

        $data['schema']['filter'][] = array(
            'field' => '',
            'operator' => '',
            'value' => '',
        );
        $data['schema']['table'][] = array(
            'field' => '',
            'title' => '',
        );
        $data['type'] = 'report';
        $data['report_fields'] = $this->fieldsReports();

        return Inertia::render('JobsSettings/Reports/Form', [
            'data' => $data
        ]);
    }

    public function storeReports(Request $request)
    {
        $data = new \App\Models\JobSettings();

        $request['schema'] = json_encode($request['schema']);

        $data->fill($request->all());

        $data->save();

        return to_route('jobs_settings.reports.index');
    }

    public function editReports(string $id)
    {
        $data = \App\Models\JobSettings::find($id);

        $data['schema'] = json_decode($data['schema'], true);
        $data['report_fields'] = $this->fieldsReports();

        return Inertia::render('JobsSettings/Reports/Form', [
            'data' => $data
        ]);
    }

    public function updateReports(Request $request, string $id)
    {
        $redirect = $request->input('redirect');

        $data = \App\Models\JobSettings::find($id);

        $data->fill($request->all());

        $data->save();

        if ($redirect) {
            return to_route('jobs_settings.reports.index');
        } else {
            return Inertia::location(to_route('jobs_settings.reports.edit', $id)->getTargetUrl());
        }
    }

    public function destroyReports(string $id)
    {
        \App\Models\JobSettings::destroy($id);

        return to_route('jobs_settings.reports.index');
    }

    public function extractNameAndLabel(array $array, array &$result = []): array
    {
        // Se l'array corrente contiene name e label, li salvo
        if (isset($array['name'], $array['label'])) {
            $result[] = [
                'name'  => $array['name'],
                'label' => $array['label'],
            ];
        }

        // Scorro tutti i valori
        foreach ($array as $value) {
            if (is_array($value)) {
                $this->extractNameAndLabel($value, $result);
            }
        }

        return $result;
    }
}
