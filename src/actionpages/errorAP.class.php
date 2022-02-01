<?php
 class errorAP extends ActionPage{

public function init()
{
	parent::init();
	$this->templateName="default";
	$this->actionList["main"][]="error";
	return($this->actionList);
}

public function canRun()
{
	return true;
}
 }
?>
