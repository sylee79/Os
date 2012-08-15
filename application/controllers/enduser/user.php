<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseendusercontroller.php";



class user extends baseendusercontroller {

	function __construct() {
		parent::__construct();
        $this->load->library('LibCart');
        $this->load->library('Session');
	}

    function add2Cart()
    {
        _d("DATA: :".json_encode($_POST));
        if(!isset($_POST['product_id']) || !isset($_POST['quantity'])) return false;
        $variation = array();
        foreach($_POST as $key => $value){
            if(FALSE === stristr($key, 'variation_')) continue;

            $variation[substr($key,10)]=$value;
        }
        $this->libcart->add2Cart($_POST['product_id'], $variation, $_POST['quantity']);
        if(isset($_POST['product_link'])){
            $this->session->set_userdata('added2cart', true);
            redirect($_POST['product_link']);
        }
        $this->showCart();
        return true;
    }

    function checkout_step1()
    {
        if($this->libcart->getCartCount() < 1){
            $this->showCart();
        }
        elseif(isset($_POST['clear'])){
            $this->clearCart();
        }else{
            $this->render('checkout_form', $this->libcart->getUserDetails());
        }
    }

    function checkout_step2(){
        $userDetails = $this->libcart->saveUserDetails();
        $data = $this->libcart->getCartItems();
        $data = array_merge($userDetails, $data);
        $this->render('review_details', $data);
    }

    function checkout(){
        if($this->input->post('back2Cart') || $this->libcart->getCartCount() < 1){
            $this->showCart();
        }else{
            $data = $this->libcart->getUserDetails();
            if(!$data['buyer']){
                $this->showCart();
            }else{
                $data = array_merge( $this->libcart->getCartItems(), $data);
                $this->render('order_details', $data);
                $data['for_email']=1;
                $content = $this->render('order_details', $data, true);
                $this->libcart->sendEmail("sy_lee79@yahoo.com", 'Your Order of Little Precious', $content);
            }
        }
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


