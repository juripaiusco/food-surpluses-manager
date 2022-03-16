<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Retail extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $s = $request->input('s');

        $retails = \App\Model\Retail::orderBy('name')
            ->where('name', 'LIKE', '%' . $s . '%')
            ->paginate(15);

        return view('retails.list', [
            'retails' => $retails,
            's' => $s
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('retails.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $retail = new \App\Model\Retail();

        $retail->name = $request->input('name');
        $retail->zip = $request->input('zip');
        $retail->address = $request->input('address');
        $retail->city = $request->input('city');

        $retail->save();

        return redirect()->route('retails');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $retail = \App\Model\Retail::find($id);

        return view('retails.form', [
            'retail' => $retail
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $retail = \App\Model\Retail::find($id);

        $retail->name = $request->input('name');
        $retail->zip = $request->input('zip');
        $retail->address = $request->input('address');
        $retail->city = $request->input('city');

        $retail->save();

        return redirect()->route('retails');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Model\Retail::destroy($id);

        return redirect()->route('retails');
    }
}
