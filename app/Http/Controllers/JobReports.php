<?php

namespace App\Http\Controllers;

use App\Models\JobSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class JobReports extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [];

        $reports = JobSettings::query()
            ->where('type', 'report');

        if (isset(request()->rid)) {

            $report = $reports->where('id', request()->rid)
                ->first();

            $customers = \App\Models\Customer::query()
                ->leftJoin(
                    'customers_mod_jobs',
                    'customers.id',
                    '=',
                    'customers_mod_jobs.customer_id'
                )
                ->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(customers_mod_jobs.values, '$.mod_jobs_anagrafica_parrocchia')) LIKE ?",
                    ['%Antonio%']
                )
                ->select('customers.*')
                ->get();

            dd($customers);
            dd(json_decode($report->schema, true));

        }

        $reports = $reports->get();

        return Inertia::render('JobsReports/List', [
            'data' => $data,
            'reports' => $reports,
            'filters' => request()->all(['number', 's', 'orderby', 'ordertype', 'filters'])
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
}
