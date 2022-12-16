<?php

class settings_magicAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		if(!sizeof($_POST)) return;
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$context=singletonloader::getInstance("contextClass");
		$db=singletonloader::getInstance("mysql");
		$this->log->debug("Running othertAction");
		extract($_POST);

		try{
		switch($action){
			case 'changeaccess': $out["access"]=$access;
									if($access!='ALL' && $access!='REGISTERED' && $access!='FRIENDS' && $access!='PRIVATE')
									 throw new inputException("wrong access $access");
									break;
				case 'changereadaccess': $out["access"]=$readaccess;
									if($readaccess!='ALL' && $readaccess!='REGISTERED' && $readaccess!='FRIENDS' && $readaccess!='PRIVATE')
									 throw new inputException("wrong access $readaccess");
									break;									
			case 'save': $out["comments"]="N";
						if(isset($_POST["comments"]) and $_POST["comments"]=="Y") $out["comments"]='Y';
						 break;
			case 'addtags': $out["tags"]=$tags_toput;
							if(!strlen($tags_toput)) 	 throw new inputException("wrong tags_toput $tags_toput");
							break;
			case 'deltags': $out["tags"]=$tags_todel;
							if(!strlen($tags_todel)) 	 throw new inputException("wrong tags_todel $tags_todel");
							break;
			case 'nothing': $out["action"]="";
							break;
			case 'delete': break;
			default: throw new inputException("Invalid action $action");
				}
			$out["action"]=$action;
		}
		catch(inputException $e)
		{
			$this->log->error($e->msg);
			$out["action"]="";
		}
		$q="select ID, userID, diaryID, access, createDate, left(concat(title,'...',body),60) as title from entries where diaryID='".$context->user->ID."' and ";
		if(isset($input_all) and $input_all=='y') $input_all="access='ALL'"; else $input_all="false";
		if(isset($input_registered) and $input_registered=='y') $input_registered="access='REGISTERED'"; else $input_registered="false";
		if(isset($input_friends) and $input_friends=='y') $input_friends="access='FRIENDS'"; else $input_friends="false";
		if(isset($input_private) and $input_private=='y') $input_private="access='PRIVATE'"; else $input_private="false";
		if(isset($input_search) and strlen($input_search)>0) $input_search=" concat(title,body) like '%".strip_tags($input_search)."%'"; else $input_search="true";

		$q.=" ( ( $input_all or $input_registered or $input_friends or $input_private ) and $input_search ) ";
		if(strlen($datefrom) and ereg("[0-9]{4}-[0-9]{2}-[0-9]{2}",$datefrom)) $q.=" and createDate>='$datefrom' ";
		if(strlen($dateto) and ereg("[0-9]{4}-[0-9]{2}-[0-9]{2}",$dateto)) $q.=" and createDate<='$dateto' ";
		if(strlen($tags)>1)
		{
			$t=preg_split("/[, ;]+/",strip_tags($tags));
			if(sizeof($t)){
			foreach($t as $v) $tagstr[]="'".$v."'";
			$q.=" and ID in (select entryID from categories where Name in (".implode(", ",$tagstr).")) ";
		}
			}
		$q.=" and isTerminated='N' order by createDate desc";

		$out["rows"]=$db->queryall($q);
		$codes=array();
		if(is_array($out["rows"]) and sizeof($out["rows"]))
		{
				foreach($out["rows"] as $row)
			{
				$codes[]=$row["userID"];
			}
			$out["logins"]=$this->sessionFacade->getUserLogins($codes);
		}
		//$out["sql"]=$q;
		$out["diaryID"]=$this->context->user->login;
			    //$settings=
				//$out["name"]=$this->context->user->Name;
		return $out;
 	}
}
?>
