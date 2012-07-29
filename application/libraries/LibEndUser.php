<?php

require_once('common.php');
require_once('Constants.php');

/**
 * Copyright (c) 2011 YuuZoo Pte Ltd. All Rights Reserved.
 *
 * THIS IS UNPUBLISHED PROPRIETARY SOURCE CODE DEVELOPED BY YUUZOO
 *
 * It may not be disclosed to third parties,copied or duplicated in any
 * form, in whole or in part, without the prior written permission from
 * YuuZoo. Furthermore, this software if distributed is
 * distributed as is, with absolutely no warranty of any kind, either
 * expressed or implied, including, but not limited to, the implied
 * warranties of merchantability of fitness for a particular purpose.
 *
 * The copyright notice above does not evidence any actual or
 * intended publication of such source code.
 */

class LibEnduser{
    function __construct(){
    }

    function getCategoryProduct(&$data, $categoryId){
        if(is_numeric($categoryId)){
            $q = 'select category_name_en from category where id = '.$categoryId;
            $result = $this->getDBData($q);
            if(isset($result[0])){
                $data['category_title']=$result[0]['category_name_en'];
                $data['productList']=$this->getProduct($categoryId);
                return true;
            }
        }

        $data['category_title']='New Arrival';
        $data['productList']=$this->getNewArrival();
        return true;
    }


    function getProductVariation($productId){
        if(!is_numeric($productId)) return false;
        $q = 'select id, variation_type_en, variation_value_en'
            .' from product_variation'
            .' where product_id = '.$productId
            .' and variation_type_en <> \'standard\' and is_deleted = 0';
        return $this->getDBData($q);
    }

    function getProductDetails($productShortcut){
        $config =& get_config();
        $q = 'select p.id, p.title_en, p.description_en, p.price, concat(\''.$config['enduser_product_url'].'\',p.product_shortcut) as productLink, c.category_name_en'
            .' from product p'
            .' left join category c on c.id = p.category_id and c.is_deleted = 0'
            .' where p.product_shortcut = :productShortcut order by p.added_on';
        $result = $this->getDBData($q, true, array(':productShortcut'=>$productShortcut) );
        if(!isset($result[0]))return false;
        return $result[0];
    }

    function getProductImages($productId){
        $config =& get_config();
        $q = 'select concat(\''.$config['base_url'].'\', pi.image_url) as image_url, pi.description'
            .' from product_image pi'
            .' where pi.product_id = '.$productId
            .' and pi.is_deleted = 0'
            .' order by is_main desc';
        return $this->getDBData($q);
    }

    function getProduct($categoryId, $page=1, $pageSize=20){
        if(!is_numeric($page) || !is_numeric($pageSize)){
            return false;
        }
        $config =& get_config();
        $limit = ($page-1)*$pageSize .','.$pageSize;
        $q = 'select distinct(p.id), p.title_en, p.description_en, p.price, concat(\''.$config['enduser_product_url'].'\',p.product_shortcut) as productLink, concat(\''.$config['base_url'].'\', pi.image_url) as image_url, c.category_name_en'
            .' from product p'
            .' left join product_image pi on pi.product_id = p.id and pi.is_main = 1 and p.is_deleted = 0'
            .' left join category c on c.id = p.category_id and c.is_deleted = 0'
            .' where ( p.category_id = '.$categoryId.' or c.parent_category_id = '.$categoryId.')'
            .' and p.is_deleted  = 0 order by p.added_on'
            .' limit '.$limit;
        return $this->getDBData($q);

    }

    function getNewArrival($page=1, $pageSize=20){
        if(!is_numeric($page) || !is_numeric($pageSize)){
            return false;
        }
        $config =& get_config();
        $limit = ($page-1)*$pageSize .','.$pageSize;
        $q = 'select distinct(p.id), p.title_en, p.description_en, p.price, concat(\''.$config['enduser_product_url'].'\',p.product_shortcut) as productLink, concat(\''.$config['base_url'].'\', pi.image_url) as image_url, c.category_name_en'
            .' from product p'
            .' left join product_image pi on pi.product_id = p.id and pi.is_main = 1 and p.is_deleted = 0'
            .' left join category c on c.id = p.category_id and c.is_deleted = 0'
            .' where p.is_deleted  = 0 order by p.added_on'
            .' limit '.$limit;
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
        _d("Exception at [$function][" . $exception->getLine() . "][$comment] :" . $exception->getMessage());
    }
}
