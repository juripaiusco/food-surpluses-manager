<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

            $customer = $this->init($request);

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

        // Data per popolare l'interfaccia ---------------------------------------------------
        $products_fead = \App\Models\Product::where('type', 'fead')
            ->orderby('name')
            ->get();

        $products_feadno = \App\Models\Product::where('type', 'fead no')
            ->orderby('name')
            ->get();

        $products_more_moved = DB::table('stores')
            ->select([
                'stores.product_id',
                'stores.cod',
                'products.name',
                DB::raw('count(*) AS count')
            ])
            ->join('products', 'products.id', '=', 'stores.product_id')
            ->groupBy('product_id')
            ->orderBy('count', 'DESC')
            ->limit(16)
            ->get();
        // -----------------------------------------------------------------------------------

        return Inertia::render('Shop/Cash', [
            'data' => [
                's_customer' => $request->input('s_customer'),
                'customer' => isset($customer) ? $customer : [],
                's_product' => $request->input('s_product'),
                'product' => isset($product) ? $product : [],
                'is_first_order' => isset($customer) ? $this->is_first_order($customer) : false,
                'products_fead' => $products_fead,
                'products_feadno' => $products_feadno,
                'products_more_moved' => $products_more_moved,
            ],
            'create_url' => route('shop.index', [
                's_customer' => $request->input('s_customer'),
            ])
        ]);
    }

    /**
     * Inizializzazione cassa, quando viene identificato il cliente
     * ed il cliente è attivo, vegono eseguite delle impostazioni
     * @return void
     */
    public function init(Request $request)
    {
        $customer = \App\Models\Customer::with('order')
            ->where('cod', $request->input('s_customer'))
            ->first();

        // Al primo inserimento del cliente inserire la borsa frutta / verdura -----------------
        if ($request->session()->get('shopProducts') == null) {
            $this->add($request, 'P000093');
            $this->add($request, 'P000093');
        }
        // -------------------------------------------------------------------------------------

        // Verifico che sia la prima spesa del mese --------------------------------------------
        if ($this->is_first_order($customer)) {

            $customer->points = $customer->points / 2;

        }
        // -------------------------------------------------------------------------------------

        return $customer;
    }

    /**
     * Verifico che sia il primo ordine
     * @return void
     */
    public function is_first_order($customer)
    {
        $out = false;

        if (isset($customer->order) &&
            (
                count($customer->order) <= 0 ||
                (date('n', strtotime($customer->order[0]->date)) < date('n'))
            )) {

            $out = true;

        }

        return $out;
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
        $customer = \App\Models\Customer::with('order')
            ->find($customer_id);

        // Verifico che sia il primo ordine del mese
        $is_first_order = $this->is_first_order($customer);

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

        // Verifico se è il primo ordine del mese, in caso positivo scalo al cliente
        // l'80% di metà punteggio se non viene superato l'80% di metà del punteggio
        if ($is_first_order &&
            $points < ($customer->points_renew / 2) * 80/100) {

            $points_min = ($customer->points_renew / 2) * 80/100;

            $customer = \App\Models\Customer::find($customer->id);

            // Carico i punti consumati
            $customer->points += $points;

            // Scarici il minimo dei punti da consumare
            $customer->points -= $points_min;

            $customer->save();

        }

        return Inertia::location(to_route('shop.index')->getTargetUrl());
    }
}
