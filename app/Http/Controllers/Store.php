<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Store extends Controller
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
        return view('store.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('store.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$store = new \App\Model\Store();

        $store->product_id = $request->input('id');
        $store->cod = $request->input('cod');
        $store->kg = $request->input('kg');
        $store->amount = $request->input('amount');
        $store->date = date('Y-m-d H:i:s', strtotime($request->input('date')));

        $store->save();*/

        /*$this->setStore($request->input('id'), $request->input('kg'), $request->input('amount'));*/

        $this->setStore(array(
            'storeArrayData' => $request->input(),
        ));

        return redirect()->route('store');
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

    public function search(Request $request)
    {
        $out = '';

        if ($request->input('product_cod')) {

            $product = \App\Model\Product::where('cod', $request->input('product_cod'))
                ->first();

            $out = json_encode($product);

        }

        return $out;
    }

    public function setStore($args = array())
    {
        if ($args['storeArrayData']) {

            $product = \App\Model\Product::find($args['storeArrayData']['id']);

            if (isset($product->id)) {

                // Inserimento movimento magazzino
                $store = new \App\Model\Store();

                $store->product_id = $args['storeArrayData']['id'];
                $store->cod = $product->cod;
                $store->kg = isset($args['storeArrayData']['kg']) ? $args['storeArrayData']['kg'] : null;
                $store->amount = $args['storeArrayData']['amount'];
                $store->date = date('Y-d-m H:i:s', strtotime($args['storeArrayData']['date']));

                $store->save();

                // Modifica quantità totale prodotto
                $product->type == 'fead no' ? $product->kg_total = null : $product->kg_total += $args['storeArrayData']['kg'];
                $product->amount_total += $args['storeArrayData']['amount'];

                $product->save();

            }

        }
    }
}
