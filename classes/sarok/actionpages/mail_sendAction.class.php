<?php
class mail_sendAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$this->db=singletonloader::getInstance("mysql");
		$this->sf=singletonloader::getInstance("sessionfacade");
		$this->mf=singletonloader::getInstance("mailfacade");
		$this->log->debug("Running mail_composeAction");
		$row=$_POST;
		extract ($row);
		$users=preg_split("/[ ,;]+/",$recipient);
		foreach($users as $u)
		{
			if((sizeof($users)>1 and $u!="") or sizeof($users)==1)
				{
				if($uc=$this->sf->getUserCode($u))
				$userCodes[]=$uc;
				}
		}
			$userCodes=array_unique($userCodes);
		$senderID=$this->context->user->ID;
		$this->log->debug("Body is $body");
		$this->mf->sendMails($userCodes, $senderID, $title, $body, $replyOn);

		$userLogins=$this->sf->getUserLogins($userCodes);
		$out["recipients"]=$userLogins;

		return $out;
 	}
}
?>