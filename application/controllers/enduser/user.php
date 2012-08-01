<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseendusercontroller.php";



class user extends baseendusercontroller {

	function __construct() {
		parent::__construct();
        $this->load->library('LibCart');
	}

    function add2Cart()
    {
        if(!isset($_POST['product_id']) || !isset($_POST['quantity'])) return false;
        $variation = array();
        foreach($_POST as $key => $value){
            if(FALSE === stristr($key, 'variation_')) continue;

            $variation[substr($key,10)]=$value;
        }
        $this->libcart->add2Cart($_POST['product_id'], $variation, $_POST['quantity']);
        $this->showCart();
        return true;
    }

    function clearCart(){
        $this->libcart->clearCart();
        $this->showCart();
    }

    function test(){
        $this->libcart->add2Cart(1, array(1=>'X',2=>'blue'), 1);
    }

    function showCart(){
        $data = $this->libcart->getCartItems();
        $this->render('cart_details', $data);
    }

}


