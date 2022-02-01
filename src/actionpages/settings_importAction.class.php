<?php

class settings_importAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		if(!sizeof($_POST)) return;
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$context=singletonloader::getInstance("contextClass");
		$db=singletonloader::getInstance("mysql");
		$if=singletonloader::getInstance("importfacade");
		$this->log->debug("Running importAction");
		extract($_POST);
		$user=$this->context->user;
		$importfile=$_FILES["importfile"];
		$location=$importfile["tmp_name"];
		$error=$importfile["error"];
		$out["rows"]=array();
		try{
		switch($importType){
			case 'sarok':
					$this->log->debug("import type is sarok");
					$rows=$if->getDiary($location,$user->ID);
					$this->log->debug("serializing imported rows");
					$this->serializeImport($rows);
					break;
			default: throw new inputException("Invalid importType $importType");
				}
		}
		catch(inputException $e)
		{
			$this->log->error($e->msg);
			$out["action"]="";
		}
		foreach($rows as $index=>$row)
		{
			print_r($row);
			$rows[$index]["body"]=substr(strip_tags($row["title"]." ".$row["body"]),0,50);
			$rows[$index]["index"]=$index;
		}
		$out["rows"]=$rows;
		$codes=array();
		/*if(is_array($out["rows"]) and sizeof($out["rows"]))
		{
				foreach($out["rows"] as $row)
			{
				$codes[]=$row["userID"];
			}
			$out["logins"]=$this->sessionFacade->getUserLogins($codes);
		}*/
		return $out;
 	}

 	private function serializeImport($rows)
 	{
 		$this->log->debug2("serializing import");
 		$filename=$_SERVER["DOCUMENT_ROOT"]."/../cache/imports/i_".$this->context->user->ID;
 		if(file_exists($filename))
 		{
 			unlink($filename);
 		}
		$s = serialize($rows);
  		$fp = fopen($filename, "w");
  		fwrite($fp, $s);
  		fclose($fp);
 	}
}
?>
