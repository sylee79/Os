<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseendusercontroller.php";

class default_controller extends baseendusercontroller {
	
	function __construct() {
		parent::__construct();
	}

	public function index()	{
        $data = array();
		$this->render('main');
	}
	
	public function login() {
		echo "Login enduser controller";
	}
}
