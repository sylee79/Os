<?php
//require_once "log4php/Logger.php";

//Logger::configure(dirname(__FILE__).'/../config/clog.properties');

//Debug message
function _d($msg) {
    $trace=debug_backtrace();
    $calledloc = $trace[0];
    $filename = basename($calledloc['file']);
    $line = $calledloc['line'];
	log_message('error', "DEBUG: ($filename:$line) $msg");
    //$logger = Logger::getLogger("cranky");
    //$logger->debug("($filename:$line) $msg");
}

//Error message
function _e($msg) {
    $trace=debug_backtrace();
    $calledloc = $trace[0];
    $filename = basename($calledloc['file']);
    $line = $calledloc['line'];
	log_message('error', "ERROR: ($filename:$line) $msg");
    //$logger = Logger::getLogger("cranky");
    //$logger->error("($filename:$line) $msg");
}

//Info message
function _i($msg) {
    $trace=debug_backtrace();
    $calledloc = $trace[0];
    $filename = basename($calledloc['file']);
    $line = $calledloc['line'];
	log_message('error', "INFO: ($filename:$line) $msg");
    //$logger = Logger::getLogger("cranky");
    //$logger->info("($filename:$line) $msg");
}

//Fatal message
function _f($msg) {
    $trace=debug_backtrace();
    $calledloc = $trace[0];
    $filename = basename($calledloc['file']);
    $line = $calledloc['line'];
	log_message('error', "FATAL: ($filename:$line) $msg");
    //$logger = Logger::getLogger("cranky");
    //$logger->fatal("($filename:$line) $msg");
}


//Warning message
function _w($msg) {
    $trace=debug_backtrace();
    $calledloc = $trace[0];
    $filename = basename($calledloc['file']);
    $line = $calledloc['line'];
	log_message('error', "WARNING: ($filename:$line) $msg");
    //$logger = Logger::getLogger("cranky");
    //$logger->warn("($filename:$line) $msg");
}