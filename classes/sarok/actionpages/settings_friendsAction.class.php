<?php

class settings_friendsAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("Running defaultAction");

		$friendOfs=array();
		$banOfs=array();
		$friends=$this->context->user->friends;
		if(is_array($friends) and sizeof($friends))
		{
			$this->log->debug("Retrieved ".sizeof($friends)." friends");
			$this->log->debug("sorting friends");
			//sort($friends);

			$friendOfs=$this->context->user->friendOfs;
		}
		else
		{
			$friends=array();
		}

		$bans=$this->context->user->bans;
		if(is_array($bans) and sizeof($bans))
		{
		$this->log->debug("Retrieved ".sizeof($bans)." bans");
		$this->log->debug("sorting bans");
		sort($bans);

		$banOfs=$this->context->user->banOfs;
		}
		else
		{
			$bans=array();
		}

		$out["friends"]=$friends;
		$out["friendLogins"]=$this->sessionFacade->getUserLogins($friends);
		$out["bans"]=$bans;
		$out["banLogins"]=$this->sessionFacade->getUserLogins($bans);
		$out["friendOfs"]=$friendOfs;
		$out["banOfs"]=$banOfs;
		return $out;
 	}
}
?>