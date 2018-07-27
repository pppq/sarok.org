<?php

class logoutAction extends Action{
public function execute() {
    	$this->log->debug("Running logoutAction");
		$session=singletonloader::getInstance("sessionfacade");
		$data=array();

		$session->logout();

			$data["location"]="/";

    	return $data;
    }
}
?>