<?php

class emptyAction extends Action{

    public function execute() {
    	$this->log->debug("Running emptyAction");
    	return array();
    }
}
?>