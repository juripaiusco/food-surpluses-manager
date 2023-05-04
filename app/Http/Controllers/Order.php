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

                    if ($field === 'customer_name') {

                        $q->orWhere('json_customer', 'like', '%' . request('s') . '%');

                    } else {

                        $q->orWhere($field, 'like', '%' . request('s') . '%');
                    }

                }

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->select();

        $data->addSelect(DB::raw(
            'CONCAT(
                JSON_VALUE(json_customer, \'$.number\'),
                \' - \',
                JSON_VALUE(json_customer, \'$.cod\'),
                \' - \',
                JSON_VALUE(json_customer, \'$.name\'),
                \' \',
                JSON_VALUE(json_customer, \'$.surname\')
            ) as customer_name'
        ));

        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

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
        /*// Creo un oggetto di dati vuoto
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
        ]);*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($args = array())
    {
        if (isset($args['id'])) {

            $order_id = $args['id'];

            DB::table('orders')
                ->where('id', $args['id'])
                ->update($args['data']);

        } else {

            $order_id = DB::table('orders')
                ->insertGetId($args['data']);
        }

        return $order_id;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = \App\Models\Order::with('customer')
            ->with('retail')
            ->with('user')
            ->find($id);

        if (!isset($data)) {
            return to_route('orders.index');
        }

        $customer = json_decode($data->json_customer);
        $products = json_decode($data->json_products);

        return Inertia::render('Orders/Show', [
            'data' => $data,
            'customer' => $customer,
            'products' => $products,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        /*$data = \App\Models\Order::with('store')
            ->with('store.user')
            ->with('store.customer')
            ->with('store.order')
            ->find($id);

        return Inertia::render('Orders/Form', [
            'data' => $data
        ]);*/
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        /*$order = \App\Models\Order::find($id);

        $order->fill($request->all());

        $order->save();

        return to_route('orders.list');*/
    }

    public function restore(string $id)
    {
        $order = \App\Models\Order::find($id);

        // Ripristino le giacenze prodotti
        foreach (json_decode($order->json_products) as $product) {

            $store = \App\Models\Store::where('customer_id', $order->customer_id)
                ->where('order_id', $id)
                ->where('product_id', $product->id);

            $store_get = $store->first();

            if (isset($store_get)) {

                $product_edit = \App\Models\Product::find($product->id);
                $product_edit->kg_total += $store_get->kg ? $store_get->kg * (-1) : 0;
                $product_edit->amount_total += $store_get->amount ? $store_get->amount * (-1) : 0;
                $product_edit->save();

                $store->delete();

            }
        }
        // END - Ripristino le giacenze prodotti

        // Riemetto i punti al cliente
        $orders_count_this_month = \App\Models\Order::where('customer_id', $order->customer_id)
            ->where('date', 'LIKE', date('Y-m-d') . '%')
            ->count();

        $customer = \App\Models\Customer::find($order->customer_id);

        if ($orders_count_this_month == 1) {
            $customer->points = $customer->points_renew;
        } else {
            $customer->points += $order->points;
        }

        $customer->save();
        // END - Riemetto i punti al cliente
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->restore($id);

        \App\Models\Order::destroy($id);

        return to_route('orders.index');
    }
}
