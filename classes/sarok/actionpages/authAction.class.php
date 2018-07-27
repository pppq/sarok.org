<?php

class authAction extends Action{
public function execute() {
    	$this->log->debug("Running authAction");
		$session=singletonloader::getInstance("sessionfacade");
		$data=array();
		try{
		$session->loginUser($_POST["login"],$_POST["pass"]);
		$success=true;
		}
		catch(LoginFailedException $e){
			$this->log->warning("Login for ".$_POST["login"]." failed, IP:".gethost()." pass:".$_POST["pass"],"Login for ".$_POST["login"]." failed");
			$this->log->mail("Login for ".$_POST["login"]." failed, IP:".gethost()." pass:".$_POST["pass"],"Login for ".$_POST["login"]." failed");
			$success=false;
		}
		if($success)
		{
		if(isset($_POST["from"]))
			$data["location"]=$_POST["from"];
		else
			$data["location"]="/";
		}
		else
		{
			$this->context->ActionPage->templateName="splash";
			//$data["location"]="/error!";
		}
    	return $data;
    }
}
?>