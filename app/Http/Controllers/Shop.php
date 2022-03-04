<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
//        dd($request->input());

        $array_group = array();

        foreach ($request->input('product_id') as $product_id) {

            if (!isset($array_group[$product_id])) {
                $array_group[$product_id] = 0;
            }

            $array_group[$product_id] += 1;

        }

        foreach ($array_group as $product_id => $count) {

            $product = \App\Model\Product::find($product_id);

            if (isset($product->id)) {

                $store = new Store();
                $store->setStore(array(
                    'storeArrayData' => array(
                        'id' => $product_id,
                        'customer_id' => $request->input('customer_id'),
                        'kg' => isset($product->kg) ? $product->kg * $count * (-1) : null,
                        'amount' => $product->amount * $count * (-1),
                        'date' => date('Y-d-m H:i:s'),
                    )
                ));

            }

        }

        /*foreach ($request->input('product_id') as $product_id) {

            $product = \App\Model\Product::find($product_id);

            if (isset($product->id)) {

                $store = new Store();
                $store->setStore(array(
                    'storeArrayData' => array(
                        'id' => $product_id,
                        'customer_id' => $request->input('customer_id'),
                        'kg' => isset($product->kg) ? $product->kg * (-1) : null,
                        'amount' => $product->amount * (-1),
                        'date' => date('Y-d-m H:i:s'),
                    )
                ));

            }

        }*/

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
