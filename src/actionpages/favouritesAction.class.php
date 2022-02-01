<?php
class favouritesAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$this->log->debug("Running favouritesAction");
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		if(!sizeof($this->context->params))
		{
		$favourites=$this->context->user->favourites;
		$this->log->debug("size of favourites is: ".sizeof($favourites));
		$newFavourites=$this->context->user->newFavourites;
		$this->log->debug("size of new favourites is: ".sizeof($newFavourites));
		foreach($favourites as $i=>$f)
		{
			$favourites[$i]["userLogin"]=$this->sessionFacade->getUserLogin($f["userID"]);
			$favourites[$i]["diaryLogin"]=$this->sessionFacade->getUserLogin($f["diaryID"]);
		}

		foreach($newFavourites as $i=>$f)
		{
			$newFavourites[$i]["userLogin"]=$this->sessionFacade->getUserLogin($f["userID"]);
			$newFavourites[$i]["diaryLogin"]=$this->sessionFacade->getUserLogin($f["diaryID"]);
		}

		$out["favourites"]=$favourites;
		$out["newFavourites"]=$newFavourites;
		}
		else
 		{
 			$this->log->debug("Location is ".$_SERVER['HTTP_REFERER']);
 			$out["location"]=$_SERVER['HTTP_REFERER'];
 		}
		return $out;
 	}
}
?>