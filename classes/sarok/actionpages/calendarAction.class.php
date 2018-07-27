<?php

class calendarAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$this->db=singletonloader::getInstance("mysql");
		$this->log->debug("Running friendlistAction");
		$login=$this->context->user->login;
		$ID=$this->context->user->ID;
		$q="select login, activationDate from users where ID in (select userID from friends where friendType='friend' and friendOf='$ID') order by activationDate desc";
		$out["list"]=$this->db->queryall($q);
		$q="select distinct userID as login, activationDate from sessions where sessions.activationDate>=now() - interval 1 hour and sessions.userID in (select userID from friends where friendType='friend' and friendOf='$ID') order by activationDate desc";
		$online=$this->db->queryall($q);
		for($i=0;$i<sizeof($online);$i++)
 		{
			$out["online"][]=$online[$i]["login"];
 		}
 		$out["login"]=$login;
		return $out;
 	}
}
?>