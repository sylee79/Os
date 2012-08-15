<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class defaultcontroller extends CI_Controller {
	public function index()
	{
        $config =& get_config();
        redirect($config['enduser_url']);
	}
}
