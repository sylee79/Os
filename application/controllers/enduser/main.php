<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseendusercontroller.php";

class main extends baseendusercontroller {
	
	function __construct() {
		parent::__construct();
	}

	public function index()	{
        $data = array();
        $data['productList']=$this->libenduser->getNewArrival();
		$this->render('main', $data);
	}

	public function login() {
		echo "Login enduser controller";
	}
}
