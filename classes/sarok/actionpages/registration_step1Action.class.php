<?php

class registration_step1Action extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("registration_step1Action");
		$loginName="";
		if(isset($_POST["login"]))
		{
			$loginName=$_POST["login"];
		}


		//$out["name"]=$this->context->user->Name;
		$out["date"]=now();
		$out["login"]=$loginName;
		return $out;
 	}
}
?>