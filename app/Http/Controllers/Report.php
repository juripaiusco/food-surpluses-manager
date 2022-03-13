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

    public function index()
    {
        $orders = \App\Model\Order::get();
        $reports = array();

        foreach ($orders as $order) {

            $products_obj = json_decode($order->json_products);
            $customer_obj = json_decode($order->json_customer);

            foreach ($products_obj as $product) {

                if ($product->type == 'fead') {

                    if (!isset($reports[$product->cod]['customers_count']['n_family_total']))
                        $reports[$product->cod]['customers_count']['n_family_total'] = 0;

                    $reports[$product->cod]['product'] = $product;
                    $reports[$product->cod]['customers'][] = $customer_obj;
                    $reports[$product->cod]['customers_count'] = array(
                        'n_family' => count($reports[$product->cod]['customers']),
                        'n_family_total' => $customer_obj->family_number + $reports[$product->cod]['customers_count']['n_family_total']
                    );

                }

            }

        }

        return view('report.list', [
            'reports' => $reports
        ]);
    }

}
