<?php

class monthstat{
public $year;
public $month;
public $user;
public $ipList;
public $visits;
public $referrers;
public $keywords;
public $users;
private $sessions;
public $comments;
public $entries;
public $selfComments;
public $entryList;

function monthstat()
{
	$this->sessions=array();
}
	public function addIP($ip)
	{
		if(isset($this->ipList[$ip]))
			$this->ipList[$ip]++;
		else
			$this->ipList[$ip]=1;
	}
	
	public function addVisitor($day,$sessID)
	{
		if(in_array($sessID,$this->sessions)) return 0;
		$this->sessions[]=$sessID;
		$day=(int)$day;
		if(isset($this->visits[$day]))
			$this->visits[$day]++;
		else
			$this->visits[$day]=1;
		echo sizeof($this->sessions);
		return 1;
	}
	
	public function addEntryStat ($entryCode) {
		if(isset($this->entryList[$entryCode]))
			$this->entryList[$entryCode]++;
		else
			$this->entryList[$entryCode]=1;
	}
	
	public function addReferrer($referrer)
	{
		if(isset($this->referrers[$referrer]))
			$this->referrers[$referrer]++;
		else
			$this->referrers[$referrer]=1;
	}
	
	public function addKeyword($keyword)
	{
		if(isset($this->keywords[$keyword]))
			$this->keywords[$keyword]++;
		else
			$this->keywords[$keyword]=1;
	}
	
	public function addUser($user)
	{
		if(isset($this->users[$user]))
			$this->users[$user]++;
		else
			$this->users[$user]=1;
	}
	public function resetSessionData()
	{
		$this->sessions=array();
	}
	
	public function getTotalVisitors()
	{
		return array_sum($this->visits);
	}
	public function getTotalUsers()
	{
		return sizeof($this->ipList);
	}
	

}
?>