<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class basewebappcontroller extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!defined('CDN_PREFIX')) {
			define('CDN_PREFIX', '/res');
		}
        $this->load->helper("clog");
	}

	protected function _render($page, &$data = array(), $returnAsString = false) {
		$data['CDN_PREFIX'] = CDN_PREFIX;
		$subsystem = $data['subsystem'];
		$htmlFile = $subsystem . '/' . $page . '.html';

        if ($returnAsString) {
            return $this->parser->parse($htmlFile, $data, TRUE);
        } else {
		    $this->output->set_header('Content-Type: text/html; charset=utf-8');
            $this->parser->parse($htmlFile, $data);
        }
	}
}
