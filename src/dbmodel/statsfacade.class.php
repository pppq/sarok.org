<?
class statsfacade {
	private $log;
	private $db,$df,$sf,$bf;
	private $context;
	public $query;
	private $lastCollectionDate;
	private $ipTab;
	
	private $userMonths;
	function statsfacade() {
		$this->log = singletonloader :: getInstance("log");
		$this->db = singletonloader :: getInstance("mysql");
		$this->context = singletonloader :: getInstance("contextClass");
		error_reporting(0);
	}

	function putLogRecord()
	{
		list ($usec, $sec) = explode(" ", microtime());
		$usec=(int)($usec*1000);
		$nq=$this->db->counter+1;
		$referrer=addslashes(substr($_SERVER['HTTP_REFERER'],0,110));
		$action=addslashes(substr($_GET['p'],0,64));
		
		$q="insert into accesslog( datum, micros,                      sessid,                         action,                 referrer,                    ip,                         userCode,                           runTime,          numQueries) " .
			"values              (now(), '$usec', '{$this->context->session->ID}', '$action', '$referrer','{$_SERVER['REMOTE_ADDR']}','{$this->context->session->userID}','{$this->log->ms}', '$nq' )";
		$this->db->mquery($q);
	}
	
	public function collectData($limit=2500)
	{
		//include("stats/monthstat.php");
		global $cookiedomain;
		$this->sf = singletonloader :: getInstance("sessionfacade");
		
		$this->loadLastCollectionDate();
		//$newLast=now();
		$q="select datum, sessid, action, referrer, ip, userCode from accesslog where datum>='$this->lastCollectionDate' and action like 'users/_%' order by datum limit $limit";
		$result=$this->db->mquery($q);
//		$this->log->debug2("received ".sizeof($rows)." rows to process");
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
		{
			extract($row);
			$ip=$this->resolveIP($ip);
			if($this->isBot($ip)) continue;
			$this->log->debug("datum is $datum, sessid is $sessid, action is $action, ip is $ip");
			$newLast=$datum;
			$dat=explode(" ",$datum);
			$dat=explode("-",$dat[0]);
			$year=$dat[0];
			$month=$dat[1];
			$day=$dat[2];
			$user=$this->getUserCode($action);
			$day=(int)$day;
			$obj=$this->getUserMonth($user,$year,$month);
			
			$obj->addIP($ip);
			if(strpos($referrer,$cookiedomain)===false and !$this->isBot($referrer) and strlen($referrer)>5)
				$obj->addReferrer($referrer);
			if($obj->addVisitor($day,$sessid))
				{
					$this->log->debug("registered a new session");
				}
				$mCode=$this->getEntryCode($action);
				if($mCode)
				{
					$obj->addEntryStat($mCode);
				}
			$obj->addUser($this->sf->getUserLogin($userCode));
		}
		$this->log->debug2("data collecting finished successfully, new lastCollectionDate is $newLast");
		$this->lastCollectionDate=$newLast;
		$this->serializeAllMonthes();
		$this->storeLastCollectionDate();
	}
	
	public function collectBlogMonthInformation($userCode,$year,$month)
	{
		$month=(int)$month;
		$obj=$this->getUserMonth($userCode,$year,$month);
		/*
		if(is_array($obj->selfComments) and is_array($obj->entries) and is_array($obj->comments)) 
			{
				$this->log->debug("Blog Information is already collected");
				return;
			}*/
		$this->log->debug("Blog Month information is not collected yet");
		$date1="$year-$month-01";
		$date2="$year-$month-".getMaxDaysInMonth($year,$month);
		$this->log->debug2("Getting self comments stat for user $userCode on $year-$month");
		$q="select dayDate, count(*) as num from comments where userID='$userCode' and dayDate>='$date1' and dayDate<='$date2' group by dayDate";
		$rows=$this->db->queryall($q);
		foreach($rows as $row)
		{
			$dat=explode("-",$row["dayDate"]);
			$day=$dat[2];
			$days[(int)$day]=$row["num"];
			
		}
		$obj->selfComments=$days;
		unset($rows);
		unset($days);
		$this->log->debug2("Getting self entries stat for user $userCode on $year-$month");
		$q="select dayDate, count(*) as num from entries where (userID='$userCode' or diaryID='$userCode' ) and dayDate>='$date1' and dayDate<='$date2' group by dayDate";
		$rows=$this->db->queryall($q);
		foreach($rows as $row)
		{
			$dat=explode("-",$row["dayDate"]);
			$day=$dat[2];
			$days[(int)$day]=$row["num"];
			
		}
		$obj->entries=$days;
		unset($rows);
		unset($days);
		$this->log->debug2("Getting other comments stat for user $userCode on $year-$month");
		$q="select dayDate, count(*) as num from comments where dayDate>='$date1' and dayDate<='$date2' and  entryID in (select ID from entries where userID='$userCode' or diaryID='$userCode')  group by dayDate";
		$rows=$this->db->queryall($q);
		foreach($rows as $row)
		{
			$dat=explode("-",$row["dayDate"]);
			$day=$dat[2];
			$days[(int)$day]=$row["num"];
			
		}
		$obj->comments=$days; 
		if($year!=year() or (int)$month!=month())
			{
			$this->log->debug("The month is not current month, so the month can be serialized");
			$this->serializeUserMonth($obj);
			}
	}
	
	public function collectBlogInformation($userCode)
	{
		$this->log->debug2("Getting self comments number for user $userCode ");
		$q="select count(*) as num from comments where userID='$userCode'";
		$out["selfComments"]=$this->db->querynum($q);
	
	
		$this->log->debug2("Getting self public entries number for user $userCode");
		$q="select access, count(*) as num from entries where (userID='$userCode' or diaryID='$userCode' )group by access";
		$rows=$this->db->queryall($q);
		foreach($rows as $row)
		{
			$out["entries"][$row["access"]]=$row["num"];
		}
		$out["entries"]["total"]=array_sum($out["entries"]);

		/*$this->log->debug2("Getting other comments number for user $userCode");
		$q="select count(*) as num from comments where entryID in (select ID from entries where userID='$userCode')";
		$out["comments"]=$this->db->querynum($q);*/
		return $out;
	}
	
	private function isBot($ip)
	{
		if(strpos($ip,"google")) return true;
		if(strpos($ip,"search")) return true;
		if(strpos($ip,"yandex")) return true;
		if(strpos($ip,"msnbot")) return true;
		if(strpos($ip,"bloglines")) return true;
		if(strpos($ip,"msnbot")) return true;
		if(strpos($ip,"feed")) return true;
		if(strpos($ip,"altavista")) return true;
		if(strpos($ip,"crawl")) return true;
		if(strpos($ip,"bot")) return true;
		if(strpos($ip,"goliat")) return true;
		if(strpos($ip,"server.barthazi")) return true;
		
		
		return false;
	}
	
	
	private function resolveIP($ip)
	{
		//return $ip;
		if(!isset($this->ipTab[$ip]))
			{
				$hname=$this->gethost($ip);
				if($hname=="") $hname=$ip;
				$this->ipTab[$ip]=$hname;	
			}
	return $this->ipTab[$ip];
	}

private function gethost($ip)
{
 $host=gethostbyaddr($ip);
 $this->log->debug("hostname for $ip is $host");
 return $host;
 /*  $host = `host -W 1 $ip`;
   $host=end(explode(' ',$host));
   $host=substr($host,0,strlen($host)-2);
   $chk=split("\(",$host);
   if($chk[1]) return $ip;
   else return $host; */
}
	
	private function getUserCode($string)
	{
		$this->log->debug("extracting user Code from $string");
		if(ereg("users/([0-9A-Za-z_]+).*",$string,$regs))
		{
			$login=$regs[1];
			$this->log->debug("login is $login");
			$userCode=$this->sf->getUserCode($login);
			return($userCode);	
		}
		return(1);
	}
	
	private function getEntryCode($string)
	{
		$this->log->debug("extracting user Code from $string");
		if(ereg("users/([0-9A-Za-z_]+).*/m_([0-9]+)",$string,$regs))
		{
			$mCode=$regs[2];
			$this->log->debug("mCode is $mCode");
//			$userCode=$this->sf->getUserCode($login);
			return($mCode);	
		}
		return(0);	
	}
	
	public function getUserMonth($userCode, $year, $month)
	{
		$month=(int)$month;
		if(!isset($this->userMonths["$userCode-$year-$month"]))
		{
			$this->userMonths["$userCode-$year-$month"]=$this->unSerializeUserMonth($userCode,$year,$month);
	
		}
	
			return($this->userMonths["$userCode-$year-$month"]);
		
	}
	
	private function unSerializeUserMonth($userCode, $year, $month)
	{
		global $appRoot;
		$month=(int)$month;
		$file= $appRoot."/cache/stats/s$userCode-$year-$month";
		if (file_exists($file))
		{
			$this->log->debug($file." is cached, getting it from file");
			$s= implode("", @ file($file));
			$obj= unserialize($s);
			if($obj->year!=$year or $obj->month!=$month or $obj->user!=$userCode)
			{
				$this->log->error("serialized statmonth $userCode-$year-$smonth is not consistent!");
			}
		}
		else
		{
			$this->log->debug("$userCode $year $month does not exist yet, creating it");
			$obj=new monthstat();
			$obj->user=$userCode;
			$obj->year=$year;
			$obj->month=$month;
		}
		return $obj;
		
	}
	
	private function serializeAllMonthes()
	{
		$this->log->debug("Serializing ".sizeof($this->userMonths)." usermonthes");
		foreach($this->userMonths as $month)
		{
		//print_r($month);	
		    $this->serializeUserMonth($month);
		}
	}
	
	private function serializeUserMonth($obj)
	{
		global $appRoot;
		$userCode=$obj->user;
		$year=$obj->year;
		$month=(int)$obj->month;
		$this->log->debug("serializing stats for the $userCode, $year, $month");
		$file= $appRoot."/cache/stats/s$userCode-$year-$month";
		$obj->resetSessionData();
		$this->log->debug("Before serialize");
		$s=serialize($obj);
		$this->log->debug("serialization successfull, length of serialized string is: ".strlen($s));
		$fp= fopen($file, "w");
		$this->log->debug("Opened $file");
		if($fp)
		{
		$this->log->debug("Writing to file");
		fwrite($fp, $s);
		fclose($fp);
		$this->log->debug("Write successfull");
		}
		else
		{
		$this->logger->mail("Could not open $file");
		}
		$this->log->debug("SeraializeUserMonth <-end");
	}
	
	public function loadLastCollectionDate()
	{
		global $appRoot;
		$file= $appRoot."/cache/stats/LastCollectionDate";
		$this->log->debug("lastcollectiondate file is $file");
		if(file_exists($file))
		{
		$s= trim(implode("", @ file($file)));
		}
		else
		{
			$s="0000-00-00 00:00:00";
			$this->log->warning("$file does not exist");
		}
		$this->log->debug("Last collection date is: ".$s);
		
		$this->lastCollectionDate=$s;
		return $s;
	}
	private function storeLastCollectionDate()
	{
	global $appRoot;
		$file= $appRoot."/cache/stats/LastCollectionDate";
	
	//	$file= $_SERVER["DOCUMENT_ROOT"]."/../cache/stats/LastCollectionDate";
		$fp= fopen($file, "w");
		fwrite($fp, $this->lastCollectionDate);
		fclose($fp);
	}
	

   }
?>
