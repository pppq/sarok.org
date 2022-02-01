<?php

class settings_makeImportAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		if(!sizeof($_POST)) return;
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$context=singletonloader::getInstance("contextClass");
		$db=singletonloader::getInstance("mysql");
		$if=singletonloader::getInstance("importfacade");
		$this->log->debug("Running makeImportAction");
		extract($_POST);
		$user=$this->context->user;
		$rows=$this->unSerializeImport();

		$out["rows"]=array();
		foreach($codes as $code)
		{
			$entries[]=$rows[$code];
		}
		$if->commitEntries($entries,$user->ID);
		$out=array();
		return $out;
 	}

 	private function unSerializeImport()
 	{
 		$this->log->debug2("unSerializing import");
 		$filename=$_SERVER["DOCUMENT_ROOT"]."/../cache/imports/i_".$this->context->user->ID;
 		if(file_exists($filename))
 		{
 			$this->log->debug("$filename exists");
 			$s = implode("", @file($filename));
  			$rows = unserialize($s);
 		}
 		else
 		{
 			$rows=array();
 		}
		return($rows);
 	}
}
?>
