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

    public function customerSearch(Request $request)
    {
        $out = '';

        if ($request->input('customer_cod')) {

            $customer = \App\Model\Customer::where('cod', $request->input('customer_cod'))
                ->first();

            $out = '
                <table class="table">
                    <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Nome</th>
                            <th class="">Indirizzo</th>
                            <th class="text-right">Punti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="align-middle">' . $customer['cod'] . '</td>
                            <td class="align-middle">' . $customer['name'] . ' ' . $customer['surname'] . '</td>
                            <td class="align-middle">' . $customer['address'] . '</td>
                            <td class="align-middle text-right h1">' . $customer['points'] . '</td>
                        </tr>
                    </tbody>
                </table>
            ';

        }

        return $out;
    }

    public function productSearch(Request $request)
    {
        $out = '';

        if ($request->input('product_cod')) {

            $product = \App\Model\Product::where('cod', $request->input('product_cod'))
                ->first();

            /*$out = '
                <table class="table">
                    <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Nome</th>
                            <th class="">Indirizzo</th>
                            <th class="text-right">Punti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="align-middle">' . $product['cod'] . '</td>
                            <td class="align-middle">' . $product['name'] . ' ' . $product['surname'] . '</td>
                            <td class="align-middle">' . $product['address'] . '</td>
                            <td class="align-middle text-right h1">' . $product['points'] . '</td>
                        </tr>
                    </tbody>
                </table>
            ';*/

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
        //
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
