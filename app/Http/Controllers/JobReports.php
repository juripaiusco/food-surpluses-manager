<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class JobReports extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request_validate_array = [
            'cod',
            'number',
            'customer_name',
            'name',
            'surname',
            'address',
            'family_number',
            'points',
            'phone',
        ];

        // Query data
        $data = \App\Models\Customer::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:' . implode(',', $request_validate_array)],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $data->where(function ($q) use ($request_validate_array) {

                foreach ($request_validate_array as $field) {
                    if ($field != 'customer_name') {
                        $q->orWhere($field, 'like', '%' . request('s') . '%');
                    }
                }

            });
        }

        // Filtro RICERCA Numero Assistito
        if (request('number')) {
            $data->where('number', request('number'));
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->with('order');

        if (request('filters') == 'no-order-3-months') {

            $data = $data->leftJoin(
                'orders', 'customers.id', '=', 'orders.customer_id'
            );

            $data = $data->select([
                'customers.id',
                'customers.active',
                'customers.view_reception',
                'customers.cod',
                'customers.number',
                'customers.name',
                'customers.surname',
                'customers.name_delegato',
                'customers.address',
                'customers.city',
                'customers.provincia',
                'customers.phone',
                'customers.family_number',
                'customers.points',
                'customers.points_renew',
                'customers.note',
                'customers.note_alert',
                'customers.b1',
                'customers.b2',
                'customers.b3',
                'customers.char1',
                'customers.char2',
                'customers.char3',
                'customers.c_group',
                'customers.channel',
                'customers.created_at',
                'customers.updated_at',
                'orders.reference AS order_reference',
                'orders.date AS order_date',
            ]);

            $data = $data->addSelect(DB::raw('
                CONCAT(surname, \' \', name) AS customer_name
            '));

            $data = $data->where(
                'orders.date',
                '>=',
                date('Y-m-d 00:00:00', strtotime('-3 months'))
            );

            $data = $data->groupBy('customers.id');

            $array_id_not = array();
            foreach ($data->get() as $d) {
                $array_id_not[] = $d->id;
            }

            $data = \App\Models\Customer::query();
            $data = $data->whereNotIn('id', $array_id_not);
            $data = $data->select();

            // Filtro ORDINAMENTO
            if (request('orderby') && request('ordertype')) {
                $data->orderby(request('orderby'), strtoupper(request('ordertype')));
            }

        } else {

            $data = $data->select();
        }

        $data = $data->addSelect(DB::raw('
            CONCAT(surname, \' \', name) AS customer_name
        '));
        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return Inertia::render('JobsReports/List', [
            'data' => $data,
            'filters' => request()->all(['number', 's', 'orderby', 'ordertype', 'filters'])
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
