<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "baseadmincontroller.php";

class admin extends baseadmincontroller {

	function __construct() {
		parent::__construct();
	}

	public function index()	{
        $data["error_msg"] = "";
		$this->render('index', $data);
	}

    public function product($action=false, $id=false) {
        $data["error_msg"] = "";
        if(!$action) $action = $this->input->post('action');

        switch($action){
            case 'create & new':
                $product = $this->libadmin->createProduct($this->myUser(), $_POST, json_decode(urldecode($_POST['product_variation_json']), true));

            case 'new':
                $data['selectionManufacturer'] = $this->libadmin->getManufacturer();
                $data['selectionCategory'] = $this->libadmin->getProductCategory();
                $data['selectionPriority'] = $this->libadmin->getPriorityList();
                $data['productVariation'] = $this->libadmin->getDefaultVariation();
                $data['productVariationJson'] = urlencode(json_encode($data['productVariation'],true));
                if(!isset($data['priority'])) $data['priority']=10;
                $this->render('product/createProduct', $data);
                break;

            case 'create & add image':
                $data['product'] = $this->libadmin->createProduct($this->myUser(), $_POST, json_decode(urldecode($_POST['product_variation_json']), true));
                $data['product_id'] = $data['product']->id;

            case 'edit image':
                $this->libadmin->mergeData($data, $_POST);
                $data['productImages'] = $this->libadmin->getProductImages($data['product_id']);
                $data['product_title_en'] = $data['product']['title_en'];
                $data['image_url']= $data['productImages'][0]['image_url'];
                $this->render('product/productImage', $data);

                break;

            case 'add variation >>':
                $data['selectionManufacturer'] = $this->libadmin->getManufacturer();
                $data['selectionCategory'] = $this->libadmin->getProductCategory();
                $data['selectionPriority'] = $this->libadmin->getPriorityList();
                $this->libadmin->mergeData($data, $_POST);
                $data['productVariation'] = $this->libadmin->addVariation($data);
                $data['productVariationJson'] = urlencode(json_encode($data['productVariation'],true));
                $this->render('product/createProduct', $data);
                break;

            case '<< remove variation':
                $data['selectionManufacturer'] = $this->libadmin->getManufacturer();
                $data['selectionCategory'] = $this->libadmin->getProductCategory();
                $data['selectionPriority'] = $this->libadmin->getPriorityList();
                $this->libadmin->mergeData($data, $_POST);
                $data['productVariation'] = $this->libadmin->removeVariation($data);
                $data['productVariationJson'] = urlencode(json_encode($data['productVariation'],true));
                $this->render('product/createProduct', $data);
                break;

            case 'edit':
                $data['selectionManufacturer'] = $this->libadmin->getManufacturer();
                $data['selectionCategory'] = $this->libadmin->getProductCategory();
                $data['selectionPriority'] = $this->libadmin->getPriorityList();
                $product = $this->libadmin->getProduct($id);
                if(!$product){
                    $this->render('index', $data);
                }
                $this->libadmin->mergeData($data, $product);
                $data['productVariation'] = $this->libadmin->getProductVariation($id);
                $data['productVariationJson'] = urlencode(json_encode($data['productVariation'],true));
                $this->render('product/editProduct', $data);
                return;


            case 'create & add image':
                echo json_encode($data);
                break;

            default:
                $q =
'select p.id as id, p.title_en as title_en, m.name as manufacturer_name, p.price as price, p.reference_price as reference_price
from product p left join manufacturer m on m.id=p.manufacturer_id
where p.is_deleted = 0';
				$prodInfo = $this->getDBData($q);
				foreach ($prodInfo as &$prod) {
					$prod["links"] = "<a href='/admin/product/edit/" . $prod["id"] . "'>Edit</a>";
					$prodId = $prod['id'];
					$prod["html_snippet"] = <<<HTML
<input type='radio' name='selected_product' id='radio_product_$prodId' class='product_selector_radio'/>
HTML;
                }
				$data["product_list"] = $prodInfo;
				$data["product_list_headers"] = "EMPTY,Title,Manufacturer,Price,Ref. Price,Action";
				$data["product_list_keys"] = "html_snippet,title_en,manufacturer_name,price,reference_price,links";
                $this->render('product/product', $data, false);
                return;
        }
    }

}
