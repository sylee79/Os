<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$domain = $_SERVER["HTTP_HOST"];
$domain = substr($domain, 0, ($pos = strpos($domain, ":")) !== FALSE ? $pos : strlen($domain));
$domain .= ":" . $_SERVER["SERVER_PORT"];
define("DOMAIN", $domain);
$baredomain = substr($domain, 0, ($pos = strpos($domain, ":")));
define("BARE_DOMAIN", $baredomain);

/*
Routes:
admin.local:9090
merchant.local:9090
mogi.local:9090

*/
if (0) {
	$route['default_controller'] = "defaultcontroller";
	$route['404_override'] = '';
	$route['(:any)'] = $route['default_controller'] . "/$1";
} else {
	$wildcard_exceptions = array("login", "logout");

	$folder = "";
//	if (fnmatch('*.*:*', $domain)) {
//		Admin site
//		$folder = "admin";
//	} else if (fnmatch('merchant.*:*', $domain)) {
//		Merchant
//		$folder = "merchant";
//	} else {
//		End user site
		$folder = "enduser";
//	}
	
	$route["default_controller"] = "$folder/default_controller";
	
	$requestUri = $_SERVER["REQUEST_URI"];
	$requestToks = strtok($requestUri, "/");
	$firstComponent  = false;
	while ($requestToks !== false) {
		$firstComponent = $requestToks;
		break;
	}

	if ($firstComponent !== false) {
		$controller = BASEPATH . "../application/controllers/$folder/$firstComponent" . ".php";
		if (file_exists($controller)) {
			$route[$firstComponent] = "$folder/$firstComponent";
			$route[$firstComponent . "/(:any)"] = "$folder/$firstComponent/$1";
		} else {
			if (!in_array($firstComponent, $wildcard_exceptions)) {
				define("V2_WILDCARD_FIRST_COMPONENT", $firstComponent);
				$route[$firstComponent] = "$folder/wildcard_controller";
				$route[$firstComponent . "/(:any)"] = "$folder/wildcard_controller/$1";
			}
		}
	}
	$route['404_override'] = '';
	$route['(:any)'] = $route['default_controller'] . "/$1";
}
//echo json_encode($route);exit;

/* End of file routes.php */
/* Location: ./application/config/routes.php */