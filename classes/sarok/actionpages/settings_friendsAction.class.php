<?php

class settings_friendsAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->log->debug("Running defaultAction");

        $user = $this->context->user;
        
		$friends = $this->context->getUserLinks($user->ID, 'friends')->toArray();
        $this->log->debug("Retrieved " . count($friends) . " friends");
        $friendOfs = $this->context->getUserLinks($user->ID, 'friendOfs')->toArray();

		$bans = $this->context->getUserLinks($user->ID, 'bans')->toArray();
		$this->log->debug("Retrieved " . count($bans) . " bans");
        $banOfs = $this->context->getUserLinks($user->ID, 'banOfs')->toArray();

		$out["friends"] = $friends;
		$out["friendOfs"] = $friendOfs;
		$out["bans"] = $bans;
		$out["banOfs"] = $banOfs;

		$out["friendLogins"] = $this->sessionFacade->getUserLogins($friends);
		$out["banLogins"] = $this->sessionFacade->getUserLogins($bans);

		return $out;
 	}
}
