<?php

class commentsAction extends Action{
	protected $mysql;
	protected $bf;
public function execute() {
    	$this->log->debug("Running entryAction");
		$session=singletonloader::getInstance("sessionfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$this->bf=singletonloader::getInstance("blogfacade");
		$tp=new textProcessor();
		$data=array();

		$params=$this->context->ActionPage->params;
		$data["isLoggedIn"]=($this->context->user->ID!=1);
		$data["your_name"]=isset($_COOKIE["your_name"])?$_COOKIE["your_name"]:"";
		$data["your_web"]=isset($_COOKIE["your_web"])?$_COOKIE["your_web"]:"";
		$query=$this->bf->canViewEntry($this->context->ActionPage->entryCode,$this->context->user,$this->context->blog);
		$row=array();
		$row=$this->mysql->queryone($query);
if(sizeof($row)>1)
{
	if($row["userID"]==$this->context->user->ID)
		{
		$this->mysql->mquery("update entries set lastVisit=now() where ID='".$this->context->ActionPage->entryCode."'");
		}
			//print_r($row);
			$row["body"]=$tp->postFormat($row["body"]);
			$commentList=$this->bf->getComments($this->context->ActionPage->entryCode,$this->context->user);
			if(count($commentList) > 0)
			{
			foreach($commentList as $k=>$v)
			{
				$logins[]=$v["userID"];
				$commentIDs[]=$v["ID"];
			}
			$userCommentRates=$this->bf->getUserCommentRates($commentIDs,$this->context->user->ID);
			
			$logins[]=$row["userID"];
			$logtable=$session->getUserLogins($logins);
			foreach($commentList as $k=>$v)
			{
				$commentList[$k]["canDelete"]=$this->bf->canDeleteComment($v,$row,$this->context->user->ID);
				$commentList[$k]["userLogin"]=$logtable[$commentList[$k]["userID"]];
				$commentList[$k]["diaryLogin"]=$this->context->blog->login;
				$commentList[$k]["canRate"]=true;
				if($commentList[$k]["userID"]==$this->context->user->ID or $this->context->user->ID==1 or in_array($commentList[$k]["ID"],$userCommentRates))
					$commentList[$k]["canRate"]=false;
			}
			}
			else
			{
				$logins[]=$row["userID"];
				$logtable=$session->getUserLogins($logins);
			}
			$cList=array();
			if(count($commentList) > 0) 
			foreach($commentList as $comment)
			{
				if(!in_array($comment["userID"],$this->context->user->bans) and !in_array($comment["userID"],$this->context->user->banOfs))
				$cList[]=$comment;
			}
			$data["commentList"]=$cList;
}
else
{
	$params=$this->context->ActionPage->templateName="error";
}
		$data["entry"]=$row;

		$data["entryID"]=$row["ID"];
		$data["userLogin"]=$logtable[$row["userID"]];
		$data["userLoginName"]=$logtable[$row["userID"]];
		$data["myLoginName"]=$this->context->user->login;
		//$this->log->debug("myLoginName is ".$data["myLoginName"]);
		$data["blogName"]=$this->context->blog->blogName;
		$data["diaryLogin"]=$this->context->blog->login;
		$data["canCommentIt"]=$this->bf->canComment($row,$this->context->user->ID);
		$data["canChange"]=$this->bf->canChangeEntry($row,$this->context->user->ID);
		$data["isFavourite"]=$this->bf->updateFavourite($this->context->user->ID,$row["ID"]);
		$data["canHaveFavourite"]=$this->context->user->ID!=1;
		$data["tags"][$row["ID"]]=$this->bf->getTags($row["ID"]);
    	//print_r($data);
    	return $data;
    }
}
