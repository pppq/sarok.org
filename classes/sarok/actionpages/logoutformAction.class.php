<?php

class logoutFormAction extends Action{
public function execute() {
    	$this->log->debug("Running logoutFormAction");
		$data["name"]=$this->context->user->login;
		$data["loggedin"]=$this->context->props["loggedin"];
    	return $data;
    }
}
?>