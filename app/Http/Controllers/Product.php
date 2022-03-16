<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Product extends Controller
{
    var $type_array = array(
        'fead',
        'fead no',
    );

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
    public function index(Request $request)
    {
        $s = $request->input('s');

        $products = \App\Model\Product::where('name', 'LIKE', '%'.$s.'%')
            ->orWhere('cod', 'LIKE', '%'.$s.'%')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('products.list', [
            'products' => $products,
            's' => $s
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $cod = $request->input('cod');

        return view('products.form', [
            'cod' => isset($cod) ? $cod : '',
            'type_array' => $this->type_array
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new \App\Model\Product();

        $product->cod = $request->input('cod');
        $product->type = $request->input('type');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->points = $request->input('points');
        $product->kg = $request->input('kg') ? str_replace(',', '.', $request->input('kg')) : null;
        $product->amount = $request->input('amount');

        $product->save();

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
        $product = \App\Model\Product::with('store')
            ->with('store.user')
            ->with('store.customer')
            ->with('store.order')
            ->find($id);

        return view('products.form', [
            'product' => $product,
            'type_array' => $this->type_array
        ]);
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
        $product = \App\Model\Product::find($id);

        $product->cod = $request->input('cod');
        $product->type = $request->input('type');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->points = $request->input('points');

        if ($request->input('kg')) {

            $product->kg = str_replace(',', '.', $request->input('kg'));

        } else {

            $product->kg = null;
        }

        $product->amount = $request->input('amount');

        $product->save();

        return redirect()->route('products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Model\Product::destroy($id);

        return redirect()->route('products');
    }
}
