<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Store extends Controller
{
    public function index(Request $request)
    {
        $product = array();

        if ($request->input('s')) {

            $product = \App\Models\Product::where('cod', $request->input('s'))
                ->first();
        }

        return Inertia::render('Store/Form', [
            'product' => $product,
            'data' => [
                'date' => date('d/m/Y H:i:s'),
                's' => $request->input('s')
            ]
        ]);
    }

    public function store(Request $request, $id)
    {
        $store = new \App\Models\Store();

        $store->kg = $request->input('kg');
        $store->amount = $request->input('amount');
        $store->cod = $request->input('cod');
        $store->product_id = $id;
        $store->user_id = Auth::user()->id;
        $store->date = date(
            'Y-m-d H:i:s',
            strtotime(
                str_replace(
                    '/',
                    '-',
                    $request->input('date')
                )
            )
        );

        $store->save();

        $product = \App\Models\Product::find($id);

        $product->kg_total += $request->input('kg');
        $product->amount_total += $request->input('amount');

        $product->save();

        return Inertia::location(to_route('store.index')->getTargetUrl());
    }
}
