<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class Shop extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Ricerco il cliente
        if ($request->input('s_customer')) {

            $customer = \App\Models\Customer::where('cod', $request->input('s_customer'))
                ->first();

        } else {

            // Elimino la sessione quando inizio la cassa
            $request->session()->forget('shopProducts');
        }

        // Ricerco il prodotto
        if ($request->input('s_product')) {

            $product = \App\Models\Product::where('cod', $request->input('s_product'))
                ->first();

            if (isset($product->id)) {
                $request->session()->push('shopProducts', $product);
            }
        }

        // Salvo la sessione prodotti tramite inertia
        Inertia::share('shopProducts', $request->session()->get('shopProducts'));

        return Inertia::render('Shop/Cash', [
            'data' => [
                's_customer' => $request->input('s_customer'),
                'customer' => isset($customer) ? $customer : [],
                's_product' => $request->input('s_product'),
                'product' => isset($product) ? $product : [],
            ],
            'create_url' => route('shop.index', [
                's_customer' => $request->input('s_customer'),
            ])
        ]);
    }
}
