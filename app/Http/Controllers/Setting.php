<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class Setting extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = \App\Models\Setting::query();
        $settings->select();

        $data = $settings->first();

        return Inertia::render('Settings/Form', [
            'data' => $data,
            'msg' => Session::get('msg')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        unset($request['created_at']);
        unset($request['updated_at']);

        $setting = \App\Models\Setting::find($id);

        $setting->fill($request->all());

        $setting->save();

        return to_route('settings.index')->with('msg', 'Impostazioni salvate');
    }
}
