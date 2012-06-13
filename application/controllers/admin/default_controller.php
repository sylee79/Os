<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseadmincontroller.php";

class default_controller extends baseadmincontroller {

	function __construct() {
		parent::__construct();
	}
	
	public function index()	{
		$this->_render('index');
	}
	
	public function login() {
		echo "Login admin controller";
	}
}
