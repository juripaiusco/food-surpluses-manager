<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class Setting extends Controller
{
    var $array_settings_var_name = array(
        'shop_btn' => '',
        'shop_ctrl_points' => '',
    );
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->get();

        return Inertia::render('Settings/Form', [
            'data' => $data,
            'msg' => Session::get('msg')
        ]);
    }

    public function get()
    {
        $settings = \App\Models\Setting::query();
        $settings->select();
        $settings = $settings->get();

        $data = $this->array_settings_var_name;

        foreach ($settings as $setting) {

            $data[$setting->name] = $setting->value;

        }

        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        unset($request['created_at']);
        unset($request['updated_at']);

        foreach ($request->all() as $name => $value) {

            $setting = \App\Models\Setting::query();
            $setting->where('name', $name);

            if ($setting->count() >= 1) {

                $setting->update(['value' => $value]);

            } else {

                $setting->insert([
                    'name' => $name,
                    'value' => $value,
                ]);
            }

        }

        return to_route('settings.index')->with('msg', 'Impostazioni salvate');
    }
}
