<?php

class settings_imagesAction extends Action{
protected $sessionFacade,$im;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->im=singletonloader::getInstance("imagefacade");
		$this->log->debug("images defaultAction");

		$fileList=$this->im->listDir($_SERVER["DOCUMENT_ROOT"]."/userimages/".$this->context->user->ID."/");
		//rsort($fileList);
		$this->log->debug("Filelist is ".implode(", ",$fileList));
		$out["fileList"]=$fileList;
		$out["path"]="/userimages/".$this->context->user->ID."/";
		return $out;
 	}
}
?>