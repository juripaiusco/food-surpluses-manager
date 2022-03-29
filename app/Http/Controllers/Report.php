<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Report extends Controller
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

    public function get_reports()
    {
        $orders = \App\Model\Order::where('date', 'LIKE', date('Y-m-d') . '%')
            ->get();
        $reports = array();

        // Prendo la lista degli ordini
        foreach ($orders as $order) {

            $products_obj = json_decode($order->json_products);
            $customer_obj = json_decode($order->json_customer);

            // Ogni ordine ha una lista di prodotti che ciclo
            foreach ($products_obj as $product) {

                // Recupero soltanto i prodotti FEAD
                if ($product->type == 'fead') {

                    if (!isset($n_family_total[$customer_obj->cod]))
                        $n_family_total[$product->cod][$customer_obj->cod] = 0;

                    $reports[$product->cod]['product'] = $product;

                    // Se voglio contare tutte le volte che ogni cliente ha acquistato il prodotto
                    /*$reports[$product->cod]['customers'][] = $customer_obj;
                    $n_family_total[$product->cod][] = $customer_obj->family_number;*/

                    // Se voglio contare da quale singola famiglia è stato acquistato il prodotto
                    $reports[$product->cod]['customers'][$customer_obj->cod] = $customer_obj;
                    $n_family_total[$product->cod][$customer_obj->cod] = $customer_obj->family_number;

                    $reports[$product->cod]['customers_count'] = array(
                        'n_family' => count($reports[$product->cod]['customers']),
                        'n_family_total' => array_sum($n_family_total[$product->cod])
                    );

                }
            }
        }

        return $reports;
    }

    public function index()
    {
        $reports = $this->get_reports();

        return view('report.list', [
            'reports' => $reports
        ]);
    }

}
