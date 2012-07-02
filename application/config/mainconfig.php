<?php
if (defined ( "DOMAIN" )) {
    if (fnmatch ( "*.local:*", DOMAIN )) {
		//Local laptop testing settings
//		require_once 'mogicard-dev.php';
	} else {
//		die('LIVE settings not configured');
		//require_once 'mogicard-live.php';
	}
} else {
	die('Unable to configure platform');
}
$config['enduser_url'] = 'http://littleprecious.comeze.com';
$config['enduser_product_url'] = $config['enduser_url'].'/product';


