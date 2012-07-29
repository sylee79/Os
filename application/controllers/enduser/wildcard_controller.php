<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseendusercontroller.php";

class wildcard_controller extends baseendusercontroller {
	
	function __construct() {
		parent::__construct();
	}

	public function index($params=false)	{
		echo "Hello End User WILDCARD controller $params";
	}

    public function orderDetails(){
        $data = array();
        $this->render('order', $data);
    }

}
