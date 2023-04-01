<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class Order extends Controller
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
            'date',
            'reference',
            'customer_name',
            'points',
        ];

        // Query data
        $data = \App\Models\Order::query();
        $data->with('customer');
        $data->with('retail');

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

        $data->addSelect(DB::raw(
            'CONCAT(JSON_VALUE(json_customer, \'$.surname\'), \' \', JSON_VALUE(json_customer, \'$.name\')) as customer_name'
        ));

        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

//        dd($data->items());

        return Inertia::render('Orders/List', [
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
        $columns = Schema::getColumnListing('orders');

        $orders_array = array();
        foreach ($columns as $orders_field) {
            $orders_array[$orders_field] = null;
        }

        unset($orders_array['id']);
        unset($orders_array['deleted_at']);
        unset($orders_array['created_at']);
        unset($orders_array['updated_at']);

        $data = json_decode(json_encode($orders_array), true);

        return Inertia::render('Orders/Form', [
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $order = new \App\Models\Order();

        $order->fill($request->all());

        $order->save();

        return to_route('orders.list');
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
        $data = \App\Models\Order::with('store')
            ->with('store.user')
            ->with('store.customer')
            ->with('store.order')
            ->find($id);

//        dd($data);

        return Inertia::render('Orders/Form', [
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = \App\Models\Order::find($id);

        $order->fill($request->all());

        $order->save();

        return to_route('orders.list');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Order::destroy($id);

        return to_route('orders.list');
    }
}
