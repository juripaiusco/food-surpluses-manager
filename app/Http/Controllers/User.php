<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

class User extends Controller
{
    var $modules_array = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->modules_array = Lang::get('layout');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = \App\Model\User::paginate(15);

        return view('users.list', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $retails = \App\Model\Retail::get();

        return view('users.form', [
            'modules_array' => $this->modules_array,
            'retails' => $retails
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new \App\Model\User();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->json_modules = json_encode($request->input('modules'));

        $user->save();

        return redirect()->route('users');
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
        $user = \App\Model\User::find($id);
        $retails = \App\Model\Retail::get();

        return view('users.form', [
            'user' => $user,
            'modules' => $this->modules_array,
            'modules_user' => json_decode($user->json_modules, true),
            'retails' => $retails,
            'retails_user' => json_decode($user->json_retails, true)
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
        $user = \App\Model\User::find($id);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->json_modules = json_encode($request->input('modules'));

        $retails[$request->input('retails')] = 'on';

        $user->json_retails = json_encode($retails);

        $user->save();

        return redirect()->route('users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \App\Model\User::destroy($id);

        return redirect()->route('users');
    }
}
