<?php
 class splashAP extends ActionPage{

public function init()
{
	global $tileList;
	
	$tileList=array();
	//$this->tiles["main"]="";
	$this->templateName="splash";
	$this->actionList["main"][]="empty";
	return($this->actionList);
}
 }
?>
