<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class Retail extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request_validate_array = [
            'name',
            'address',
        ];

        // Query data
        $retails = \App\Models\Retail::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:' . implode(',', $request_validate_array)],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $retails->where(function ($q) use ($request_validate_array) {

                foreach ($request_validate_array as $field) {
                    $q->orWhere($field, 'like', '%' . request('s') . '%');
                }

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $retails->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $retails = $retails->select();
        $retails = $retails->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return Inertia::render('Retails/List', [
            'retails' => $retails,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Creo un oggetto di dati vuoto
        $retail_columns = Schema::getColumnListing('retails');

        $retails_array = array();
        foreach ($retail_columns as $retails_field) {
            $retails_array[$retails_field] = null;
        }

        unset($retails_array['id']);
        unset($retails_array['deleted_at']);
        unset($retails_array['created_at']);
        unset($retails_array['updated_at']);

        $retail = json_decode(json_encode($retails_array), true);

        return Inertia::render('Retails/Form', [
            'data' => $retail
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $retail = new \App\Models\Retail();

        $retail->fill($request->all());

        $retail->save();

        return to_route('retails.list');
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
        $retail = \App\Models\Retail::find($id);

        return Inertia::render('Retails/Form', [
            'data' => $retail
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $retail = \App\Models\Retail::find($id);

        $retail->fill($request->all());

        $retail->save();

        return to_route('retails.list');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Retail::destroy($id);

        return to_route('retails.list');
    }
}
