<?php

class settings_statsAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
 		global $honapok;
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$statsFacade=singletonloader::getInstance("statsfacade");

		$this->log->debug("Running othertAction");
		$userID=$this->context->user->ID;
		$createDate=extractDate($this->context->user->createDate);
		$nowDate=extractDate(now());		
		$monthList=$this->getMonthList($createDate["year"],$createDate["month"],$nowDate["year"],$nowDate["month"]);
		$out["monthList"]=$monthList;
		$out["blogStat"]=$statsFacade->collectBlogInformation($userID);
		$out["lastCollectionDate"]=$statsFacade->loadLastCollectionDate();
		//$out["name"]=$this->context->user->Name;
		return $out;
 	}
 	
 	function getMonthList($y1,$m1,$y2,$m2){
 	global $honapok;
 	$startYear=12*$y1+$m1-1;
 	$endYear=12*$y2+$m2-1;
 	for($i=$startYear;$i<=$endYear;$i++)
 	{
 		$month=$i%12+1;
 		$year=($i-($i%12))/12;
 		$monthlist[]=array($year,$month);
 	}
 	return $monthlist;
 	}
}
?>