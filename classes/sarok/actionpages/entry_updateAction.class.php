<?php

class entry_updateAction extends Action{
protected $sessionFacade;
protected $bf,$mysql,$df;

 	function execute()
 	{
		$this->df=singletonloader::getInstance("dbfacade");
		$this->bf=singletonloader::getInstance("blogfacade");
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$blog=$this->context->blog;
		$user=$this->context->user;
		$data=$_POST;
		$userList=array();
		extract($data);
		$this->log->debug("updating entry");
		$tags=preg_split("/[, ;]+/",strip_tags($tags));
		$tags=array_unique($tags);
		if(strlen($list))
		{
			$userList1=preg_split("/[ ,;]+/",$list);
			foreach($userList1 as $userLogin){
				if($this->df->userExists($this->sessionFacade->getUserCode($userLogin)))
				 {
				 	$userList[]=$this->sessionFacade->getUserCode($userLogin);
				 	$this->log->debug("adding $userLogin to list");
				 }
			}
		}
		if(!isset($needsMap) or $needsMap!='Y')
		{
			$posX=$posY=0.0;
		}
		$this->context->templateName="empty";
		//print_r($data);

		if(isset($ID) and is_numeric($ID))
		{
			$this->log->debug("changing entry #".$ID);
			$newBlog=$this->context->getUser($diaryLogin);
			$data["userID"]=$this->mysql->querynum("select userID from entries where ID='$ID' limit 1");
			$data["diaryID"]=$this->sessionFacade->getUserCode($diaryLogin);
			if($this->bf->canChangeEntry($data,$user->ID) and $this->bf->canAddEntry($user,$newBlog))
			{
			$bodies=$this->bf->splitBodies($body);
			$body1=$bodies[0];
			$body2=$bodies[1];
			$this->bf->changeEntry($ID,$newBlog->ID, $access, $userList, $comments, $title, $body1,$body2, $tags,$posX,$posY);
			}
			else
			{
				$this->log->error("cannot change entry #".$ID." unsufficcient priviledges: ".$this->bf->canChangeEntry($data,$user->ID)." and ".$this->bf->canAddEntry($user,$newblog));
			}
			if(isset($referrer) and strlen($referrer))
				$out["location"]=$referrer;
			else
				$out["location"]="/users/".$newBlog->login."/m_".$ID."/";
		}
		else
		{
			if(strlen($diaryLogin))
				$diary=$this->context->getUser($diaryLogin);
			else
				$diary=$blog;
			if($this->bf->canAddEntry($user,$diary))
					{
					$this->log->debug("creating new entry, author ".$user->login.", owner ".$diary->login);
					$bodies=$this->bf->splitBodies($body);
					$body1=$bodies[0];
					$body2=$bodies[1];
					$ID=$this->bf->addEntry($diary->ID,$user->ID,now(), $access,  $userList, $comments, $title, $body1, $body2,$tags, $posX,$posY);
					if(isset($referrer) and strlen($referrer))
						$out["location"]=$referrer;
					else
						$out["location"]="/users/".$diary->login."/m_".$ID."/";
					}
				else
				{
					$this->log->error("cannot add entry #".$ID." unsufficcient priviledges: ".$this->bf->canChangeEntry($data,$user->ID)." and ".$this->bf->canAddEntry($user,$newblog));
					
					$out["location"]=$referrer;
				}
		}


		return $out;
 	}
}
