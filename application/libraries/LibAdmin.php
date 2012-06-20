<?php
require_once 'Constants.php';


class LibAdmin
{
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
        return $this->getDBData('select variation_type_en as type_en, variation_value_en as value_en from product_variation where product_id = '.$productId);
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
        echo("Exception at [$function][" . $exception->getLine() . "][$comment] :" . $exception->getMessage());
    }
}

