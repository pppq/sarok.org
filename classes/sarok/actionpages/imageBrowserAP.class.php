<?php
class imageBrowserAP extends ActionPage{
public $actionMap;
public $key;
    function init() {
    parent::init();
    $this->log->debug("Initializing imageBrowserAP");
	$this->actionList["main"]=array();
    $this->actionList["main"][]="imageBrowser";
   // $this->templateName="empty";
     return $this->actionList;
    
    }

    public function canRun()
    {
//    	return true;
    	return $this->context->props["loggedin"];
    }
}
?>