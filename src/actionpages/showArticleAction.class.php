<?php

class showArticleAction extends Action{

 	function execute()
 	{
		$this->log->debug("Running showArticleAction");
		$bf=singletonloader:: getInstance("blogfacade");
		$key=$this->context->ActionPage->key;
		try{
		$entry=$bf->getEntry($key);
		}
		catch(dbFacadeException $e)
		{
			$entry=$bf->getEntry(87287);
		}
		$out=array();
		$out["body"]=$entry["body"];
		$out["body2"]=$entry["body2"];
		return $out;
 	}
}
?>