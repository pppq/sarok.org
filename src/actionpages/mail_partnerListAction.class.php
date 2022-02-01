<?php
class mail_partnerListAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$this->db=singletonloader::getInstance("mysql");
		$this->sf=singletonloader::getInstance("sessionfacade");
		$this->mf=singletonloader::getInstance("mailfacade");
		$this->log->debug("Running mail_partnerListAction");
		$p=$this->mf->getMailPartners($this->context->user->ID);

 		 $out["partners"]=$p["list"];
 		 $out["min"]=$p["min"];
 		 $out["max"]=$p["max"];
		return $out;
 	}
}
?>