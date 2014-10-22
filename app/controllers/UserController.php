<?php

class UserController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function indexjson()
	{
	    $users = User::all();
		return $users->toJSON();
	}
	
	public function login()
	{
	    $user = User::whereRaw('username = ? and password = ?', array($_REQUEST['username'], md5($_REQUEST['password'])))->first(array('id', 'realname', 'email'));
	    
	    if ($user){
	        $vms = $user->vms()->get(array('id', 'name', 'uuid', 'os', 'cpu', 'memory', 'spiceport'));
	        $rt = array(
	           'user' => $user,
	           'vms' => $vms
	        );
	        return $rt;
	    }
	    return 0;
	}

}
