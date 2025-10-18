<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class Customer extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

        return Inertia::render('Customers/List', [
            'data' => $data,
            'filters' => request()->all(['number', 's', 'orderby', 'ordertype', 'filters']),
            'msg_alert' => request()->get('msg_alert'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Creo un oggetto di dati vuoto
        $columns = Schema::getColumnListing('customers');

        $customers_array = array();
        foreach ($columns as $customers_field) {
            $customers_array[$customers_field] = null;
        }

        unset($customers_array['id']);
        unset($customers_array['deleted_at']);
        unset($customers_array['created_at']);
        unset($customers_array['updated_at']);

        $customers_array['saveRedirect'] = Redirect::back()->getTargetUrl();

        $data = json_decode(json_encode($customers_array), true);

        return Inertia::render('Customers/Form', [
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'number'        => ['required', 'unique:customers'],
            'cod'           => ['required', 'unique:customers', 'min:8'],
            'name'          => ['required'],
            'surname'       => ['required'],
            'family_number' => ['required'],
            'points'        => ['required'],
            'points_renew'  => ['required'],
        ]);

        $saveRedirect = $request['saveRedirect'];

        unset($request['saveRedirect']);

        $customer = new \App\Models\Customer();

        $customer->fill($request->all());

        $customer->save();

        return Redirect::to($saveRedirect);
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
        $data = \App\Models\Customer::with(['order' => function ($q) {
            $q->select([
                'id',
                'reference',
                'user_id',
                'customer_id',
                'retail_id',
                'points',
                'date',
            ]);
        }])
            ->find($id);

        $data->saveRedirect = Redirect::back()->getTargetUrl();

        return Inertia::render('Customers/Form', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'number'        => ['required', 'unique:customers,number,' . $id],
            'cod'           => ['required', 'unique:customers,cod,' . $id, 'min:8'],
            'name'          => ['required'],
            'surname'       => ['required'],
            'family_number' => ['required'],
            'points'        => ['required'],
            'points_renew'  => ['required'],
        ]);

        $saveRedirect = $request['saveRedirect'];

        unset($request['order']);
        unset($request['created_at']);
        unset($request['updated_at']);
        unset($request['saveRedirect']);

        $customer = \App\Models\Customer::find($id);

        $customer->fill($request->all());

        $customer->save();

        return Redirect::to($saveRedirect);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Customer::destroy($id);

        return to_route('customers.index');
    }

    public function view_reception(Request $request, string $id)
    {
        $customer = \App\Models\Customer::find($id);
        $msg_alert = false;

        // Se view_reception è 0 significa che si vuole aggiungere il customer,
        // quindi dev'essere fatto un controllo sul numero massimo
        if ($customer->view_reception == 0) {

            $setting = new Setting();
            $n_max_assistiti = intval($setting->get()['n_max_assistiti']);

            if ($n_max_assistiti) {

                $assistiti_count = \App\Models\Customer::query()
                    ->where('view_reception', 1)
                    ->count();

                if ($assistiti_count >= $n_max_assistiti) {
//                    dd($assistiti_count, $n_max_assistiti);
                    $msg_alert = true;
                    $customer->view_reception = 1;
                }
            }

        }

        $customer->view_reception = $customer->view_reception == 1 ? 0 : 1;
        $customer->save();

        return to_route(
            'customers.index',
            array_merge(
                $request->all(),
                ['msg_alert' => $msg_alert]
            )
        );
    }

    public function active(Request $request, string $id)
    {
        $customer = \App\Models\Customer::find($id);

        $customer->active = $customer->active == 1 ? 0 : 1;

        $customer->save();

        return to_route('customers.index', $request->all());
    }

    public function view_reception_reset()
    {
        $customers = \App\Models\Customer::query();
        $customers->update(['view_reception' => 0]);
    }

    public function points_renew()
    {
        DB::table('customers')->update([
            'points' => DB::raw('points_renew')
        ]);
    }
}
