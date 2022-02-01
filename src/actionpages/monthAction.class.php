<?php

class monthAction extends Action{
protected $sessionFacade;
protected $db,$bf;
 	function execute()
 	{
		$this->db=singletonloader::getInstance("mysql");
		$this->bf=singletonloader::getInstance("blogfacade");
		$this->log->debug("Running monthAction");
		$login=$this->context->user->login;
if(isset($this->context->ActionPage->month))
{
	$month=$this->context->ActionPage->month;
	$year=$this->context->ActionPage->year;
}
else
{
	$year=year();
	$month=month();
}

		$this->log->debug("Getting BlogDays");
		$rows=$this->bf->getBlogDays($this->context->blog,$year,$month,$this->context->ActionPage->params["friends"]);
		
		for($i=0;$i<sizeof($rows);$i++)
		{
			extract($rows[$i]);
			$days["$y-$m-$d"]=$rows[$i];
			$days["$y-$m-$d"]["link"]="/users/".$this->context->blog->login."/$y/$m/$d/";
			if($this->context->ActionPage->params["friends"]==true)
			{
				$days["$y-$m-$d"]["link"]="/users/".$this->context->blog->login."/friends/$y/$m/$d/";
			}
			$this->log->debug($days["$y-$m-$d"]["link"]);
		}
		if(isset($days))
			$out["days"]=$days;
		$out["y"]=$year;
		$out["m"]=$month;
		return $out;
 	}
}
?>