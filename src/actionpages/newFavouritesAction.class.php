<?php
class newFavouritesAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$this->log->debug("Running newFavouritesAction");
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$favourites=$this->context->user->newFavourites;
		foreach($favourites as $i=>$f)
		{
			$favourites[$i]["userLogin"]=$this->sessionFacade->getUserLogin($f["userID"]);
			$favourites[$i]["diaryLogin"]=$this->sessionFacade->getUserLogin($f["diaryID"]);
		}
		$out["favourites"]=$favourites;
		return $out;
 	}
}
?>