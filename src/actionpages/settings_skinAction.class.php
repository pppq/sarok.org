<?php

class settings_skinAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("Running skinAction");
		$names=array('css');
	    //$settings=
		foreach($names as $key=>$value){
			$out[$value]=$this->context->user->$value;
 			}

		//$out["name"]=$this->context->user->Name;
		$skins["default"]="Alap";
		$skins["classic"]="Régi";
		$skins["yellow"]="Szep, sarga (oldstyle)";
		$skins["minimal"]="Csunya (munkahelyi)";
		$skins["greybox"]="GreyBox";		
		$out["skins"]=$skins;
		$out["skinName"]=$this->context->user->skinName;
		return $out;
 	}
}
?>