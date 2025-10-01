<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Store extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
        $request->validate([
            'amount'    => ['required'],
        ]);

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

    public function setStore($args = array())
    {
        if ($args['storeArrayData']) {

            $product = \App\Models\Product::find($args['storeArrayData']['id']);

            // Tolgo i punti CLIENTE dalla tessera.
            // Il cliente non può inserire prodotti, quindi ogni volta che
            // è presente un customer_id significa che l'operazione è di acquisto
            // prodotto, quindi al cliente devono essere scalati i punti.
            if (isset($args['storeArrayData']['customer_id'])) {

                $customer = \App\Models\Customer::find($args['storeArrayData']['customer_id']);

                $customer->points += $args['storeArrayData']['points'] * $args['storeArrayData']['products_count'] * (-1);

                $customer->save();

            }

            // Verifico che il prodotto sia una box ed estraggo i prodotti
            if (isset($product->id) && $product->type == 'box') {

                $box_products = json_decode($product->json_box, true);

                foreach ($box_products as $box_product) {

                    $box_product_db = \App\Models\Product::find($box_product['id']);

                    $box_args = $args;
                    $box_args['storeArrayData']['id'] = $box_product['id'];
                    $box_args['storeArrayData']['kg'] = $args['storeArrayData']['products_count'] * $box_product_db->kg * (-1);
                    $box_args['storeArrayData']['amount'] = $args['storeArrayData']['products_count'] * $box_product_db->amount * (-1);
                    $box_args['storeArrayData']['price'] = $args['storeArrayData']['products_count'] * $box_product_db->price;

                    $this->setStoreProduct($box_product_db, $box_args);

                }

            }
            // END - Verifico che il prodotto sia una box ed estraggo i prodotti

            // Modifico i dati PRODOTTO
            if (isset($product->id)) {

                $this->setStoreProduct($product, $args);

            }
        }
    }

    /**
     * Imposto il movimento per il singolo prodotto
     *
     * @param $product
     * @param $args
     * @return void
     */
    public function setStoreProduct($product, $args)
    {
        // Inserimento movimento magazzino
        $store = new \App\Models\Store();

        $store->product_id = $args['storeArrayData']['id'];
        $store->user_id = Auth::id();
        $store->order_id = isset($args['storeArrayData']['order_id']) ? $args['storeArrayData']['order_id'] : null;
        $store->customer_id = isset($args['storeArrayData']['customer_id']) ? $args['storeArrayData']['customer_id'] : null;
        $store->cod = $product->cod;
        $store->kg = isset($args['storeArrayData']['kg']) ? $args['storeArrayData']['kg'] : null;
        $store->amount = $args['storeArrayData']['amount'];
        $store->price = $args['storeArrayData']['price'];
        $store->date = $args['storeArrayData']['date'];

        $store->save();

        // Modifica quantità totale prodotto
        $product->type == 'fead no' ? $product->kg_total = null : $product->kg_total += $args['storeArrayData']['kg'];
        $product->amount_total += $args['storeArrayData']['amount'];

        $product->save();
    }
}
