<?php

class settings_blogAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("Running defaultAction");
		$names=array('blogAccess','blogName','blogText','commentAccess','copyright','copyrightText',
'google','messageAccess','statistics','entriesPerPage');
	    //$settings=
		foreach($names as $key=>$value){
			$out[$value]=$this->context->user->$value;
 			}

		//$out["name"]=$this->context->user->Name;
		return $out;
 	}
}
?>