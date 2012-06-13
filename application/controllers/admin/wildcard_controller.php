<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseadmincontroller.php";

class wildcard_controller extends baseadmincontroller {

	function __construct() {
		parent::__construct();
	}

	public function index()	{
		echo "Hello  Admin WILDCARD controller";
	}
}
