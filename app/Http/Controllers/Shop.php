<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Shop extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('shop.cash');
    }

    public function search(Request $request)
    {
        $out = '';

        if ($request->input('customer_cod')) {

            $customer = \App\Model\Customer::where('cod', $request->input('customer_cod'))
                ->first();

            $out = json_encode($customer);

        }

        if ($request->input('product_cod')) {

            $product = \App\Model\Product::where('cod', $request->input('product_cod'))
                ->first();

            $out = json_encode($product);

        }

        return $out;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Raggruppo i prodotti, così da scalare in una sola volta
        // gli stessi prodotti
        $array_group = array();

        foreach ($request->input('product_id') as $product_id) {

            if (!isset($array_group[$product_id])) {
                $array_group[$product_id] = 0;
            }

            $array_group[$product_id] += 1;

        }

        // Creo un codice di riferimento random
        $order_reference = strtoupper(\Illuminate\Support\Str::random(5));

        // Imposto la data attuale dell'ordine
        $oder_data = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')));

        // Recupero i dati del cliente
        $customer = \App\Model\Customer::find($request->input('customer_id'));

        // Creazione ordine
        $order = new Order();
        $order_id = $order->store(array(
            'data' => array(
                'reference' => $order_reference,
                'user_id' => Auth::id(),
                'customer_id' => $request->input('customer_id'),
                'json_customer' => json_encode($customer),
                'date' => $oder_data
            )
        ));

        // Scarico i prodotti da magazzino
        $points = 0;
        $product_array = array();

        foreach ($array_group as $product_id => $count) {

            $product = \App\Model\Product::find($product_id);

            if (isset($product->id)) {

                $store = new Store();
                $store->setStore(array(
                    'storeArrayData' => array(
                        'id' => $product_id,
                        'order_reference' => $order_reference,
                        'order_id' => $order_id,
                        'customer_id' => $request->input('customer_id'),
                        'kg' => isset($product->kg) ? $product->kg * $count * (-1) : null,
                        'amount' => $product->amount * $count * (-1),
                        'products_count' => $count,
                        'date' => $oder_data,
                    )
                ));

                $points += $count * $product->points;

                for ($i = 0; $i < $count; $i++) {
                    $product_array[] = $product;
                }

            }

        }

        // Modifica ordine appena creato, per aggiungere i dati mancanti
        $order = new Order();
        $order->store(array(
            'id' => $order_id,
            'data' => array(
                'points' => $points,
                'json_products' => json_encode($product_array)
            )
        ));

        return redirect()->route('shop');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
