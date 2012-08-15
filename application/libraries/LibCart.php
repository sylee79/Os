<?php
require_once('common.php');
require_once('Constants.php');

class cartItem{
    public $productId;
    public $variations;
    public $quantity;

    function __construct($productId, $variations, $quantity){
        $this->productId = $productId;
        if(is_array($variations)){
            $this->variations = array();
            foreach($variations as $key => $value){
                $this->variations[$key]=$value;
            }
        }else{
            $this->variations = NULL;
        }
        $this->quantity = $quantity;
    }

    function isMatch($productId, $variations){
        if(is_array($variations)){
            foreach($variations as $key=>$value){
                if(!isset($this->variations[$key]) || $this->variations[$key] != $value){
                    return false;
                }
            }
            return true;
        }else{
            return true;
        }
    }


    function adjust($productId, $variations, $quantity, $isAdding=false){
        if(is_array($variations)){
            foreach($variations as $key=>$value){
                if(!isset($this->variations[$key]) || $this->variations[$key] != $value){
                    return false;
                }
            }
        }

        if($isAdding)
            $this->quantity += $quantity;
        else
            $this->quantity = $quantity;

        return true;
    }
}

class cartSession{
    public $items;
    public $count;

    function __construct(){
        $this->items = array();
        $this->count = 0;
    }

    function add2Cart($productId, $variations, $quantity){
        if(!isset($this->items[$productId])){
            $this->items[$productId]=array();
        }else{
            foreach($this->items[$productId] as $item){
                if($item->adjust($productId, $variations, $quantity, true)){
                    return true;
                }
            }
            //item not found
        }
        ++$this->count;
        array_push($this->items[$productId], new cartItem($productId, $variations, $quantity));
        return true;
    }


}

class LibCart{
    private $CI;
    function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->library('Session');

    }

    function clearCart(){
        $this->CI->session->unset_userdata('cart_session');
    }

    function getCartCount(){
        Common::startBenchmark(__FUNCTION__);
        $cartSess = $this->CI->session->userdata("cart_session");
        Common::endBenchmark(__FUNCTION__);
        if(!$cartSess) return 0;
        return $cartSess->count;
    }

    function add2Cart($productId, $variation, $quantity){
        $cartSess = $this->CI->session->userdata("cart_session");
        if(!$cartSess){
            $cartSess = new cartSession();
        }
        $cartSess->add2Cart($productId, $variation, $quantity);
        $this->CI->session->set_userdata("cart_session", $cartSess);
    }

    function getUserDetails()
    {
        return $this->CI->session->userdata("user_details");
    }

    function saveUserDetails()
    {
        $userDetails = $this->CI->session->userData("user_details");
        if(!$this->CI->input->post("buyer")) return $userDetails;
        $userDetails['buyer'] = $this->CI->input->post("buyer");
        $userDetails['email'] = $this->CI->input->post("email");
        $userDetails['contact'] = $this->CI->input->post("contact");
        $userDetails['address'] = $this->CI->input->post("address");
        $userDetails['comment'] = $this->CI->input->post("comment");
        $this->CI->session->set_userdata("user_details",$userDetails);
        return $userDetails;
    }

    function getCartItems()
    {
        $cartSess = $this->CI->session->userdata("cart_session");
        if(!$cartSess) return array('items'=>array(), 'total'=>0);;
        $productIds = array_keys($cartSess->items);
        if(count($productIds)<1) return array('items'=>array(), 'total'=>0);

        $q = 'select id, concat( title_en, \'*x*\' , price) from product where id in ('.implode(',',$productIds).')';

        $result = $this->getDBData($q, true, array(), PDO::FETCH_KEY_PAIR);

        $retItems = array();
        $total = 0;

        foreach($cartSess->items as $product){
            foreach($product as $item){
                $productDetail = explode('*x*', $result[$item->productId]);
                $variableStr = count($item->variations)>0?str_ireplace('=>',':', json_encode($item->variations)):NULL;
                $variableStr = str_ireplace('"','',$variableStr);
                $variableStr = str_ireplace('{','(',$variableStr);
                $variableStr = str_ireplace('}',')',$variableStr);
                array_push($retItems, array('productId'=>$item->productId
                    , 'title'=>$productDetail[0]
                    , 'variation'=> $variableStr
                    , 'price' => number_format($productDetail[1], 2)
                    , 'quantity' => $item->quantity
                    , 'subtotal' => number_format($item->quantity * $productDetail[1], 2)
                    ));
                $total += $item->quantity * $productDetail[1];
            }
        }

        return array('items'=>$retItems, 'total'=>number_format($total,2), 'count'=>$cartSess->count);
    }

	function sendEmail($to, $subject, $contentHTML) {
		_d("SENDEMAIL: To [$to] Subject [$subject] Content [$contentHTML]");
        $Config =& get_config();

        $CI =& get_instance();
        $CI->load->library('email');
        $params['protocol']= 'smtp';
        $params['smtp_host']='ssl://smtp.gmail.com';
        $params['smtp_user']='littleprecious123@gmail.com';
        $params['smtp_pass']='sPLp12143';
        $params['smtp_port']=465;
        $params['mailtype']='html';
        $params['newline']="\n";
        $params['charset']='utf-8';

        $CI->email->initialize($params);
        $CI->email->set_newline("\r\n");

        $CI->email->from('littleprecious123@gmail.com', 'Little Precious');
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message($contentHTML);
        $ret = $CI->email->send();
//        echo $CI->email->print_debugger();
        return true;
	}

    function sendOrder(){

    }

    function getDBData($query, $returnResult = true, $params = array(), $fetchMode=PDO::FETCH_ASSOC)
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
            return $stmt->fetchAll($fetchMode);
        } catch (Exception $e) {
//            $this->logException(__FUNCTION__, $e, null, $query);
            return array();
        }
    }


}


