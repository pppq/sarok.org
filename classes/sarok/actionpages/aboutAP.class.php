<?php
class aboutAP extends ActionPage{
public $actionMap;
public $key;
    function init() {
    parent::init();
    $this->log->debug("Initializing map");
    $this->actionMap=array();
    $this->actionMap["mediaajanlat"]=20505;
    $this->actionMap[""]=15287;
	$this->actionMap["us"]=15287;
	$this->actionMap["map"]="generalMap";
	$this->actionMap["pacients"]="userList";
	$this->key=87288;
	//$this->actionList["friendlist"][]="logoutForm";

	$path=$this->context->params;
	//unset($path[0]);
	$pathline=implode("/",$path);
	$this->log->debug("pathline is $pathline");
	if(!isset($this->actionMap[$pathline]))
	{
		$this->key=15287;
		$this->log->warning("item not found in actionMap");
	}else
	{
		$this->key=$this->actionMap[$pathline];
	}
	if(is_numeric($this->key))
	{
		 $this->actionList["main"][]="showArticle";
		 $this->log->warning("found numeric key for the item");
	}
	else
	{
		 $this->actionList["main"][]=$this->key;
	}
	$this->log->debug("key set to ".$this->key);
    return $this->actionList;
    }

    public function canRun()
    {
    	return true;
//    	return $this->context->props["loggedin"];
    }
}
?>