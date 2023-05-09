<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use function GuzzleHttp\Promise\all;

class Product extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function productsGet()
    {
        $request_validate_array = [
            'monitoring_buy',
            'cod',
            'type',
            'name',
            'kg_total',
            'amount_total',
            'points',
        ];

        // Query data
        $data = \App\Models\Product::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:' . implode(',', $request_validate_array)],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $data->where(function ($q) use ($request_validate_array) {

                foreach ($request_validate_array as $field) {
                    $q->orWhere($field, 'like', '%' . request('s') . '%');
                }

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->with('category');
        $data = $data->whereNull('json_box');
        $data = $data->select();
        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Products/List', [
            'data' => $this->productsGet(),
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Creo un oggetto di dati vuoto
        $columns = Schema::getColumnListing('products');

        $products_array = array();
        foreach ($columns as $products_field) {
            $products_array[$products_field] = null;
        }

        unset($products_array['id']);
        unset($products_array['deleted_at']);
        unset($products_array['created_at']);
        unset($products_array['updated_at']);

        $data = json_decode(json_encode($products_array), true);

        $categories = Category::orderBy('name')
            ->get();

        return Inertia::render('Products/Form', [
            'data' => $data,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cod'       => ['required', 'unique:products', 'min:7'],
            'type'      => ['required'],
            'name'      => ['required'],
            'points'    => ['required'],
            'amount'    => ['required'],
        ]);

        $product = new \App\Models\Product();

        $product->cod = $request->input('cod');
        $product->category_id = $request->input('category_id');
        $product->type = $request->input('type');
        $product->monitoring_buy = $request->input('monitoring_buy');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->points = $request->input('points');
        $product->kg = $request->input('kg');
        $product->amount = $request->input('amount');

        $product->save();

        return to_route('products.index');
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
        $data = \App\Models\Product::with('store')
            ->with('store.user')
            ->with('store.customer')
            ->with('store.order')
            ->find($id);

        $categories = Category::orderBy('name')
            ->get();

        return Inertia::render('Products/Form', [
            'data' => $data,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'cod'       => ['required', 'unique:products,cod,' . $id, 'min:7'],
            'type'      => ['required'],
            'name'      => ['required'],
            'points'    => ['required'],
            'amount'    => ['required'],
        ]);

        $product = \App\Models\Product::find($id);

        $product->cod = $request->input('cod');
        $product->category_id = $request->input('category_id');
        $product->type = $request->input('type');
        $product->monitoring_buy = $request->input('monitoring_buy');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->points = $request->input('points');
        $product->kg = $request->input('kg');
        $product->amount = $request->input('amount');

        $product->save();

        return to_route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Product::destroy($id);

        return to_route('products.index');
    }

    public function category_index()
    {
        $request_validate_array = [
            'name',
            'limit',
        ];

        // Query data
        $data = \App\Models\Category::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:' . implode(',', $request_validate_array)],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $data->where(function ($q) use ($request_validate_array) {

                foreach ($request_validate_array as $field) {
                    $q->orWhere($field, 'like', '%' . request('s') . '%');
                }

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->select();
        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return Inertia::render('Products/Categories/List', [
            'data' => $data,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }

    public function category_create()
    {
        // Creo un oggetto di dati vuoto
        $columns = Schema::getColumnListing('categories');

        $categories_array = array();
        foreach ($columns as $categories_field) {
            $categories_array[$categories_field] = null;
        }

        unset($categories_array['id']);
        unset($categories_array['deleted_at']);
        unset($categories_array['created_at']);
        unset($categories_array['updated_at']);

        $data = json_decode(json_encode($categories_array), true);

        return Inertia::render('Products/Categories/Form', [
            'data' => $data
        ]);
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name'      => ['required'],
        ]);

        $category = new \App\Models\Category();

        $category->fill($request->all());

        $category->save();

        return to_route('products.categories.index');
    }

    public function category_edit(string $id)
    {
        $data = \App\Models\Category::find($id);

        return Inertia::render('Products/Categories/Form', [
            'data' => $data
        ]);
    }

    public function category_update(Request $request, string $id)
    {
        $request->validate([
            'name'      => ['required'],
        ]);

        $category = \App\Models\Category::find($id);

        $category->fill($request->all());

        $category->save();

        return to_route('products.categories.index');
    }

    public function category_destroy(string $id)
    {
        \App\Models\Category::destroy($id);

        return to_route('products.categories.index');
    }

    public function box_index(Request $request)
    {
        // Resetto la sessione per il contenuto della scatola
        $this->boxActionInit($request);

        $request_validate_array = [
            'name',
            'limit',
        ];

        // Query data
        $data = \App\Models\Product::query();

        // Request validate
        request()->validate([
            'orderby' => ['in:' . implode(',', $request_validate_array)],
            'ordertype' => ['in:asc,desc']
        ]);

        // Filtro RICERCA
        if (request('s')) {
            $data->where(function ($q) use ($request_validate_array) {

                foreach ($request_validate_array as $field) {
                    $q->orWhere($field, 'like', '%' . request('s') . '%');
                }

            });
        }

        // Filtro ORDINAMENTO
        if (request('orderby') && request('ordertype')) {
            $data->orderby(request('orderby'), strtoupper(request('ordertype')));
        }

        $data = $data->where('json_box', '!=', '');
        $data = $data->select();
        $data = $data->paginate(env('VIEWS_PAGINATE'))->withQueryString();

        return Inertia::render('Products/Box/List', [
            'data' => $data,
            'filters' => request()->all(['s', 'orderby', 'ordertype'])
        ]);
    }

    public function box_create(Request $request)
    {
        $this->boxAction($request);

        // Creo un oggetto di dati vuoto
        $columns = Schema::getColumnListing('products');

        $products_array = array();
        foreach ($columns as $products_field) {
            $products_array[$products_field] = null;
        }

        unset($products_array['id']);
        unset($products_array['deleted_at']);
        unset($products_array['created_at']);
        unset($products_array['updated_at']);

        $data = json_decode(json_encode($products_array), true);

        return Inertia::render('Products/Box/Form', [
            'data' => $data,
            'products' => $this->productsGet(),
            'filters' => request()->all(['s', 'orderby', 'ordertype']),
            'create_url' => $request->input('currentUrl')
        ]);
    }

    public function box_store(Request $request)
    {
        $request->validate([
            'cod'       => ['required', 'unique:products', 'min:7'],
            'name'      => ['required'],
        ]);

        $product = new \App\Models\Product();

        $product->fill($request->all());
        $product->json_box = json_encode($request->session()->get('boxProducts'));
        $product->type = 'box';

        $product->save();

        return to_route('products.box.index');
    }

    public function box_edit(Request $request, $id)
    {
        $data = \App\Models\Product::find($id);

        $this->boxAction($request, json_decode($data->json_box));

        return Inertia::render('Products/Box/Form', [
            'data' => $data,
            'products' => $this->productsGet(),
            'filters' => request()->all(['s', 'orderby', 'ordertype']),
            'create_url' => $request->input('currentUrl') ? $request->input('currentUrl') : null
        ]);
    }

    public function box_update(Request $request, $id)
    {
        $request->validate([
            'cod'       => ['required', 'unique:products,cod,' . $id, 'min:7'],
            'name'      => ['required'],
        ]);

        $product = \App\Models\Product::find($id);

        $product->fill($request->all());
        $product->json_box = json_encode($request->session()->get('boxProducts'));
        $product->type = 'box';
        $product->points = $this->boxAction_getPoints($request->session()->get('boxProducts'));

        $product->save();

        return to_route('products.box.index');
    }

    public function box_destroy($id)
    {
        \App\Models\Product::destroy($id);

        return to_route('products.box.index');
    }

    public function boxActionInit(Request $request)
    {
        $request->session()->forget('boxProducts');
    }

    public function boxAction(Request $request, $data = array())
    {
        if (count($data) > 0) {

            $request->session()->put('boxProducts', $data);

        }

        $this->boxActionDel($request);
        $this->boxActionAdd($request);
    }

    public function boxActionAdd(Request $request)
    {
        if ($request->input('boxAddTo') == true) {

            $product = \App\Models\Product::find($request->input('product_id'));
            $product->index = $request->session()->get('boxProducts') ? array_key_last($request->session()->get('boxProducts')) + 1 : 0;

            $request->session()->push('boxProducts', $product);

            Inertia::share('boxProducts', $request->session()->get('boxProducts'));

            return true;
        }

        Inertia::share('boxProducts', $request->session()->get('boxProducts'));

        return false;
    }

    public function boxActionDel(Request $request)
    {
        if ($request->input('boxRemove') == true) {

            $boxProducts_array = $request->session()->get('boxProducts');

            unset($boxProducts_array[$request->input('product_index')]);

            $boxProducts_array = array_values($boxProducts_array);

            foreach ($boxProducts_array as $k => $product) {
                $boxProducts_array[$k]->index = $k;
            }

            $request->session()->put('boxProducts', $boxProducts_array);

            Inertia::share('boxProducts', $request->session()->get('boxProducts'));

        }
    }

    public function boxAction_getPoints($box_data)
    {
        $points = 0;

        foreach ($box_data as $d) {

            $points += $d->points;

        }

        return $points;
    }
}
