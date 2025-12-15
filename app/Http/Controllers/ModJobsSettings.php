<?php

namespace App\Http\Controllers;

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

        $data['schema'] = '';
        $data['type'] = 'report';

        return Inertia::render('JobsSettings/Reports/Form', [
            'data' => $data
        ]);
    }

    public function storeReports(Request $request)
    {

    }

    public function editReports(string $id)
    {

    }

    public function updateReports(Request $request, string $id)
    {

    }

    public function destroyReports(string $id)
    {

    }
}
