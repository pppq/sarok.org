<?php

class entry_deleteAction extends Action{
protected $sessionFacade;
protected $bf,$mysql,$df;

 	function execute()
 	{
		$this->df=singletonloader::getInstance("dbfacade");
		$this->bf=singletonloader::getInstance("blogfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$blog=$this->context->blog;
		$user=$this->context->user;

		$this->log->debug("deleting");
		$category=0;
		$this->context->templateName="empty";
		$params=$this->context->params;
		$entryID=$this->context->ActionPage->entryCode;

		if(isset($entryID) and is_numeric($entryID))
		{
			$entry=$this->mysql->queryone("select * from entries where ID='$entryID' and isTerminated='N'");

			if(sizeof($params)>2)
			{
				$ID=$params[2];
				$comment=$this->mysql->queryone("select * from comments where ID='$ID' and entryID='$entryID' and isTerminated='N'");
				if($this->bf->canDeleteComment($comment,$entry,$user->ID))
				{
					$this->log->debug("deleting comment #".$ID);
					$this->bf->removeComment($ID);
					$out["location"]="/users/".$blog->login."/m_".$entryID."/";
				}
			}
			else
			{
				if($this->bf->canChangeEntry($entry,$user->ID))
				   $this->bf->removeEntry($entryID);
				$this->log->debug("deleting entry #".$entryID);
			$out["location"]="/users/".$blog->login."/";
			}

			//if($this->bf->candeleteEntry($data,$user->login));



		}



		return $out;
 	}
}
?>