<?php

class generalMapAction extends Action{
protected $sessionFacade,$blogFacade;
 	function execute()
 	{
		$mapAction=singletonloader::getInstance("settings_mapAction");
		$out=$mapAction->execute();
		return $out;
 	}
}
?>