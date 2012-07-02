<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once BASEPATH . '../application/controllers/basewebappcontroller.php';

class baseendusercontroller extends basewebappcontroller {
	
	function __construct() {
		parent::__construct();
	}

	protected function _render($page, &$data = array(), $returnAsString = false) {
		$data['subsystem'] = 'enduser';
		parent::_render($page, $data, $returnAsString);
	}

    private function initRender(&$data, $page){
    }

    protected function render($page, $data = array())
    {
        $this->initRender($data, $page);
        $this->parser->parse("enduser/$page.html", $data);
        return;
    }



}
