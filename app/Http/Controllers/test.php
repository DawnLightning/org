<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use DB;
use Illuminate\Http\Response;
use Jtools\Uchelper;
use Jtools\Capi;
use Illuminate\Support\Facades\Input;

class test extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    	$url = asset('img/photo.jpg');
    	//echo $url;
    	//$uc=Uchelper::getInstance();
    	
    	//$user=Auth::check();
    	//$config=config('database.connections.mysql');
    	//var_dump($config);
    	//var_dump($_SGLOBALS);
    	
    	//var_dump($uc);
    	//exit;
    	//return ()->json(__DIR__);
    	//echo action('Auth\AuthController@getLogin');
    	capi_showmessage_by_data('do_success',0,$url);
    	
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
    	//redirect()->withInput();
        //$query = DB::select('SELECT * FROM users');
    	$url = asset('index2.php');
    	$all=Input::all();
    	
    	echo $url;
        var_dump($_SERVER);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
