<?
class exportfacade {
	private $dbcon;
	private $log;
	private $db,$df,$sf;
	private $context;
	public $query;

	private $ownBlogTab = array ();
	private $friendBlogTab = array ();

	function exportfacade() {
		global $day_range;
		$this->log = singletonloader :: getInstance("log");
		$this->db = singletonloader :: getInstance("mysql");
		$this->df = singletonloader :: getInstance("dbfacade");
		$this->sf = singletonloader :: getInstance("sessionfacade");
		$this->context = singletonloader :: getInstance("contextClass");

		$this->log->info("exportfacade initialized");

	}


	public function getUserData($userID)
	{
		$rows=$this->db->queryall("select name, value from userdata where userID='$userID'");
		$out="";
		foreach($rows as $item)
		{
			$out.="<property name=\"".$item["name"]."\"><![CDATA[".$item["value"]."]]></property>\n";
		}
		$out="<properties>\n".$out."\n</properties>\n";
		return $out;

	}

	public function getEntry($entryID,$withComments=false)
	{

		$q="select * from entries where ID='$entryID' and isTerminated='N'";
		$row=$this->db->queryone($q);
		$out="";
		extract($row);
		$out.="<ID>$ID</ID>\n";
		$out.="<diaryID>".$this->sf->getUserLogin($diaryID)."</diaryID>\n";
		$out.="<userID>".$this->sf->getUserLogin($userID)."</userID>\n";
		$out.="<createDate>$createDate</createDate>\n";
		$out.="<modifyDate>$modifyDate</modifyDate>\n";
		$out.="<access>$access</access>\n";
		$out.="<comments>$comments</comments>\n";
		$out.="<title><![CDATA[$title]]></title>\n";
		$body=stripslashes($body);
		$out.="<body>\n<![CDATA[$body]]></body>\n";
		$out.="<body2><![CDATA[$body2]]></body2>\n";
		$q="select Name from categories where entryID='$ID'";
		$rows=$this->db->queryall($q);
		if(sizeof($rows))
		{

			$out.="<tags>\n";
			foreach($rows as $tag) $out.="<tag><![CDATA[".$tag["Name"]."]]></tag>\n";
			$out.="</tags>\n";
		}
		if($withComments){
			$out.=$this->getComments($entryID);
		}
		$out="<entry>\n$out</entry>\n";
		return($out);
	}

	public function getComments($entryID)
	{
		$q="select * from comments where isTerminated='N' and entryID='$entryID' order by createDate";
		$rows=$this->db->queryall($q);
		$out="";
		if(sizeof($rows))
		{
			$out="<commentList>\n";
			foreach($rows as $comment){
				$out.="<comment>\n";
				extract($comment);
				$out.="<id>$ID</id>\n";
				$out.="<entryID>$entryID</entryID>\n";
				$out.="<userID>".$this->sf->getUserLogin($userID)."</userID>\n";
				$out.="<createDate>$createDate</createDate>\n";
				$body=stripslashes($body);
				$out.="<body><![CDATA[$body]]></body>\n";
				$out.="</comment>\n";
			}
			$out.="</commentList>\n";
		}
		return $out;
	}

	public function getEntries($codes,$withComments=false)
	{
		$out="<entries>\n";
		foreach($codes as $code)
		{
			$out.=$this->getEntry($code,$withComments);
		}
		$out.="</entries>\n";
		return($out);
	}

	public function getDiary($diaryID,$codes,$withComments=false){
	 if($withComments)
		{
			$this->log->debug("With comments!");

		}
		else
		{
				$this->log->debug("Without comments!");
		}
	 $out="<diary ID=\"$diaryID\">\n";
	 $out.=$this->getUserData($diaryID);
	 $out.=$this->getEntries($codes,$withComments);
	 $out.="\n</diary>";
	 return($out);
	}
}
?>