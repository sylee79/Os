<?php
$GLOBALS['c_ads']='';
$GLOBALS['c_ads_code']='';

set_include_path(get_include_path().":".BASEPATH."/libraries/:".BASEPATH."libraries/PEAR-1.9.4/");
date_default_timezone_set('Asia/Singapore');
if (defined ( "DOMAIN" )) {
    if (fnmatch ( "*.local:*", DOMAIN )) {
        $config['base_url'] = 'http://test.local:80/';
        $config['enduser_url'] = 'http://test.local:80/';
		//Local laptop testing settings
//		require_once 'mogicard-dev.php';
	} else {
        $config['base_url'] = 'http://littleprecious.3owl.com/';
        $config['enduser_url'] = 'http://littleprecious.3owl.com/';
//		die('LIVE settings not configured');
		//require_once 'mogicard-live.php';
	}
} else {
	die('Unable to configure platform');
}
//$config['enduser_url'] = 'http://littleprecious.comeze.com/';
$config['email_from_addr'] ='littleprecious123@gmail.com';
$config['email_from_name'] ='Little Precious';
$config['ajax_loader'] = 'res/enduser/images/ajax_280.gif';
$config['enduser_product_url'] = $config['enduser_url'].'catalogue/product/';
$config['cron_white_list'] = array('127.0.0.1','220.255.2.73');


