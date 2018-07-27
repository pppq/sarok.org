<?php

class settings_otherAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("Running othertAction");
		$names=array('friendListOnly','toMainPage','trackMe','rss','wysiwyg');
	    //$settings=
		foreach($names as $key=>$value){
			$out[$value]=$this->context->user->$value;
 			}
 			$out["login"]=$this->context->user->login;
 			$out["secret"]=urlencode($this->context->user->secret());

		//$out["name"]=$this->context->user->Name;
		return $out;
 	}
}
?>