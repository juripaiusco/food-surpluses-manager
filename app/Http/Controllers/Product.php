<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class Product extends Controller
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
            'monitoring_buy',
            'cod',
            'type',
            'name',
            'kg_total',
            'amount_total',
            'points',
        ];

        // Query data
        $data = \App\Models\Product::query();

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

        return Inertia::render('Products/List', [
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
        $columns = Schema::getColumnListing('products');

        $products_array = array();
        foreach ($columns as $products_field) {
            $products_array[$products_field] = null;
        }

        unset($products_array['id']);
        unset($products_array['deleted_at']);
        unset($products_array['created_at']);
        unset($products_array['updated_at']);

        $data = json_decode(json_encode($products_array), true);

        return Inertia::render('Products/Form', [
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cod'       => ['required', 'unique:products', 'min:7'],
            'type'      => ['required'],
            'name'      => ['required'],
            'points'    => ['required'],
            'amount'    => ['required'],
        ]);

        $product = new \App\Models\Product();

        $product->cod = $request->input('cod');
        $product->type = $request->input('type');
        $product->monitoring_buy = $request->input('monitoring_buy');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->points = $request->input('points');
        $product->kg = $request->input('kg');
        $product->amount = $request->input('amount');

        $product->save();

        return to_route('products.index');
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
        $data = \App\Models\Product::with('store')
            ->with('store.user')
            ->with('store.customer')
            ->with('store.order')
            ->find($id);

        return Inertia::render('Products/Form', [
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'cod'       => ['required', 'unique:products,cod,' . $id, 'min:7'],
            'type'      => ['required'],
            'name'      => ['required'],
            'points'    => ['required'],
            'amount'    => ['required'],
        ]);

        $product = \App\Models\Product::find($id);

        $product->cod = $request->input('cod');
        $product->type = $request->input('type');
        $product->monitoring_buy = $request->input('monitoring_buy');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->points = $request->input('points');
        $product->kg = $request->input('kg');
        $product->amount = $request->input('amount');

        $product->save();

        return to_route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Product::destroy($id);

        return to_route('products.index');
    }
}
