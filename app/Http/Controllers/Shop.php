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

    /**
     * Mostra la cassa
     *
     * @param Request $request
     * @return \Inertia\Response
     */
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
                $product->index = $request->session()->get('shopProducts') ? array_key_last($request->session()->get('shopProducts')) + 1 : 0;
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

    /**
     * Rimuove i prodotti dal carrello della cassa
     * 
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request, $id)
    {
        // Recupero Sessione Dati
        $shopProducts_array = $request->session()->get('shopProducts');

        // Elimino il prodotto selezionato
        unset($shopProducts_array[$request->input('product.index')]);

        // Ricreo l'indice
        $shopProducts_array = array_values($shopProducts_array);

        // Importo il valore index
        foreach ($shopProducts_array as $k => $product) {
            $shopProducts_array[$k]->index = $k;
        }

        // Imposto il valore sessione
        $request->session()->put('shopProducts', $shopProducts_array);

        // Salvo la sessione prodotti tramite inertia
        Inertia::share('shopProducts', $request->session()->get('shopProducts'));

        return to_route('shop.index', [
            's_customer' => $request->input('s_customer')
        ]);
    }
}
