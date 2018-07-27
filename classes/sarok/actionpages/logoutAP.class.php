<?php
 class logoutAP extends ActionPage{

public function init()
{
	parent::init();
	//$this->tiles["main"]="";
	$this->templateName="empty";
	$this->actionList["main"][]="logout";
	return($this->actionList);
}

public function canRun()
{
	return $this->context->props["loggedin"];
}
 }
?>
