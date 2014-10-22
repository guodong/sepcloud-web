<?php

class IndexController extends BaseController {


	public function index()
	{
// 	    if (!isset($_SESSION['admin'])){
// 	        return Redirect::to('login');
// 	    }
		return View::make('index');
	}

	public function adminlogin()
	{
	    $user = Admin::whereRaw('username = ? and password = ?', array($_REQUEST['username'], md5($_REQUEST['password'])))->first(array('id', 'username'));
	    if ($user){
	        return $user->toJSON();
	    }
	    return 0;
	}
}
