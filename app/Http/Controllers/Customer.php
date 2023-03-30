<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class Customer extends Controller
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
            'cod',
            'number',
            'name',
            'surname',
            'address',
            'family_number',
            'points',
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
                    $q->orWhere($field, 'like', '%' . request('s') . '%');
                }

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->select();
        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return Inertia::render('Customers/List', [
            'data' => $data,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
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

        $data = json_decode(json_encode($customers_array), true);

        return Inertia::render('Customers/Form', [
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer = new \App\Models\Customer();

        $customer->fill($request->all());

        $customer->save();

        return to_route('customers.list');
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
        $data = \App\Models\Customer::with('order')
            ->find($id);

        return Inertia::render('Customers/Form', [
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = \App\Models\Customer::find($id);

        $customer->fill($request->all());

        $customer->save();

        return to_route('customers.list');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Customer::destroy($id);

        return to_route('customers.list');
    }
}
