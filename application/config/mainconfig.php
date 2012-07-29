<?php

date_default_timezone_set('Asia/Singapore');
if (defined ( "DOMAIN" )) {
    if (fnmatch ( "*.local:*", DOMAIN )) {
        $config['base_url'] = 'http://test.local:80/';
        $config['enduser_url'] = 'http://test.local:80/';
		//Local laptop testing settings
//		require_once 'mogicard-dev.php';
	} else {
        $config['base_url'] = 'http://littleprecious.comeze.com/';
        $config['enduser_url'] = 'http://littleprecious.comeze.com/';
//		die('LIVE settings not configured');
		//require_once 'mogicard-live.php';
	}
} else {
	die('Unable to configure platform');
}
//$config['enduser_url'] = 'http://littleprecious.comeze.com/';
$config['enduser_product_url'] = $config['enduser_url'].'catalogue/product/';


