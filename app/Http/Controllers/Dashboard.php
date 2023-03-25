<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
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
        $products = Product::count();

        $customers = Customer::count();

        $orders = Order::count();

        $orders_today = Order::with('customer')
            /*->where('date', 'LIKE', date('Y-m-d') . '%')*/
            ->orderBy('id', 'DESC')
            ->take(5)
            ->get();

        $points = Order::select([
            DB::raw('SUM(points) AS points_total'),
        ])->first();

        return Inertia::render('Dashboard', [
            'products_count' => $products,
            'customers_count' => $customers,
            'orders_count' => $orders,
            'points_count' => $points->points_total,
            'orders_today' => $orders_today,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }
}
