<?
class mailfacade {
	private $log;
	private $db,$df,$sf;
	private $context;
	public $query;

	private $ownBlogTab = array ();
	private $friendBlogTab = array ();

	function mailfacade() {
		global $day_range;
		$this->log = singletonloader :: getInstance("log");
		$this->db = singletonloader :: getInstance("mysql");
		$this->df = singletonloader :: getInstance("dbfacade");
		$this->sf = singletonloader :: getInstance("sessionfacade");
		$this->context = singletonloader :: getInstance("contextClass");

		$this->log->info("mailfacade initialized");

	}


/**
	 * PRIVATES
	 */

	public function sendMails($recipientID, $senderID, $title, $body, $replyOn = 0)
	{
		foreach($recipientID as $recipient)
		{
			$this->sendMail($recipient, $senderID, $title, addslashes($body), $replyOn);
			//$this->getMailPartners($recipient);
		}
		//$this->getMailList("",$senderID,"","",0, 50, true);
		//$this->getMailList($senderID,"","","",0, 50, false);
	}

	/**
	 *  sendMail($recipientID,$senderID,$replyOn)
	 *  2005.09.25.
	 */

	public function sendMail($recipientID, $senderID, $title, $body, $replyOn = 0) {
		global $mail_secretWord;
		$this->log->info("sendMail($recipientID,$senderID,$title,$body,$replyOn)");
		try {
			if (!$this->df->userExists($recipientID) or !$this->df->userExists($senderID)) {
				$this->log->error("User $recipientID or user $senderID does not exist");
				throw new dbFacadeException("User $recipientID or user $senderID does not exist");
			}
			$this->db->mquery("insert into mail(recipient, sender,replyOn,title, body, Date) values ('$recipientID','$senderID','$replyOn','$title',encode('$body','$mail_secretWord'),now())");
			$mailID = mysqli_insert_id();
			$this->df->setUserProperty($recipientID, "newMail",  $this->getNewMailCount($recipientID));
			$this->unCacheMailList($recipientID);
			$this->unCacheMailList($senderID);
			$this->unCachePartners($senderID);
			//$this->updatePartnerCache($senderID,$recipientID,1);
			$this->log->debug("Mail inserted, ID is $mailID");
			return ($mailID);
		} catch (mysqlException $e) {
			$this->log->error("sendMail($recipientID,$senderID,$title,$body,$replyOn)");

		}
	}
	
	function updatePartnerCache($userID, $recipientID,$count=1)
	{
		$this->log->info("updatePartnerCache($userID, $recipientID,$count)");
		$plist=$this->getCachedPartners($recipientID);
		if(!$plist) return false;
		$list=$plist["list"];
		
		foreach($list as $k=>$v)
		{
			$this->log->debug("$k -> $v");
		}
		$userLogin=$this->sf->getUserLogin($userID);
		$list[$userLogin]+=$count;
		$this->log->debug("list[$userLogin]={$list[$userLogin]}");
		if($list[$userLogin]<=0)
		{
			unset($list[$userLogin]);
			$this->log->debug("list[$userLogin] deleted");
		}
		$plist["list"]=$list;
		$plist["max"]=max($plist["max"],$list[$userLogin]);
		$plist["min"]=min($plist["max"],$list[$userLogin]);
		$this->cachePartners($plist,$recipientID);
	}

	private function getNewMailCount($userID)
	{
		$q="select count(*) as num from mail where recipient='$userID' and isRead='N' and isDeletedByRecipient='N' and ((sender!=1027 and sender!=2296) or recipient!=4546)";
		$num=$this->db->querynum($q);
		return $num;
	}

	/**
	 *  getMail($mailID)
	 *  2005.09.25.
	 */

	public function getMail($mailID,$readerID) {
		global $mail_secretWord;
		$this->log->info("getMail($mailID)");
		try {
			$row = $this->db->queryone("select ID, recipient, sender, date, title, decode(body,'$mail_secretWord') as body, isRead, isDeletedByRecipient, isDeletedBySender, replyOn from mail where ID='$mailID' and ((recipient='$readerID' and isDeletedByRecipient='N') or (sender='$readerID' and isDeletedBySender='N')) limit 1");
			if (!is_array($row)) {
				$this->log->error("No such mail $mailID");
				throw new dbFacadeException("No such mail $mailID");
			}
			return $row;
		} catch (mysqlException $e) {
			$this->log->error("getMail($mailID): failure");

		}

	}

	/**
	 *  getMailList($options,$startfrom,$num)
	 *  2005.09.25.
	 */
/*
	public function getMailList($options, $likeOptions, $startfrom, $num) {
		$this->log->info("getMailList($options,$startfrom,$num)");
		try {
			$q = "select ID, recipient, sender, date, title, isRead, isDeletedByRecipient, isDeletedBySender, replyOn from mail where sender!=1027 ";
			if (is_array($options)) {
				foreach ($options as $key => $value) {
					$q .= " and $key='$value' ";
				}
			}

			if (is_array($likeOptions)) {
				foreach ($likeOptions as $key => $value) {
					$q .= " and $key like '$value' ";
				}
			}
			$q .= " order by date desc limit $startfrom, $num";
			return ($this->db->queryall($q));
		} catch (mysqlException $e) {
			$this->log->error("getMailList($options,$startfrom,$num): failure");

		}
	}*/

	public function getMailList($senderID,$recipientID,$date,$keyword,$startfrom, $num, $isIncoming = true) {
		global $mail_secretWord;
		$this->log->info("getMailList($senderID,$recipientID,$startfrom,$date,$keyword,$num)");
		$isMainPage=false;
		if($this->isInBox($senderID,$recipientID,$date,$keyword,$startfrom, $isIncoming))
		{
			$isMainPage=true;
			$inbox=true;
			$rows=$this->getCachedMailList($recipientID,true);
			if(is_array($rows)) return $rows;
		}elseif($this->isOutBox($senderID,$recipientID,$date,$keyword,$startfrom, $isIncoming))
		{
			$isMainPage=true;
			$inbox=false;
			$rows=$this->getCachedMailList($senderID,false);
			if(is_array($rows)) return $rows;
		}
		try {
			$q = "select ID, recipient, sender, `date`, title, isRead, isDeletedByRecipient, isDeletedBySender, replyOn from mail where 1 ";
			if(strlen($senderID))
			{
				$q.=" and sender='$senderID' ";
			}
			if(strlen($keyword))
			{
				$q.=" and concat(title,decode(body,'$mail_secretWord')) like '%$keyword%' ";
			}			
			if(strlen($recipientID))
			{
				$q.=" and recipient='$recipientID' ";
			}
			if(strlen($date))
			{
				$q.=" and DATE_FORMAT(date,'%Y-%m-%d')='$date' ";
			}
			if($isIncoming)
			{
				$q.=" and isDeletedByRecipient='N' and (sender!=1027 and sender!=2296) ";
			}
			else
			{
					$q.=" and isDeletedBySender='N' ";
			}

			$q .= " order by date desc limit $startfrom, $num";
			$rows=$this->db->queryall($q);
			if($isMainPage)
			{
				if($inbox)
					$this->cacheMailList($rows,$recipientID,true);
				else
					$this->cacheMailList($rows,$senderID,false);
			}
			return ($rows);
		} catch (mysqlException $e) {
			$this->log->error("getMailList($options,$startfrom,$num): failure");

		}
	}

	private function isInBox($senderID,$recipientID,$date,$keyword,$startfrom,$isIncoming)
	{
		$l=(!strlen($senderID) and !strlen($date) and !strlen($keyword) and $startfrom==0  and $isIncoming);
		if($l) $this->log->debug("This is inbox");
		return($l);
	}

	private function isOutBox($senderID,$recipientID,$date,$keyword,$startfrom,$isIncoming)
	{
		$l=(!strlen($recipientID) and !strlen($date) and !strlen($keyword) and $startfrom==0  and !$isIncoming);
		if($l) $this->log->debug("This is outbox");
		return $l;
	}

	/**
	 *
	 *  2005.09.25.
	 */

	public function setReadFlag($mailID) {
		$this->log->info("setReadFlag($mailID)");
		try {
			$res = $this->db->mquery("update mail set isRead='Y' where ID='$mailID' limit 1");
			if ($this->db->mysqli_affected_rows() != 0) {
				$this->log->debug("Affected rows: ".$this->db->mysqli_affected_rows());
				$row = $this->db->queryone("select sender, recipient from mail where ID='$mailID' limit 1");
				$this->df->setUserProperty($row["recipient"], "newMail", $this->df->getUserProperty($row["recipient"], "newMail") - 1, true);
				$this->unCacheMailList($row["recipient"]);
				$this->unCacheMailList($row["sender"]);
			}
		} catch (mysqlException $e) {
			$this->log->error("setReadFlag($mailID): failure");

		}

	}

		public function deleteMailByUserID($mailID,$recipientID)
			{
				$this->log->info("deleteMailByUserID($mailID,$recipientID)");
				$q="select sender from mail where ID='$mailID' and (recipient='$recipientID' or sender='$recipientID') and recipient!=sender limit 1";
				$userID=$this->db->querynum($q);
				if($userID)
				{
					$this->log->debug("Mail exists");
					if($userID!=$recipientID)
					{
						$this->log->debug("Deleting from the recipient side");
						$this->deleteMail($mailID,true);
						$this->unCacheMailList($userID);
						$this->df->setUserProperty($recipientID, "newMail",  $this->getNewMailCount($recipientID));

					}
					else
					{
						$this->log->debug("Deleting from the sender side");
						$this->deleteMail($mailID,false);
					}
					$this->unCachePartners($recipientID);
					$this->updatePartnerCache($userID,$recipientID,-1);
					$this->getMailPartners($recipientID);
					$this->unCacheMailList($recipientID);

				return true;
				}
				else{
					$q="select recipient from mail where ID='$mailID' and (recipient='$recipientID' or sender='$recipientID') and recipient=sender limit 1";
					$num=$this->db->querynum($q);
					if($num>0)
						{
							$this->df->setUserProperty($recipientID, "newMail",  $this->getNewMailCount($recipientID));
							$this->deleteMail($mailID,true);
							$this->deleteMail($mailID,false);
							//$this->unCachePartners($recipientID);
							$this->updatePartnerCache($userID,$recipientID,-1);
						//	$this->getMailPartners($userID);
							$this->unCacheMailList($recipientID);
						return true;
						}
					else
						{
							$this->log->security("Wrong mail!");
					return false;
				}
				}

			}

	/**
	 *  deleteMail($mailID,$byRecipient=true)
	 *  2005.09.25.
	 */

	private function deleteMail($mailID, $byRecipient = true) {
		$this->log->info("deleteMail($mailID,$byRecipient)");
		try {
			if ($byRecipient) {
				$field = "isDeletedByRecipient";
				$this->db->mquery("update mail set isRead='Y' where ID='$mailID' limit 1");

			} else {
				$field = "isDeletedBySender";
			}
			$this->db->mquery("update mail set $field='Y' where ID='$mailID' limit 1");

		} catch (mysqlException $e) {
			$this->log->error("deleteMail($mailID,$byRecipient): failure");

		}

	}

	/**
	 * getMailPartners($userID)
	 *  2005.09.25.
	 */

	public function getMailPartners($userID) {
		$this->log->info("getMailPartners($userID)");
		try {
			if($out=$this->getCachedPartners($userID))
			{
				$this->log->debug("Partners are already cached");
				return $out;
			}
			/*
			$q = "SELECT sender, count(*) as numRec FROM `mail` WHERE recipient='$userID' and isDeletedByRecipient='N' group by sender";
			$rows=$this->db->queryall($q);
			foreach($rows as $r){
				$out[$r["sender"]]=$r["numRec"];
			}
			$partners=array();
*/
 			if(is_array($out))
 			{
 			$min=10000000;
 			$max=0;
	 			$userLogins=$this->sf->getUserLogins(array_keys($out));
	 			foreach($out as $uID=>$num)
	 			{
	 			$min=min($num,$min);
	 			$max=max($num,$max);
	 			$partners[$userLogins[$uID]]=$num;
	 			}
 			}
			$p["list"]=$partners;
			$p["min"]=$min;
			$p["max"]=$max;
			$this->cachePartners($p,$userID);
			return ($p);
		} catch (mysqlException $e) {
			$this->log->error(": failure");

		}

	}

	private function cacheMailList($mails,$userID,$inbox=true)
	{
		$this->log->debug("serializing maillist for the ID $userID");
		if($inbox)
		{
			$file=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_in_$userID";
		}
		else
		{
			$file=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_out_$userID";
		}

		$s = serialize($mails);
  		$fp = fopen($file, "w");
  		fwrite($fp, $s);
  		fclose($fp);
	}

	private function unCacheMailList($userID)
	{
		$this->log->debug("deleting cache of maillists for the ID $userID");
			$file1=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_in_$userID";
			$file2=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_out_$userID";

		if(file_exists($file1)) unlink($file1);
		if(file_exists($file2))  unlink($file2);
	}

	private function getCachedMailList($userID,$inbox=true)
	{
		$this->log->debug("getting maillist from cache for ID $userID");
		if($inbox)
		{
			$this->log->debug("checking inbox");
			$file=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_in_$userID";
		}
		else
		{
			$this->log->debug("checking outbox");
			$file=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_out_$userID";
		}
		if(file_exists($file))
		{
			$s = implode("", @file($file));
  			$mails = unserialize($s);
			return $mails;
		}
		else{
			return false;
		}
	}

	private function cachePartners($list,$userID)
	{
		$this->log->debug("serializing partners for the ID $userID");

		$file=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_pl_$userID";
		$s = serialize($list);
  		$fp = fopen($file, "w");
  		fwrite($fp, $s);
  		fclose($fp);
	}

	private function getCachedPartners($userID)
	{
		$this->log->debug("getting partners from cache for ID $userID");

			$file=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_pl_$userID";
		if(file_exists($file))
		{
			$s = implode("", @file($file));
  			$list = unserialize($s);
			return $list;
		}
		else{
			return false;
		}
	}

	private function unCachePartners($userID)
	{
		$this->log->debug("uncaching partners for the ID $userID");

		$file=$_SERVER["DOCUMENT_ROOT"]."/../cache/mail/m_pl_$userID";
		if(file_exists($file))  unlink($file);
	}

}
?>
