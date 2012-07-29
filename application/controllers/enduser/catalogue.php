<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseendusercontroller.php";

class catalogue extends baseendusercontroller {
	
	function __construct() {
		parent::__construct();
	}

	public function product($shortcut)	{
        $data = array();
        $data['product']=$this->libenduser->getProductDetails($shortcut);
        if(!$data['product']){
            $this->notFound();
            exit;
        }
        $data['productImages']=$this->libenduser->getProductImages($data['product']['id']);
        $data['mainImage']=$data['productImages'][0];
        $data['productVariation']=$this->libenduser->getProductVariation($data['product']['id']);
		$this->render('product', $data);
	}

    public function category($categoryId){
        $data=array();
        $data['categoryId']=$categoryId;
        $this->libenduser->getCategoryProduct($data, $categoryId);
		$this->render('category', $data);
    }
	
	public function notFound() {
		echo "Login enduser controller";
	}
}
