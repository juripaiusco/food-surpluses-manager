<?php

namespace App\Http\Controllers;

use App\Models\Retail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class User extends Controller
{
    var $modules_array = array(
        'home' => [
            'title' => 'Dashboard'
        ],

        'shop' => [
            'title' => 'Cassa'
        ],

        'orders' => [
            'title' => 'Ordini',
            'single' => 'ordine'
        ],

        'products' => [
            'title' => 'Prodotti',
            'single' => 'prodotto'
        ],

        'store' => [
            'title' => 'Magazzino'
        ],

        'customers' => [
            'title' => 'Assistiti',
            'single' => 'assistito'
        ],

        'retails' => [
            'title' => 'Negozi',
            'single' => 'negozio'
        ],

        'report' => [
            'title' => 'Report'
        ],

        'users' => [
            'title' => 'Volontari',
            'single' => 'volontario'
        ]
    );

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Query data
        $users = \App\Models\User::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:name,email,modules_list,retails_list'],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $users->where(function ($q) {

                $q->orWhere('name', 'like', '%' . request('s') . '%');
                $q->orWhere('email', 'like', '%' . request('s') . '%');

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $users->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $users = $users->select();

        // Converto il compo json_modules in campi virtuali della tabella
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        foreach ($this->modules_array as $k => $module) {

            $users = $users->addSelect(
                'json_modules->' . $k . ' AS mod_' . $k
            );

        }

        $sql_mod_array = array();
        foreach ($this->modules_array as $k => $module) {
            $sql_mod_array[] = 'IF (JSON_VALUE(json_modules, \'$.' . $k . '\') = "on", "' . $module['title'] . '", "")';
        }

        $users = $users->addSelect(DB::raw(
            'CONCAT(
            ' . implode(', \' | \', ', $sql_mod_array) . '
            ) as modules_list'
        ));
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        // Converto il compo json_retails in campi virtuali della tabella
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        $retails = Retail::get();

        $sql_retails_array = array();
        foreach ($retails as $retail) {
            $sql_retails_array[] = 'IF (JSON_VALUE(json_retails, \'$.' . $retail->id . '\') = "on", "' . $retail->name . '", "")';
        }

        $users = $users->addSelect(DB::raw(
            'CONCAT(
            ' . implode(', \' | \', ', $sql_retails_array) . '
            ) as retails_list'
        ));
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $users = $users->paginate(env('VIEWS_PAGINATE'));

        return Inertia::render('Users/List', [
            'users' => $users,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
