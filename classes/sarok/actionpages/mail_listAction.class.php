<?php
class mail_listAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$this->db=singletonloader::getInstance("mysql");
		$this->sf=singletonloader::getInstance("sessionfacade");
		$this->mf=singletonloader::getInstance("mailfacade");
		$this->log->debug("Running mail_listAction");
		$login=$this->context->user->login;
		$sender=$recipient=$date=$keyword="";
		$skip=0;
		$num=50;
		$recipient=$this->context->user->ID;
		if(strlen($this->context->ActionPage->fromto))
			$sender=$this->sf->getUserCode($this->context->ActionPage->fromto);
		$this->log->debug(sizeof($_POST["keyword"]));
		if(isset($_POST["keyword"]))
		{
			$keyword=$_POST["keyword"];
			$this->log->debug("keyword is $keyword");
		}
		if(strlen($this->context->ActionPage->year))
		{
			$date=$this->context->ActionPage->year."-".$this->context->ActionPage->month."-".$this->context->ActionPage->day;
		}
 		if(strlen($this->context->ActionPage->skip))
			$skip=$this->context->ActionPage->skip;
 		$mails=$this->mf->getMailList($sender,$recipient,$date,$keyword,$skip, $num);
 		$out["login"]=$login;
 		if(count($mails) > 0)
 		{
 			for($i=0;$i<sizeof($mails);$i++)
 			{
 				$mails[$i]["Login"]=$this->sf->getUserLogin($mails[$i]["sender"]);
 				if(!strlen(trim($mails[$i]["title"]))) $mails[$i]["title"]="Nincs tárgy";
 			}
 		}
 		$out["inmails"]=$mails;

 		$mails=$this->mf->getMailList($recipient,$sender,$date,$keyword,$skip, $num,false);
 		$out["login"]=$login;
 		if(count($mails) > 0)
 		{
 			for($i=0;$i<sizeof($mails);$i++)
 			{
 				$mails[$i]["Login"]=$this->sf->getUserLogin($mails[$i]["recipient"]);
 				if(!strlen(trim($mails[$i]["title"]))) $mails[$i]["title"]="Nincs tárgy";
 			}
 		}
 		 $out["outmails"]=$mails;
 		 $out["keyword"]=$keyword;
		return $out;
 	}
}
