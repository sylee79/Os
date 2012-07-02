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

    public function product($action=false, $id=false, $data=array()) {
        if(!isset($data['error_msg']))
            $data["error_msg"] = "";
        if(!$action) $action = $this->input->post('action');

        switch($action){
            case 'deleteProduct':
                $this->libadmin->deleteProduct($id);
                $this->product();
                break;


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

            case 'save':
                $this->libadmin->saveProduct($this->input->post('product_id'), $this->myUser(), $_POST, json_decode(urldecode($_POST['product_variation_json']), true));
                $this->product('edit', $this->input->post('product_id'));
                break;

            case 'create & add image':
                $product = $this->libadmin->createProduct($this->myUser(), $_POST, json_decode(urldecode($_POST['product_variation_json']), true));
                $this->product('edit_image', $product->id);
                break;

            case 'updateImageDesc':
                $this->libadmin->updateImageDesc($this->input->post('image_id'), $this->input->post('description'));
                $this->product('edit image', $this->input->post('product_id'));
                break;

            case 'edit image':
                $data['product_id'] = $id?$id:$this->input->post('product_id');
                $data['product']=$this->libadmin->getProduct($data['product_id']);
                $data['productImages'] = $this->libadmin->getProductImages($data['product_id']);
                $data['product_title_en'] = $data['product']['title_en'];
                $data['image_url']= $data['productImages'][0]['image_url'];
                $this->render('product/productImage', $data, false);
                break;

            case 'deleteImage':
                $this->libadmin->deleteProductImage($this->input->post('image_id'));
                $this->product('edit image', $this->input->post('product_id'));
                break;

            case 'setMainPreview':
                $this->libadmin->setMainPreview($this->input->post('product_id'), $this->input->post('image_id'));
                $this->product('edit image', $this->input->post('product_id'));
                break;


            case 'upload':
            	$data['product_id'] = $this->input->post('product_id');
				if(!isset($_FILES['photoPath']) || !file_exists($_FILES['photoPath']['tmp_name']))
				{
					$data['message']='Please choose a image file';
					redirect ('admin/product');
					break;
				}
				$srcFile = $_FILES['photoPath']['tmp_name'];
				$file = $this->libadmin->getPrecropImage($srcFile, 640, 640);

				$data['temp_image'] = $file['filepath'];
				$data['image_width'] = $file['width'];
				$this->render('product/productImageCropping', $data, false);
				break;

			case 'cancel image':
           		$image = BASEPATH."../".$this->input->post('temp_image');
           		unlink($image);
                $this->product('edit image', $this->input->post('product_id'));
                break;

			case 'save image':
				$id = $this->input->post('product_id');
           		$image = BASEPATH."../".$this->input->post('temp_image');
            	$x = $this->input->post('x');
            	$y = $this->input->post('y');
            	$w = $this->input->post('w');
            	$h = $this->input->post('h');

            	$destURL = IMAGE_PATH.'product_'.$id.'_'.Common::generateRandomKey().'.jpg';
            	$destFile = BASEPATH."../".$destURL;
            	while(file_exists($destFile))
            	{
	            	$destURL = IMAGE_PATH.'product_'.$id.'_'.Common::generateRandomKey().'.jpg';
	            	$destFile = BASEPATH."../".$destURL;
            	}
                _d("CROP Image to $destFile");
            	Common::cropImage($image, $destFile, $x, $y, $w, $h, 600, 480);

                if(!file_exists($destFile)){
                    $this->product('edit image', $id, array('error_msg'=>'Image save failed'));
                    exit;
                }
            	unlink($image);

            	$this->libadmin->createProductImage($id, $destURL);

            	$this->product('edit image', $id);
				break;

            case 'add variation >>':
                $data['selectionManufacturer'] = $this->libadmin->getManufacturer();
                $data['selectionCategory'] = $this->libadmin->getProductCategory();
                $data['selectionPriority'] = $this->libadmin->getPriorityList();
                $this->libadmin->mergeData($data, $_POST);
                $data['productVariation'] = $this->libadmin->addVariation($data);
                $data['productVariationJson'] = urlencode(json_encode($data['productVariation'],true));
                if($_POST['product_id']){
                    $data['id']=$this->input->post('product_id');
                    $this->render('product/editProduct', $data);
                }
                else
                    $this->render('product/createProduct', $data);
                break;

            case '<< remove variation':
                $data['selectionManufacturer'] = $this->libadmin->getManufacturer();
                $data['selectionCategory'] = $this->libadmin->getProductCategory();
                $data['selectionPriority'] = $this->libadmin->getPriorityList();
                $this->libadmin->mergeData($data, $_POST);
                $data['productVariation'] = $this->libadmin->removeVariation($data);
                $data['productVariationJson'] = urlencode(json_encode($data['productVariation'],true));
                if($_POST['product_id']){
                    $data['id']=$this->input->post('product_id');
                    $this->render('product/editProduct', $data);
                }
                else
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
					$prod["links"] = "<a href='/admin/product/edit/" . $prod["id"] . "'>Edit</a>  "
                        ."<a href='/admin/product/deleteProduct/" . $prod["id"] . "'>Delete</a>";
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
