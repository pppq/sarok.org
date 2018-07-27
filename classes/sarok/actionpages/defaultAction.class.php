<?php

class defaultAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		//$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("Running defaultAction");
		/*$data=$this->sessionFacade->getCachedComments($this->context->user->login);
		$data2=$this->sessionFacade->getCachedEntries($this->context->user->login);
		$data3=$this->sessionFacade->getCommentsOfEntries($this->context->user->login);
		$data4=$this->sessionFacade->getMyComments($this->context->user->login);
		$out["comments"]=$data;
		$out["entries"]=$data2;
		$out["commentsOfEntries"]=$data3;
		$out["myComments"]=$data4;*/
		return array();
 	}
}
?>