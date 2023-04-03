<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class Store extends Controller
{
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
}
