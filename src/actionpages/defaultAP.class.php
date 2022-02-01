<?php

class defaultAP extends ActionPage{

    function init() {
    parent::init();
    /*$this->actionList["logout"][]="logoutform";*/
    /*$this->actionList["newmail"][]="checkMail";*/
    /*$this->actionList["menu"][]="menu";*/
    $this->actionList["leftMenu"][]="leftMenu";
    $this->actionList["main"][]="default";
    $this->actionList["leftMenu"][]="newFavourites";
    //$this->actionList["friendlist"][]="logoutForm";
    return $this->actionList;
    }

    public function canRun()
    {
    	return $this->context->props["loggedin"];
    }
}
?>