<?php
require_once 'Constants.php';
require_once 'common.php';

class LibAdmin
{
    function deleteProduct($id){
        if(!$id)return false;

        $this->deleteImageOfProduct($id);

        $q = 'update product set is_deleted = 1 where id = '.$id;
        return $this->getDBData($q,false);
    }

    function getThumbnailConfig(){
        return array(
            NEW_ARRIVAL_RESOLUTION=>array('width'=>280, 'height'=>224)
            ,PRODUCT_DETAIL_RESOLUTION=>array('width'=>480, 'height'=>384)
        );
    }

    function createThumbnail($mainImageFile){
        $mainImageFile = substr($mainImageFile, 1);
        $mainFileName = substr($mainImageFile, 0, strlen($mainImageFile)-4);
        $thumbnailConfig = $this->getThumbnailConfig();
        $count=0;
        foreach ($thumbnailConfig as $key=>$resolution){
            $thumbnailFileName = $mainFileName.'-'.$key.'.jpg';
            if(file_exists($thumbnailFileName))continue;
            $thumbnail = new SimpleImage();
            $thumbnail->load($mainImageFile);
            $thumbnail->resizeKeepRatio($resolution['width'], $resolution['height']);
            $thumbnail->save($thumbnailFileName);
            ++$count;
        }
        return $count;
    }

    function createProduct($user, $data, $variationData)
    {
        try {

            $newProduct = new Product();
            $newProduct->added_by_oam_user_id = $user['id'];
            $newProduct->manufacturer_id = $data['manufacturer_id'];
            $newProduct->category_id = $data['category_id'];
            $newProduct->title_en = $data['title_en'];
            $newProduct->description_en = $data['description_en'];
            $newProduct->price = $data['price'];
            $newProduct->reference_price = $data['reference_price'];
            $newProduct->priority = $data['priority'];
            $newProduct->save();
            $newProduct->product_shortcut = 'product' . $newProduct->id;
            $newProduct->save();

            foreach ($variationData as $entry) {
                $newProductVariation = new ProductVariation();
                $newProductVariation->added_by_oam_user_id = $user['id'];
                $newProductVariation->product_id = $newProduct->id;
                $newProductVariation->variation_type_en = $entry['type_en'];
                $newProductVariation->variation_value_en = $entry['value_en'];
                $newProductVariation->save();
            }

            $newProductImage = new ProductImage();
            $newProductImage->product_id = $newProduct->id;
            $newProductImage->added_by_oam_user_id = $user['id'];
            $newProductImage->image_url = DEFAULT_PRODUCT_IMAGE;
            $newProductImage->is_main = 1;
            $newProductImage->save();

            return $newProduct;
        } catch (Exception $e) {
            $this->logException(__FUNCTION__, $e, (($newProduct && isset($newProduct['id'])) ? $newProduct['id'] : false), false);
            return false;
        }
    }

    function saveProduct($productId, $user, $data, $variationData)
    {
        try {
            $product = Doctrine::getTable('Product')->findOneById($productId);
            $product->added_by_oam_user_id = $user['id'];
            $product->manufacturer_id = $data['manufacturer_id'];
            $product->category_id = $data['category_id'];
            $product->title_en = $data['title_en'];
            $product->description_en = $data['description_en'];
            $product->price = $data['price'];
            $product->reference_price = $data['reference_price'];
            $product->priority = $data['priority'];
            $product->save();
        }catch(exception $e){}

        $q = 'update product_variation set is_deleted = 1, added_by_oam_user_id = '.$user['id'].' where product_id = '.$productId.' and is_deleted = 0';

        $this->getDBData($q, false);

        foreach ($variationData as $entry) {
            try{
                $productVariation = Doctrine::getTable('ProductVariation')->findOneByProductIdAndVariationTypeEn($productId, $entry['type_en']);
                if(!$productVariation){
                    $newProductVariation = new ProductVariation();
                    $newProductVariation->added_by_oam_user_id = $user['id'];
                    $newProductVariation->product_id = $productId;
                    $newProductVariation->variation_type_en = $entry['type_en'];
                    $newProductVariation->variation_value_en = $entry['value_en'];
                    $newProductVariation->save();
                    continue;
                }
                $productVariation->is_deleted = 0;
                $productVariation->variation_value_en = $entry['value_en'];
                $productVariation->save();
            }catch(exception $e){}
        }
    }

	function getPrecropImage($srcFile, $maxWidth=600, $maxHeight=480)
	{
		$image = new SimpleImage();
		$image->load($srcFile);
		$image->resizeKeepRatio($maxWidth, $maxHeight);
		$width = $image->getWidth();
		$image->save($srcFile);

		return array('width'=>$width, 'filepath' =>Common::uploadPrecropImage($srcFile));
	}

    function getThumbnailName($mainFile, $thumbnailKey){
        return substr($mainFile, 0, strlen($mainFile)-4).'-'.$thumbnailKey.'.jpg';
    }

    function deleteImageOfProduct($productId){
        $q = 'select image_url from product_image where product_id = '.$productId.' and is_deleted = 0';
        $result = $this->getDBData($q);
        foreach($result as $entry){
            if(false === stristr($entry['image_url'], 'default')){
                unlink(BASEPATH."../".$entry['image_url']);
                $thumbnailConfig = $this->getThumbnailConfig();
                foreach ($thumbnailConfig as $key=>$resolution){
                    unlink($this->getThumbnailName(BASEPATH."../".$entry['image_url'], $key));
                }
            }
        }
        $q = 'delete from product_image where product_id = '.$productId;
        return $this->getDBData($q, false);
    }

    function deleteProductImage($imageId){
        $q = 'select image_url from product_image where id = '.$imageId;
        $result = $this->getDBData($q);
        if(isset($result[0])){
            if(false === stristr($result[0]['image_url'], 'default')){
                unlink(BASEPATH."../".$result[0]['image_url']);
                $thumbnailConfig = $this->getThumbnailConfig();
                foreach ($thumbnailConfig as $key=>$resolution){
                    unlink($this->getThumbnailName(BASEPATH."../".$result[0]['image_url'], $key));
                }
            }
            $q = 'delete from product_image where id = '.$imageId;
            return $this->getDBData($q, false);
        }
        return false;
    }

    function setMainPreview($productId, $imageId){
        $q = 'update product_image set is_main = 0 where product_id = '.$productId.'; update product_image set is_main = 1 where id = '.$imageId;
        return $this->getDBData($q, false);
    }

	function createProductImage($id, $destFile)
	{
		try{
			$productImage = new ProductImage();
			$productImage->product_id = $id;
            $productImage->is_main = 0;

			$productImage->image_url = $destFile;
			$productImage->save();

            $this->createThumbnail($destFile);

            $q = "select id from product_image where product_id = $id and is_main = 1 and image_url like '%default.jpg'";

            $result = $this->getDBData($q);

            if(is_array($result) && count($result)>0){
                $productImage->is_main = 1;
                $productImage->save();
                $q = "delete from product_image where id = ".$result[0]['id'];
                $this->getDBData($q, false);
            }

			return true;
		}catch(Exception $e){
            $this->logException(__FUNCTION__, $e, $id, $destFile);
			return false;
		}
	}

    function updateImageDesc($imageId, $desc){
        return $this->getDBData('update product_image set description = \''.$desc.'\' where id = '.$imageId, false);
    }

    function getProductImages($productId){
        return $this->getDBData('select * from product_image where product_id = '.$productId.' order by is_main desc');
    }

    function getPriorityList()
    {
        return array(
            array('id' => 1, 'name' => 'Lowest'),
            array('id' => 5, 'name' => 'Low'),
            array('id' => 10, 'name' => 'Medium'),
            array('id' => 15, 'name' => 'High'),
            array('id' => 20, 'name' => 'Highest'),
        );
    }

    function mergeData(&$data, $data2)
    {
        $temp = array_merge($data, $data2);
        if (count($temp) < (count($data) + count($data2))) {
            echo "CONFICT HAPPEN IN BETWEEN ARRAYS: <br/>\n" . json_encode($data) . "<br/>\n AND <br/>\n" . json_encode($data2);
            exit;
        }
        $data = $temp;
    }

    function removeVariation($data)
    {
        $productVaration = json_decode(urldecode($data['product_variation_json']), true);
        $selectedVariationType = $data['variation_type'];
        foreach ($productVaration as $key => $entry) {
            if ($entry['type_en'] == $selectedVariationType) {
                unset($productVaration[$key]);
                break;
            }
        }

        return $productVaration;
    }

    function addVariation($data)
    {
        $productVaration = json_decode(urldecode($data['product_variation_json']), true);
        $newType = $data['variation_type_en'];
        $newValue = $data['variation_value_en'];
        $typeExist = false;
        foreach ($productVaration as &$entry) {
            if ($entry['type_en'] == $newType) {
                $valueArr = explode(',', $entry['value_en']);
                $newValueArr = explode(',', $newValue);
                $valueArr = array_unique(array_merge($valueArr, $newValueArr));
                $entry['value_en'] = implode(',', $valueArr);
                $typeExist = true;
                break;
            }
        }
        if (!$typeExist) {
            array_push($productVaration, array('type_en' => $newType, 'value_en' => $newValue));
        }

        return $productVaration;
    }

    function getDefaultVariation()
    {
        return array(array('type_en' => 'standard', 'value_en' => 'standard'));
    }
    function getProduct($productId){
        $result = $this->getDBData('select id, manufacturer_id, category_id, title_en, description_en, reference_price, price, priority from product where id = '.$productId );
        if(count($result)< 1){
            return false;
        }
        return $result[0];
    }

    function getProductVariation($productId){
        return $this->getDBData('select variation_type_en as type_en, variation_value_en as value_en from product_variation where product_id = '.$productId.' and is_deleted = 0');
    }

    function getManufacturer()
    {
        return $this->getDBData('select * from manufacturer where is_deleted = 0');
    }

    function getProductCategory()
    {
        $q = 'select if(c2.id, c2.id, c1.id) as id, if(c2.id, concat(c1.category_name_en, \'->\', c2.category_name_en ), c1.category_name_en) as name from category c1
left join category c2 on c1.id = c2.parent_category_id
where c1.parent_category_id is null';
        return $this->getDBData($q);
    }

    function getDBData($query, $returnResult = true, $params = array())
    {
        try {
            $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
            $stmt = $pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            if (!$returnResult)
                return true;
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logException(__FUNCTION__, $e, null, $query);
            return array();
        }
    }

    function logException($function, $exception, $id = false, $comment = false)
    {
        $newException = new ExceptionLog();
        $newException->function_name = $function;
        $newException->exception = $exception->getMessage();
        $newException->id = $id;
        $newException->comment = $comment;
        $newException->save();
        echo("Exception at [$function][" . $exception->getLine() . "][$comment] :" . $exception->getMessage());
    }
}

