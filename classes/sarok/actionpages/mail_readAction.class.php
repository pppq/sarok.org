<?php
class mail_readAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$this->db=singletonloader::getInstance("mysql");
		$this->sf=singletonloader::getInstance("sessionfacade");
		$this->mf=singletonloader::getInstance("mailfacade");
		$this->log->debug("Running mail_readAction");
		$login=$this->context->user->login;
		if($this->context->ActionPage->mailCode<1) return array();

		try{
		$mailrow=$this->mf->getMail($this->context->ActionPage->mailCode,$this->context->user->ID);
		$mailrow["senderLogin"]=$this->sf->getUserLogin($mailrow["sender"]);
		$mailrow["recipientLogin"]=$this->sf->getUserLogin($mailrow["recipient"]);
		$out["mail"]=$mailrow;
		if($mailrow["recipient"]==$this->context->user->ID)
		{
			$this->log->debug("checking ".$this->context->ActionPage->mailCode." for beeing read by ".$mailrow["recipient"]);
			$this->mf->setReadFlag($this->context->ActionPage->mailCode);
		}
		}
		catch(dbFacadeException $e)
		{
			$this->log->security("Attempt to read non-existant or non-granted mail");
			return array();
		}





		return $out;
 	}
}
?>