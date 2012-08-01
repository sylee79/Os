<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once BASEPATH . '../application/controllers/basewebappcontroller.php';

class baseendusercontroller extends basewebappcontroller {
	
	function __construct() {
		parent::__construct();
        $this->load->library('LibEndUser');
        $this->load->library('LibCart');
	}

	protected function _render($page, &$data = array(), $returnAsString = false) {
		$data['subsystem'] = 'enduser';
		parent::_render($page, $data, $returnAsString);
	}

    private function initRender(&$data, $page){
        $config =& get_config();

        $data['cartCount']=$this->libcart->getCartCount();
        $data['BASEURL']=$config['base_url'];
        $data['ajax_loader']=$data['BASEURL'].$config['ajax_loader'];
        switch($page){
            case 'main':
                $data['menu']=1;
                break;

            case 'order':
                $data['menu']=2;
                break;

            case 'category':
                if($data['categoryId']==2){
                    $data['menu']=4;
                }elseif($data['categoryId']==3){
                    $data['menu']=5;
                }else{
                    $data['menu']=3;
                }
                break;

            default:
                $data['menu']=0;
                break;
        }
    }

    protected function render($page, $data = array())
    {
        $this->initRender($data, $page);
        $this->parser->parse("enduser/$page.html", $data);
        return;
    }



}
