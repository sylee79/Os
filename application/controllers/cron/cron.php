<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once BASEPATH . '../application/controllers/basewebappcontroller.php';

class cron extends basewebappcontroller {
	
	function __construct() {
		parent::__construct();
        $this->load->library('LibAdmin');

        $wlist = $this->config->item('cron_white_list');
        if ($wlist != null && in_array($_SERVER['REMOTE_ADDR'], $wlist)) {
        } else {
//            echo 'cron job is not allowed to called from this ip:'.$_SERVER['REMOTE_ADDR'];
//            exit(-1);
        }
	}

    function index(){
        echo "OK";
    }

    function generateThumbnail(){
        $q = 'select image_url from product_image where is_deleted = 0';
        $result = $this->getDBData($q);
        $count = 0;
        foreach($result as $entry){
            $count += $this->libadmin->createThumbnail($entry['image_url']);
        }
        echo $count." thumbnail created";
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

    function logException($function, $exception, $id = null, $comment = false)
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
