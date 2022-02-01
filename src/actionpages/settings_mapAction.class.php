<?php

class settings_mapAction extends Action{
protected $sessionFacade,$blogFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->blogFacade=singletonloader::getInstance("blogfacade");
		$this->log->debug("Running mapAction");
		$posX=$this->context->user->posX;
		$posY=$this->context->user->posY;
		
		$out=array();
		if(is_numeric($posX) && is_numeric($posX))
		{
			$out["posX"]=$posX;
			$out["posY"]=$posY;
		}
		else
		{
			$out["posX"]="";
			$out["posY"]="";
		}
		$out["bindToMap"]=$this->context->user->bindToMap;
		$coords=$this->blogFacade->getMapMarkers($this->context->user);
		$out["coords"]=$coords;
		//$out["name"]=$this->context->user->Name;
		return $out;
 	}
}
?>