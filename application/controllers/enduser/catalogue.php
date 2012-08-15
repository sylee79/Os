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
        $variation=$this->libenduser->getProductVariation($data['product']['id']);
        $data['productVariation']=array();
        foreach($variation as $entry){
            if($entry['variation_type_en'] === 'standard' && $entry['variation_value_en'] === 'standard') continue;
            array_push($data['productVariation'], array( 'id' => $entry['id']
                                                        ,'variation_type_en'=>$entry['variation_type_en']
                                                        , 'variation_value_en'=>explode(',', $entry['variation_value_en'])));
        }
        if( $this->session->userdata('added2cart')){
            $this->session->unset_userdata('added2cart');
            $data['message']='Item has added into shopping cart!';
        }
		$this->render('product_detail', $data);
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
