<?php

class favouritesAP extends ActionPage{
    private $db;
    function init() {
    parent::init();
    /*$this->actionList["logout"][]="logoutform";*/
    /*$this->actionList["newmail"][]="checkMail";*/
    /*$this->actionList["menu"][]="menu";*/
    $this->db=singletonloader::getInstance("mysql");
    $p = $this->context->params;
     $this->log->debug(implode(",",$p));
    if(isset($p[1]) && ($p[0]=="add" || $p[0]=="del"))
    {
		$entryID=$p[1];
		$userID=$this->context->user->ID;
		$this->log->debug("Performing some action on favourites");
		if($p[0]=="add")
		{
			$q="insert into favourites (userID,entryID,lastVisited) values ('$userID','$entryID', now()) on duplicate key update lastVisited=now()";
		}
		else{
			$q="delete from favourites where userID='$userID' and entryID='$entryID' limit 1";
		}
		$this->db->mquery($q);
		$this->templateName="empty";
	}
	else
	{
		$this->log->debug("Showing list of favourites");
	}

    $this->actionList["main"]=array("favourites");
    //$this->actionList["friendlist"][]="logoutForm";

    return $this->actionList;
    }

    public function canRun()
    {
    	return $this->context->props["loggedin"];
    }
}
?>