<?php

class settings_infoAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("Running defaultAction");
		$names=array('birthDate','birthYear','city','country','description','district','email','eyeColor','hairColor','ICQ','MSN','skype','name','keywords','occupation','phone','publicInfo','sex','state','WIW');
	    //$settings=
		foreach($names as $key=>$value){
			$out[$value]=$this->context->user->$value;
 			}

		//$out["name"]=$this->context->user->Name;
		return $out;
 	}
}
?>