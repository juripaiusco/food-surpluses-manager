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
        // Ultimo ordine
        $order_latest = Order::all()->last();

        // Formatto la data dell'ultimo ordine
        $order_latest_day = Carbon::parse($order_latest->date)->format('Y-m-d');
        $order_latest_day_string = Carbon::parse($order_latest->date)->format('d/m/Y');

        // Prendo i dati degli ordini dell'ultimo giorno di vendita
        $order_last_day = Order::with('customer')
            ->where('date', 'LIKE', $order_latest_day . '%');
        $order_last_day_data = $order_last_day->get();

        // Conto quanti ordini fatti l'ultimo giorno di vendita
        $orders_count = $order_last_day->count();

        // Conto i prodotti venduti l'ultimo giorno di vendita
        $products_count = 0;
        foreach ($order_last_day_data as $order) {
            $products_count += count(json_decode($order->json_products));
        }

        // Conto quanti punti usati l'ultimo giorno di vendita
        $points_count = $order_last_day
            ->select([
                DB::raw('SUM(points) AS points_total'),
            ])->first();

        // Conto i clienti dell'ultimo giorno di vendita
        $people_count = 0;
        foreach ($order_last_day_data as $order) {
            $people_count += $order->customer->family_number;
        }

        // - - - - - -

        request()->validate([
            'orderby' => ['in:date,reference,json_customer,points'],
            'ordertype' => ['in:asc,desc']
        ]);

        /*$orders_today = Order::with('customer')
            //->where('date', 'LIKE', date('Y-m-d') . '%')
            ->orderBy('id', 'DESC')
            ->take(5)
            ->get();*/

        $orders_today = Order::query();
        $orders_today->where('date', 'LIKE', $order_latest_day . '%');

        if (request('s')) {
            $orders_today->orWhere('reference', request('s'));
            $orders_today->orWhere('json_customer', 'like', '%' . request('s') . '%');
            $orders_today->orWhere('points', 'like', '%' . request('s') . '%');
        }

        if (request('orderby') && request('ordertype')) {
            $orders_today = $orders_today->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $orders_today = $orders_today->paginate(5)->withQueryString();

        // - - - - - -

        return Inertia::render('Dashboard', [
            'products_count' => $products_count,
            'people_count' => $people_count,
            'orders_count' => $orders_count,
            'points_count' => $points_count->points_total,
            'orders_today' => $orders_today,
            'order_latest_day_string' => $order_latest_day_string,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }
}
