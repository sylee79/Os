<?php
require_once (BASEPATH . "/database/doctrine/Doctrine.php");

require_once (BASEPATH . "/database/doctrine/Doctrine/Overloadable.php");
require_once (BASEPATH . "/database/doctrine/Doctrine/Record/Listener/Interface.php");
require_once (BASEPATH . "/database/doctrine/Doctrine/Record/Listener.php");

class DoctrineLogger implements Doctrine_Overloadable {
    public function __call($m, $a) {
        //log_message("error", 'Doctrine logger caught event ' . $m);
    }
}


class DoctrineRecordLogger extends Doctrine_Record_Listener {

    public function preInsert(Doctrine_Event $event) {
        $curTime = time();
        if ($event->getInvoker()->getTable()->hasField("added_on")) {
            $event->getInvoker()->added_on = $curTime;
        }

        if ($event->getInvoker()->getTable()->hasField("updated_on")) {
            $event->getInvoker()->updated_on = $curTime;
        }

    }

    public function preUpdate(Doctrine_Event $event) {
        $curTime = time();
        if ($event->getInvoker()->getTable()->hasField("updated_on")) {
            $event->getInvoker()->updated_on = $curTime;
        }
    }

}

function autoload_ext($clz) {
    $extFile = BASEPATH . '/../application/doctrine_ext/' . $clz . EXT;
    if (file_exists($extFile )) {
        require_once $extFile;
    }
}

function bootstrap_doctrine() {
        require_once(BASEPATH . "/../application/config/database.php");

        // Set the autoloader
        spl_autoload_register(array('Doctrine', 'autoload'));
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        spl_autoload_register("autoload_ext");

        // Load the Doctrine connection
        // (Notice the use of $active_group here, to make it easy to swap out
        //  you connection based on you database.php configs)

        if (!isset($db[$active_group]['dsn'])) {
                //try to create the dsn, if it has not been manually set
                //in your config. I personally would opt to set my
                //dsn manually, but it works either way
                $db[$active_group]['dsn'] = $db[$active_group]['dbdriver'] .
                        '://' . $db[$active_group]['username'] .
                        ':' . $db[$active_group]['password'].
                        '@' . $db[$active_group]['hostname'] .
                        '/' . $db[$active_group]['database'];
        }

        Doctrine_Manager::connection($db[$active_group]['dsn'], $db[$active_group]['database']);
        Doctrine_Manager::getInstance()->addRecordListener(new DoctrineLogger());
        $manager = Doctrine_Manager::getInstance();
        $manager->addRecordListener(new DoctrineRecordLogger());

        /***** MEMCACHED SECTION ******/

/*
        $servers = array(
            'host' => '127.0.0.1',
            'port' => 11211,
            'persistent' => true
        );

        $cacheDriver = new Doctrine_Cache_Memcache(array(
                'servers' => $servers,
                'compression' => false
            )
        );
*/


        /**** ARRAY CACHE ****/
        //$cacheDriver = new Doctrine_Cache_Array();
        //$cacheDriver = new Doctrine_Cache_Apc();

//        $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cacheDriver);
//        $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $cacheDriver);

        // Load the models for the autoloader
        // This assumes all of your models will exist in you
        // application/models folder
        Doctrine::loadModels(BASEPATH . '/../application/models', Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
}
