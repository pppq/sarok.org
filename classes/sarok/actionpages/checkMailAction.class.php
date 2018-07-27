<?php

class checkMailAction extends Action{
protected $db;
 	function execute()
 	{

		$this->log->debug("Running checkMailtAction");
		$hasMail=$this->context->user->newMail;

		$out["num"]=$hasMail;
		if($hasMail>0)
		{
			$out["login"]=$this->context->user->login;
			$this->db=singletonloader::getInstance("mysql");
			$this->sf=singletonloader::getInstance("sessionfacade");
			$this->log->debug("There is mail. Mail number is $hasMail");
			$ID=$this->context->user->ID;

			if($hasMail>1)
				{
			$q="select  ID, sender, `date`, title from mail where recipient='$ID' and isRead='N' and isDeletedByRecipient='N' order by Date limit 1";
			$out["first"]=$this->db->queryone($q);
			$out["first"]["senderLogin"]=$this->sf->getUserLogin($out["first"]["sender"]);
				}
			$q="select  ID, sender, `date`, title from mail where recipient='$ID' and isRead='N' and isDeletedByRecipient='N' order by Date desc limit 1";
			$out["last"]=$this->db->queryone($q);
			$out["last"]["senderLogin"]=$this->sf->getUserLogin($out["last"]["sender"]);

		if(!strlen($out["last"]["senderLogin"]))
		{
			$out["num"]=0;
		}
		}
		else
			$this->log->debug("No mail for {$this->context->user->ID}");
		return $out;
 	}
}
?>