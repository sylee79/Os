<?php
define('BASEPATH', dirname(__FILE__) . "/..");

require_once(dirname(__FILE__) . '/../application/config/database.php');
require_once(dirname(__FILE__) . '/../system/database/doctrine/Doctrine.php');

$active_group = getenv('USE_DB_GROUP');

spl_autoload_register(array('Doctrine', 'autoload'));
spl_autoload_register('generated_autoloader');
spl_autoload_register("autoload_ext");

function autoload_ext($clz) {
    $extFile = dirname(__FILE__) . "/../application/doctrine_ext/$clz.php";
    if (file_exists($extFile )) {
        require_once $extFile;
    }
}

function generated_autoloader($clz) {
    $genFile = dirname(__FILE__) . "/../application/models/generated/$clz.php";

    if (file_exists($genFile )) {
        require_once $genFile;
    }
}

// Create dsn from the info above
$db[$active_group]['dsn'] = $db[$active_group]['dbdriver'] .
                        '://' . $db[$active_group]['username'] .
                        ':' . $db[$active_group]['password'].
                        '@' . $db[$active_group]['hostname'] .
                        '/' . $db[$active_group]['database'];

// Load the Doctrine connection
Doctrine_Manager::connection($db[$active_group]['dsn'], $db[$active_group]['database']);

// Load the models for the autoloader
Doctrine::loadModels(realpath(dirname(__FILE__)) . '/../application/models', Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

// Configure Doctrine Cli
// Normally these are arguments to the cli tasks but if they are set here the arguments will be auto-filled
$config = array('data_fixtures_path'  =>  dirname(__FILE__) . '/../application/fixtures',
                'models_path'         =>  dirname(__FILE__) . '/../application/models',
                'migrations_path'     =>  dirname(__FILE__) . '/../application/migrations',
                'sql_path'            =>  dirname(__FILE__) . '/../application/schema',
                'yaml_schema_path'    =>  dirname(__FILE__) . '/../application/schema/schema.yml'
                );

$cli = new Doctrine_Cli($config);
$cli->run($_SERVER['argv']);

