<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            // Al primo inserimento del cliente inserire la borsa frutta / verdura
            $product = $this->add($request, 'P000093');
            $product = $this->add($request, 'P000093');

        } else {

            // Elimino la sessione quando inizio la cassa
            $request->session()->forget('shopProducts');
        }

        // Ricerco il prodotto e lo aggiungo alla sessione
        if ($request->input('s_product')) {
            $product = $this->add($request, $request->input('s_product'));
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
     * Aggiungo i prodotti nel carrello
     *
     * @return void
     */
    public function add(Request $request, $product_cod)
    {
        $product = \App\Models\Product::where('cod', $product_cod)
            ->first();

        if (isset($product->id)) {
            $product->index = $request->session()->get('shopProducts') ? array_key_last($request->session()->get('shopProducts')) + 1 : 0;
            $request->session()->push('shopProducts', $product);
        }

        return $product;
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

    public function store(Request $request)
    {
        // Raggruppo i prodotti, così da scalare in una sola volta
        // gli stessi prodotti
        $array_group = array();

        foreach ($request->input('products') as $product) {

            if (!isset($array_group[$product['id']])) {
                $array_group[$product['id']] = 0;
            }

            $array_group[$product['id']] += 1;

        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        // Creo un codice di riferimento random
        $order_reference = strtoupper(\Illuminate\Support\Str::random(5));

        // Imposto la data attuale dell'ordine
        $oder_data = date('Y-m-d H:i:s');

        // Recupero i dati del cliente
        $customer_id = $request->input('customer_id');
        $customer = \App\Models\Customer::find($customer_id);

        // Recupero l'ID retail
        $retail_id = current(array_keys(json_decode(Auth::user()->json_retails, true)));

        // User ID
        $user_id = Auth::user()->id;

        // Inserimento ordine ------------------------------------------------------
        $order = new Order();

        $order_id = $order->store(array(
            'data' => array(
                'reference' => $order_reference,
                'user_id' => $user_id,
                'customer_id' => $customer_id,
                'retail_id' => $retail_id,
                'json_customer' => json_encode($customer),
                'date' => $oder_data
            )
        ));

        // Scarico i prodotti da magazzino -----------------------------------------
        $points = 0;
        $product_array = array();

        foreach ($array_group as $product_id => $count) {

            $product = \App\Models\Product::find($product_id);

            if (isset($product->id)) {

                $store = new Store();
                $store->setStore(array(
                    'storeArrayData' => array(
                        'id' => $product_id,
                        'order_reference' => $order_reference,
                        'order_id' => $order_id,
                        'customer_id' => $customer_id,
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

        // Modifica ordine appena creato, per aggiungere i dati mancanti -----------------------
        $order = new Order();
        $order->store(array(
            'id' => $order_id,
            'data' => array(
                'points' => $points,
                'json_products' => json_encode($product_array)
            )
        ));

        return Inertia::location(to_route('shop.index')->getTargetUrl());
    }
}
