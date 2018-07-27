<?php
 class authAP extends ActionPage{

public function init()
{
	parent::init();
	//$this->tiles["main"]="";
	$this->templateName="empty";
	$this->actionList["main"][]="auth";
	return($this->actionList);
}

public function canRun()
{
	$this->log->debug("running canRun() ");
	$this->log->debug(isset($_POST["login"]) ." and ". isset($_POST["pass"]) ." and ". strlen($_POST["pass"])>2);

	//print_r($_POST);
	return (isset($_POST["login"]) and isset($_POST["pass"]) and strlen($_POST["pass"])>2);
}
 }
?>
