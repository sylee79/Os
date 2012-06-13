<?php

class Common{

    private function encrypt($string) {
        return sha1 ( $string ); //CAREFUL WHEN CHANGING THIS! WILL AFFECT EXISTING ACCOUNTS!
    }

    public static function generateRandomKey($length = 8) {
        $CI =& get_instance();

        $CI->load->helper('string');
        $curTime = time();
        $ret = "";
        $ret .= $curTime;
        $ret .= random_string("alnum", $length);
        return $ret;
    }

}
