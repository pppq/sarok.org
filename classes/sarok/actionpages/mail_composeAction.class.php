<?php
class mail_composeAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$this->db=singletonloader::getInstance("mysql");
		$this->sf=singletonloader::getInstance("sessionfacade");
		$this->mf=singletonloader::getInstance("mailfacade");
		$this->log->debug("Running mail_composeAction");
		$login=$this->context->user->login;
		if($this->context->ActionPage->mailCode<1)
		{
			$this->log->debug("creating a new mail");
			$mailrow["recipientLogin"]="";
			$mailrow["senderLogin"]=$login;
			$mailrow["body"]="";
			$mailrow["title"]="";
			$mailrow["replyOn"]="0";
			$out["mail"]=$mailrow;
		}
		else
		{
			try{
			$this->log->debug("Replying to a mail");
			$mailrow=$this->mf->getMail($this->context->ActionPage->mailCode,$this->context->user->ID);
			if(substr($mailrow["title"],0,2)!="RE")
				$mailrow["title"]="RE: ".$mailrow["title"];
			$mailrow["body"]="<br>\n<blockquote>".preg_replace("/<blockquote>.*<\/blockquote>/i","",$mailrow["body"])."</blockquote><br>\n<br>\n";
			$mailrow["recipientLogin"]=$this->sf->getUserLogin($mailrow["sender"]);
			$mailrow["senderLogin"]=$login;
			$mailrow["replyOn"]=$mailrow["ID"];
			$out["mail"]=$mailrow;
			}
			catch(dbFacadeException $e)
			{
				$this->log->security("Attempt to read non-existant or non-granted mail");
				$this->log->debug("creating a new mail");
				$mailrow["recipientLogin"]="";
				$mailrow["senderLogin"]=$login;
				$mailrow["body"]="";
				$mailrow["replyOn"]="0";
				$out["mail"]=$mailrow;
			}
		}




		return $out;
 	}
}
?>