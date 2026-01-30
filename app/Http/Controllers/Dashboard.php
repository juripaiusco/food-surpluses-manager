<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class Dashboard extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /*ini_set('memory_limit', '1024M');

        // FIX Saved Order
        $orders = Order::orderBy('id', 'DESC')
            ->get();

        foreach ($orders as $order) {

            $json_customer = json_decode($order->json_customer);
            unset($json_customer->order);

            $order_save = Order::find($order->id);
            $order_save->date = $order->date;
            $order_save->json_customer = json_encode($json_customer);
            $order_save->save();
        }*/

        // Ultimo ordine
        $order_latest = Order::select('date')
            ->orderBy('date', 'DESC')
            ->first();

        if ($order_latest) {
            // Formatto la data dell'ultimo ordine
            $orders_latest_date = Carbon::parse($order_latest->date)->format('Y-m-d');
            $orders_latest_date_string = Carbon::parse($order_latest->date)->format('d/m/Y');

            // ===============================================================
            request()->validate([
                'orderby' => ['in:date,reference,customer_name,points'],
                'ordertype' => ['in:asc,desc']
            ]);

            // Prendo i dati degli ordini dell'ultimo giorno di vendita
            $order_last_day = Order::with('customer')
                ->where('date', 'LIKE', $orders_latest_date . '%')
                ->select()
                ->addSelect('json_customer->name AS customer_firstname')
                ->addSelect('json_customer->surname AS customer_lastname')
                ->addSelect(DB::raw(
                    'CONCAT(
                        JSON_VALUE(json_customer, \'$.surname\'), \' \', JSON_VALUE(json_customer, \'$.name\')
                    ) AS customer_name'
                ));

            // Filtro i dati degli ordini dell'ultimo giorno
            if (request('s')) {
                $order_last_day->where(function ($q) {

                    $q->orWhere('reference', 'like', '%' . request('s') . '%');
                    $q->orWhere('json_customer', 'like', '%' . request('s') . '%');
                    $q->orWhere('points', 'like', '%' . request('s') . '%');

                });
            }

            // Ordino i dati degli ordini dell'ultimo giorno
            if (request('orderby') && request('ordertype')) {
                $order_last_day->orderby(request('orderby'), strtoupper(request('ordertype')));
            }

            // Prendo i dati degli ordini dell'ultimo giorno
            $order_last_day_data = $order_last_day->get();
            // ===============================================================

            // Conto quanti ORDINI fatti l'ultimo giorno di vendita
            $orders_count = $order_last_day_data->count();

            // Conto i PRODOTTI venduti l'ultimo giorno di vendita
            $products_count = 0;
            foreach ($order_last_day_data as $order) {
                if (is_array(json_decode($order->json_products))) {
                    $products_count += count(json_decode($order->json_products));
                }
            }

            // Conto quanti PUNTI usati l'ultimo giorno di vendita
            $points_count = 0;
            foreach ($order_last_day_data as $order) {
                $points_count += $order->points;
            }

            // Conto i CLIENTI dell'ultimo giorno di vendita
            $people_count = 0;
            $people_count_array = array();
            foreach ($order_last_day_data as $order) {
                if (isset($order->customer->id) && !isset($people_count_array[$order->customer->id])) {
                    $people_count_array[$order->customer->id] = $order->customer->family_number;
                    $people_count += $people_count_array[$order->customer->id];
                }
            }

            // Prendo i dati impaginati degli ordini dell'ultimo giorno
            $order_last_day = $order_last_day->select([
                'date',
                'reference',
                'points',
            ]);
            $order_last_day = $order_last_day->addSelect(DB::raw(
                'CONCAT(
                        JSON_VALUE(json_customer, \'$.surname\'), \' \', JSON_VALUE(json_customer, \'$.name\')
                    ) AS customer_name'
            ));
            $order_last_day_data_paginate = $order_last_day->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        } else {

            $products_count = 0;
            $people_count = 0;
            $orders_count = 0;
            $points_count = 0;
            $order_last_day_data_paginate = [];
            $orders_latest_date_string = '';
        }

        return Inertia::render('Dashboard', [
            'products_count' => $products_count,
            'people_count' => $people_count,
            'orders_count' => $orders_count,
            'points_count' => $points_count,
            'orders_lastday' => $order_last_day_data_paginate,
            'orders_latest_day_string' => $orders_latest_date_string,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }
}
