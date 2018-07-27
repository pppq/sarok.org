<?php

class entry_infoAction extends Action{
protected $sessionFacade;
protected $bf,$mysql;

 	function execute()
 	{
		$blog=$this->context->blog;
		$user=$this->context->user;
		$out=array();
		$this->sf=singletonloader::getInstance("sessionfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$params=$this->context->ActionPage->params;
		$this->log->debug("Info Action inited");

		$out["blogName"]=$this->context->blog->blogName;
		$out["blogLogin"]=$this->context->blog->login;
		$out["friends"]=$this->context->blog->friends;
		$out["friendOfs"]=$this->context->blog->friendOfs;
		$out["myFriends"]=$this->context->user->friends;
		$props=array("ID","login","createDate","loginDate","activationDate","name","blogName","occupation","hairColor","eyeColor","blogName","description","sex","district","country","city","email","WIW","MSN","ICQ","skype","phone","birthYear","birthDate");
		foreach($props as $prop)
		{
			$this->log->debug("getting $prop");
			$out[$prop]=$blog->$prop;
		}
		$out["props"]=$props;
		$out["logins"]=$this->sf->getUserLogins(array_merge($out["friends"],$out["friendOfs"]));


		return $out;
 	}
}
?>