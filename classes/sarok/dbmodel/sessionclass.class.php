<?php
class sessionclass
{
	public $ID, $userID;
	private $log, $db, $context,$banFC;
	public function __construct($ID, $IP)
	{
		$this->log= singletonloader :: getInstance("log");
		$this->db= singletonloader :: getInstance("mysql");
		$this->context= singletonloader :: getInstance("contextClass");
		$this->log->debug("checking session $ID");

		$this->getSession($ID, $IP);
	}

	public function getSession($ID, $IP)
	{
		global $skinName;
		

		if (strlen($ID) > 10)
		{
			//    	 $q="select userID from sessions where ID='$ID' and IP='$IP' and activationDate> now() - interval 1 hour limit 1";
			$q= "select userID from sessions where ID='$ID' and activationDate> now() - interval 1 hour limit 1";
			$userID= $this->db->querynum($q);
		}
		if (!isset ($userID) or $userID < 1)
		{
			$this->log->warning("session $ID does not exist, creating new");
			//if (rand(1, 100) < 5)
			$this->cleanup();
			$this->banFC=singletonloader:: getInstance("banfacade");
            $banReason = $this->banFC->getBanReason($IP);
			if ($banReason !== '')
			{
				//$this->log->security("Accessing website from banned IP $IP, reason is: ${banReason}");
				exit;
			}	
			$time= abs((int) ($this->log->getmicrotime() * 1000));
			//$this->log->debug("time is $time");
			if(is_numeric($ID) and $ID>0)
				{
				//$ID=$ID;
			//	$this->log->security("Reusing old session ID $ID");
				}
			else
			{			
				$ID= rand(10, 999).$time.rand(1, 9);
				$this->log->debug("generated ID $ID");
			}
			
			$userID= "1";
			
			
			$q= "insert into sessions (ID,userID,createDate,loginDate,activationDate,IP) values('$ID','$userID',now(),now(),now(),'$IP')";
			$this->db->mquery($q);
			
		}
		else
		{
			$q= "update sessions set activationDate=now() where ID='$ID' and userID='$userID' limit 1";
			$this->db->mquery($q);
			$q= "update users set activationDate=now() where ID='$userID' limit 1";
			$this->db->mquery($q);
		}
		$this->context->user= $this->context->requestUserDAL($userID);
		$this->ID= $ID;
		$this->userID= $userID;
		if(strlen($this->context->user->skinName))
			$skinName=$this->context->user->skinName;
		
		if ($userID != "1")
			$this->context->props["loggedin"]= true;
		else
			$this->context->props["loggedin"]= false;
			if ((isset( $PHP_AUTH_USER )) and (isset($PHP_AUTH_PW))) 
			{
				$this->logger->info("Authentication with user $PHP_AUTH_USER");
				$sf= singletonloader :: getInstance("sessionfacade");
				$ID=$sf->getUserCredentials( $PHP_AUTH_USER,$PHP_AUTH_PW);
				if($ID==1)
				{
					$this->log->security("password: $PHP_AUTH_PW","HTTP authentication failed for $PHP_AUTH_USER");
				}
				$this->ID= $ID;
			}
		return ($ID);
	}

	public function changeUser($ID)
	{
		$this->log->debug2("changeUser($ID)");
		$user=$this->context->requestUserDAL($ID);
		if($ID!=1 and $user->banned!="")
		{
		  $this->banFC=singletonloader:: getInstance("banfacade");
			$this->banFC->banIP($_SERVER["REMOTE_ADDR"],$user->banned);
			$this->log->security("banned user ".$user->login." (reason is: ".$user->banned.") tried to login from new IP ".$_SERVER["REMOTE_ADDR"]);
			return;		
		}
		$q= "update sessions set userID='$ID', loginDate=now() where ID='{$this->ID}' limit 1";
		$this->db->mquery($q);
		$this->context->user= $user;
		$this->context->user->loginDate= "";
		$this->context->user->commit();
		if ($ID == "1")
		{
			$this->context->props["loggedin"]= false;
		}
		else
		{
			$this->context->props["loggedin"]= true;
		}
		$this->log->debug("changeUser($ID) <-end");
	}

	public function logout()
	{
		$this->log->debug2("Logout");
		$this->changeUser("1");

	}

	public function cleanup()
	{
		$this->log->debug("Cleaning up sessions");
		$q= "delete from sessions where activationDate< (now() - interval 1 hour)";
		$this->db->mquery($q);
		$this->log->debug($this->db->mysqli_affected_rows()." sessions where deleted");
	}

	public function sendHeaders()
	{
		global $cookiedomain;
		global $myCookies;
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
		header("Set-Cookie: your_mommy={$this->ID}; path=/; domain=$cookiedomain");
		if(isset($myCookies) and is_array($myCookies))
				foreach($myCookies as $cname=>$cval)
				{
					$this->logger->debug("Setting cookie $cname: $cval");
					header("Set-Cookie: $cname=$cval; path=/; domain=$cookiedomain");
				}
		//setcookie("your_mommy",$this->ID, time()+7200, '/',  $cookiedomain);
	}
}
