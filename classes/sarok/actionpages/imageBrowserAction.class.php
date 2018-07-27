<?php
require("settings_imagesAction.class.php");
class imageBrowserAction extends Action{
protected $sessionFacade,$im;
 	function execute()
 	{
 		
 		$imAction=new settings_imagesAction();
 		$out=$imAction->execute();
		$this->context->ActionPage->templateName="empty";
		return $out;
 	}
}
?>