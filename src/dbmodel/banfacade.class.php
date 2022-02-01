<?
class banfacade {
	private $log;
	private $db,$df,$sf;
	private $context;
	public $query;
	public $spamwords;
	public $bannedIPs;


	function banfacade() {
		global $day_range;
		$this->log = singletonloader :: getInstance("log");
		$this->sf = singletonloader :: getInstance("sessionfacade");
		
		$this->context = singletonloader :: getInstance("contextClass");
		$this->loadBannedIPs();
		$this->log->info("banfacade initialized");
		
}

	private function loadBannedIPs()
	{
		//format is next: IP;reason
		
		$this->log->info("loading bannedIPs");
		$banned=file($_SERVER["DOCUMENT_ROOT"]."/../cache/bannedip.txt");
		foreach($banned as $line)
		{
			$line=trim($line);
			$row=explode(";",$line);
			$this->bannedIPs[$row[0]]=$row[1];
		}
		$this->log->debug("Loaded ".sizeof($this->bannedIPs)." banned IPs");
	}
	
	public function  storeBannedIPs()
	{
		$this->log->info("storing banned ip's'");
		foreach($this->bannedIPs as $ip=>$reason)
		{
			$line="$ip;$reason\n";
			$s.=$line;
		}
		
		$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/../cache/bannedip.txt", "w");
  		fwrite($fp, $s);
  		fclose($fp);
	}
	
	public function isIPBanned($ip=false)
	{
		if($ip==false)
		{
			$ip=$_SERVER["REMOTE_ADDR"];
		}
		$this->log->info("Checking ip $ip for beeing banned");
		
		if( array_key_exists($ip, $this->bannedIPs))
		{
			$this->log->debug("$ip is banned, reason is: ".$this->bannedIPs[$ip]);
			return true;
		}
		else return false;
	}
					
	
	public function banIP($ip,$reason,$store=true)
	{
		$reason=strtr($reason,"\n\r\t;","   ");
		$this->log->info("Banning $ip. Reason: $reason");
		$this->bannedIPs[$ip]=$this->bannedIPs[$ip].$reason."@".date("Y-m-d G:i:s")." ";
		$this->db=singletonloader :: getInstance("mysql");		
		$q="delete from sessions where IP='$ip'";
		$this->db->mquery($q);
		if($store)
			$this->storeBannedIPs();
	}

	public function banUser($userID,$reason)
		{
			$banUser=$this->context->requestUserDAL($userID);
			$this->log->info("Banning user ".$banUser->login.",reason: $reason");
			$banUser->banned==date("Y-m-d G:i:s")." $reason";
			$banUser->commit();
			$ipList=$this->getAllIpForUser($userID);
			foreach($ipList as $ip)
			{
				$this->banIP($ip,$banUser->login." ban. $reason",false);
			}
			$this->storeBannedIPs();
			
		}
	public function getAllIpForUser($userID)
	{
		$this->db=singletonloader :: getInstance("mysql");
		$q="select distinct ip from  accesslog where userCode='$userID'";
		$rows=$this->db->queryall($q);
		$ipList=array();
		if(is_array($rows)){
		foreach($rows as $ip) 
		{	
			$this->log->debug(" $ip");
			$ipList[]=$ip;
		}
		}
		return($ip);
	}	
	

	
	
		


}
?>