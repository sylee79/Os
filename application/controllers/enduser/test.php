<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sengyee
 * Date: 28/2/12
 * Time: 11:14 AM
 * To change this template use File | Settings | File Templates.
 */

require_once 'baseendusercontroller.php';

class test extends baseendusercontroller{
    function __construct() {
        parent::__construct();
    }

    function test1(){
        echo "test start";
    }
}