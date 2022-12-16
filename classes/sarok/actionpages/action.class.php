<?php

class Action {
protected $log;
protected $context;
function __construct() {
	$this->log=singletonloader::getInstance("log");
	$this->context=singletonloader::getInstance("contextClass");
    $this->log->debug("initializing Action");
    }


    public function execute()
    {

    }

}
?>