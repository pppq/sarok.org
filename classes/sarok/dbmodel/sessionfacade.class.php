<?
class sessionfacade {
	private $dbcon;
	private $log;
	private $db;
	private $context;
	private $logins;
	private $dayRange=7;
	private $key="fm4jklde84";

  function __construct() {
		global $day_range;
		$this->log=singletonloader::getInstance("log");
		$this->db=singletonloader::getInstance("mysql");
		$this->context=singletonloader::getInstance("contextClass");
		$this->log->info("sessionfacade initialized");
		$this->dayRange=$day_range;
  }
public function getSession($sessid=0){
		$q="select count()";
}

public function getUserCredentials($login,$pass){
	$q="select ID as num from users where login='$login' and pass=sha1('$pass')";
  	$ID=$this->db->querynum($q);
  	if($ID<1)
  	{
  		$this->log->warning("password is wrong for user!");
  		return 1;
  	}
  	else return $ID;
  	
}

  public function loginUser($login, $pass)
  {
	$this->log->debug2("Logging user $login with pass $pass");
	$q="select ID as num from users where login='$login' and pass=sha1('$pass')";
  	$ID=$this->db->querynum($q);
  	if($ID<1)
  	{
  		$this->log->warning("Login failed");
  		throw new LoginFailedException("Login failed");
  	}
  	//TODO: check whether user is banned

  	$this->context->session->changeUser($ID);
	$this->context->user=$this->context->requestUserDAL($ID);
	if($this->context->user->isTerminated=='Y')
		{
                $this->log->mail("Bot Login failed");
                throw new LoginFailedException("Login failed");

		}
	$num=$this->db->querynum("select count(*) from sessions where userID='$ID' and activationDate>now() - interval 1 hour");
	if($num<2)   //this is the first login to the system
	{
	  $user=$this->context->user;
	  $user->commentsLoaded="0";
	  $user->entriesLoaded="0";
	  $user->commentsOfEntriesLoaded="0";
	  $user->myCommentsLoaded="0";
	  $user->commit();
	   /*$this->cacheComments($ID);
	   $this->cacheEntries($ID);
	   $this->cacheCommentsOfEntries($ID);
	   $this->cacheMyComments($ID);*/
	}

  }
  
   public function encrypt($str,$key) {
	$str=addslashes($str);
	$key=addslashes($key);
   $q="select encode('$str','$key') as val";
   $passcrypt=$this->db->querynum($q);
   $passcrypt = base64_encode($passcrypt);
   $passcrypt = base64_encode($passcrypt);
 return $passcrypt;
 }
 
 //Decrypt Function
 public  function decrypt($str,$key) {
   $str=base64_decode($str);
   $str=base64_decode($str);
   $str = addslashes($str);
   $key=addslashes($key);
   
   $q="select decode('$str','$key') as val";
   $decrypted=$this->db->querynum($q);
 	return $decrypted;
 } 
 
 
 private function encryptLoginPass($login,$pass,$key)
 {
 	$ar=array($login,$pass);
 	$arstr=serialize($ar);
 	$out=$this->encrypt($arstr,$key);
 	return $out;
 }
 
private  function decryptLoginPass($str,$key)
 {
 	$arstr=$this->decrypt($str,$key);
 	$ar=unserialize($arstr);
 	return $ar;
 }
  
  
 private function getPass($login)
 {
 	
 	$q="select pass from users where login='$login'";
 	$pass=$this->db->querynum($q);
 	return ($pass);
 }
 private function checkPass($login,$pass)
 {
 	$q="select count(*) as num from users where login='$login' and pass='$pass'";
 	$num=$this->db->querynum($q);
 	return $num;
 }
 
 public function extractUserID($secret)
 {
 	$ar=$this->decryptLoginPass($secret,$this->key);
 	if(!is_array($ar) or sizeof($ar)!=2)
 	{
 		$this->log->security("Secret word extraction unsuccessfull, failed when decrypting");
 		return(0);
 	}
 	if($this->checkPass($ar[0],$ar[1]))
 	{
 		return($this->getUserCode($ar[0]));
 	}
 	else
 	{
 		$this->log->security("Secret word  $secret extraction unsuccesfull, failed when logging in with {$ar[0]}, {$ar[1]}");
 		return 0;
 	}
 }
 
 public function getSecret($login)
 {
 	$this->log->debug("getting secret for $login");
 	//$login=$this->getUserLogin($login);
 	$pass=$this->getPass($login);
 	$secret=$this->encryptLoginPass($login,$pass,$this->key);
 	return($secret);
 }

public function isUserLogged($ID)
{
	return $this->db->querynum("select count(*) from sessions where userID='$ID' and activationDate>now() - interval 1 hour");
}

public function getCachedComments($ID)
{
	$this->log->debug2("getCachedComments($ID) -->start");

	$bans=$this->context->user->bans;
	$bans=array_merge($bans,$this->context->user->banOfs);
	$this->log->debug("bans merged, number of bans is: ".sizeof($bans));
	$banstr=$friendstr="";
	if(sizeof($bans))
	{
		$bans=array_unique($bans);
		$banNames=$this->getUserLogins($bans);
		$bans=array();
		foreach($banNames as $name) $bans[]=$name; 
		for($i=0;$i<sizeof($bans);$i++) $bans[$i]="'".$bans[$i]."'";
		$banstr=" userID not in(".implode(",",$bans).") and diaryID not in (".implode(",",$bans).") and ";

		$this->log->debug("adding banned criteria into the string: $banstr");
	}


	$friendstr='';
	if($this->context->user->friendListOnly=='Y')
	{
		$friendstr="  (diaryID in (select login from friends left join users on friends.userID=users.ID where friendOf='$ID')) and ";
	}
	$q="select ID, userID, diaryID, entryID, createDate AS datum, access, body from cache_commentlist where category='comments' and $friendstr $banstr (ownerID='0' or ownerID='$ID') and createDate<=now() order by createDate desc limit 30";
	$rows=$this->db->queryall($q);
	if(sizeof($rows)<30 or $this->context->user->commentsLoaded=="0")
	{
		$this->log->warning("Not enough comments cached, reloading table");
		$this->db->mquery("delete from cache_commentlist where category='comments' and  ownerID='$ID'");
		$this->cacheComments($ID);
		$rows=$this->db->queryall($q);
	}
	for($i=0;$i<sizeof($rows);$i++)
	{ 
		$codes[]=$rows[$i]["ID"]; 
	//	$rows[$i]["diaryID"]=$this->getUserLogin($rows[$i]["DiaryID"]);
	}
	if(isset($codes))
	{
	$this->db->mquery("update cache_commentlist set lastUsed=now() where category='comments' and $friendstr ID in (".implode(",",$codes).")");
	}
	return($rows);
}

public function getCachedEntries($ID)
{
	$this->log->debug2("getCachedEntries($ID) -->start");

	$bans=$this->context->user->bans;
	$bans=array_merge($bans,$this->context->user->banOfs);
	$this->log->debug("bans merged, number of bans is: ".sizeof($bans));
	$banstr=$friendstr="";
	if(sizeof($bans))
	{
		$bans=array_unique($bans);
		$banNames=$this->getUserLogins($bans);
		$bans=array();
		foreach($banNames as $name) $bans[]=$name;
		for($i=0;$i<sizeof($bans);$i++) $bans[$i]="'".$bans[$i]."'";
		$banstr=" userID not in(".implode(",",$bans).") and ";

		$this->log->debug("adding banned criteria into the string: $banstr");
	}

	$friendstr='';
	if($this->context->user->friendListOnly=='Y')
	{
		$friendstr=" (userID in (select login from friends left join users on friends.userID=users.ID where friendOf='$ID')) and ";
	}
	$q="select ID, userID, diaryID,  createDate AS datum, access, body from cache_entrylist where $friendstr $banstr (ownerID='0' or ownerID='$ID') and createDate<=now() order by createDate desc limit 30";
	$rows=$this->db->queryall($q);
	if(sizeof($rows)<30 or $this->context->user->entriesLoaded=="0")
	{
		$this->log->warning("Not enough entries cached, reloading table");
		$this->db->mquery("delete from cache_entrylist where ownerID='$ID'");
		$this->cacheEntries($ID);
		$rows=$this->db->queryall($q);
	}
	for($i=0;$i<sizeof($rows);$i++) $codes[]=$rows[$i]["ID"];
	if(isset($codes))
	{
	$this->db->mquery("update cache_entrylist set lastUsed=now() where ID in (".implode(",",$codes).")");
	}
	return($rows);
}

public function getCommentsOfEntries($ID)
{
	$this->log->debug2("getCommentsOfEntries($ID) -->start");

	$bans=$this->context->user->bans;
	$bans=array_merge($bans,$this->context->user->banOfs);
	$this->log->debug("bans merged, number of bans is: ".sizeof($bans));
	$banstr=$friendstr="";
	if(sizeof($bans))
	{
		$bans=array_unique($bans);
		$banNames=$this->getUserLogins($bans);
		$bans=array();
		foreach($banNames as $name) $bans[]=$name;
		for($i=0;$i<sizeof($bans);$i++) $bans[$i]="'".$bans[$i]."'";
		$banstr=" userID not in(".implode(",",$bans).") and ";

		$this->log->debug("adding banned criteria into the string: $banstr");
	}

	$q="select ID, userID, diaryID, entryID, createDate AS datum, access, body from cache_commentlist where category='commentsOfEntries' and ownerID='$ID' and $banstr createDate<=now() order by createDate desc limit 30";
	$rows=$this->db->queryall($q);
	if(sizeof($rows)==0 or $this->context->user->commentsOfEntriesLoaded=="0")
	{
		$this->log->warning("Not enough comments cached, reloading table");
		$this->db->mquery("delete from cache_commentlist where category='commentsOfEntries' and ownerID='$ID'");
		$this->cacheCommentsOfEntries($ID);
		$rows=$this->db->queryall($q);
	}
	for($i=0;$i<sizeof($rows);$i++) $codes[]=$rows[$i]["ID"];
	if(isset($codes))
	{
	$this->db->mquery("update cache_commentlist set lastUsed=now() where category='commentsOfEntries' and ID in (".implode(",",$codes).")");
	}
	return($rows);
}

public function getMyComments($ID)
{
	$this->log->debug2("getMyComments($ID) -->start");
	$q="select ID, userID, diaryID, entryID, createDate AS datum, access, body from cache_commentlist where category='myComments' and ownerID='$ID' and createDate<=now() order by createDate desc limit 30";
	$rows=$this->db->queryall($q);
	if(sizeof($rows)==0 or $this->context->user->myCommentsLoaded=="0")
	{
		$this->log->warning("Not enough comments cached, reloading table");
		$this->db->mquery("delete from cache_commentlist where category='myComments' and ownerID='$ID'");
		$this->cacheMyComments($ID);
		$rows=$this->db->queryall($q);
	}
	for($i=0;$i<sizeof($rows);$i++) $codes[]=$rows[$i]["ID"];
	if(isset($codes))
	{
	$this->db->mquery("update cache_commentlist set lastUsed=now() where category='myComments' and ID in (".implode(",",$codes).")");
	}
	return($rows);
}

/*public function getMyComments($ID)   ---- old one, uses drect query, no caching
{
	$this->log->debug2("getMyComments($ID) -->start");
	$q="select c.ID, c.entryID, c.userID,date_format(c.createDate,'%k:%i:%s' ) as datum, c.body, e2.diaryID, e2.access
	from comments as c left join entries as e2 on c.entryID=e2.ID
	where c.userID='$ID'
	order by c.createDate desc limit 30";
	$data=$this->db->queryall($q);
	for($i=0;$i<sizeof($data);$i++)
	{
	$data[$i]["body"]=utf8_encode(substr(strip_tags($data[$i]["body"]),0,58));
	}
	return($data);
}*/

public function getUserLogin($ID)
{
	if(!isset($this->logins[$ID]))
	{
		$q="select login from users where ID='$ID'";
		$this->logins[$ID]=$this->db->querynum($q);
	}
		return($this->logins[$ID]);

}

public function getUserCode($login)
{
	if(!isset($this->userIDs[$login]))
	{
		$q="select ID from users where login='$login'";
		$this->userIDs[$login]=$this->db->querynum($q);
	}
		return($this->userIDs[$login]);
}

public function getUserLogins($logins)
{
	$out=array();
	if(is_array($logins) and sizeof($logins))
	{
	$q="select ID, login from users where ID in (".implode(",",$logins).")";
	$rows=$this->db->queryall($q);
		foreach($rows as $row)
		{
			$out[$row["ID"]]=$row["login"];
		}
	}
	return($out);
}

public function cacheComments($ID)
{
	$this->log->debug2("Caching comments for $ID");
	$user=$this->context->requestUserDAL($ID);
	if($user->commentsLoaded=="2") return;
	$user->commentsLoaded="2";
	$user->commit();
	$comments=$this->loadComments($ID);
	for($i=0;$i<sizeof($comments);$i++)
	{
		$row=$comments[$i];
		if($row["access"]=='ALL' or $row["access"]=='REGISTERED')
		{
		$this->log->debug("row #".$row["ID"]." is public access");
			$row["ownerID"]="0";
		}
		else
		{
			$row["ownerID"]=$ID;
		}
		$row["userID"]=$this->getUserLogin($row["userID"]);
		$row["diaryID"]=$this->getUserLogin($row["diaryID"]);
		$row["body"]=addslashes(iconv_substr(strip_tags($row["body"]),0,48));
	$q="insert into cache_commentlist values('comments','".$row["ID"]."', '".$row["ownerID"]."', '".$row["userID"]."', '".$row["diaryID"]."', '".$row["entryID"]."', '".$row["createDate"]."', '".$row["access"]."', '".$row["body"]."', now()) on duplicate key update lastUsed=now()";
	$this->db->mquery($q);
	}
	$user->commentsLoaded="1";
	$user->commit();
}

public function cacheEntries($ID)
{
	$this->log->debug2("Caching entries for $ID");
	$user=$this->context->requestUserDAL($ID);
	if($user->entriesLoaded=="2") return;
	$user->entriesLoaded="2";
	$user->commit();
	$comments=$this->loadEntries($ID);
	for($i=0;$i<sizeof($comments);$i++)
	{
		$row=$comments[$i];
		if($row["access"]=='ALL' or $row["access"]=='REGISTERED')
		{
		$this->log->debug("row #".$row["ID"]." is public access");
			$row["ownerID"]="0";
		}
		else
		{
			$row["ownerID"]=$ID;
		}
		$row["body"]=addslashes(iconv_substr(strip_tags($row["body"]),0,48));
		$row["userID"]=$this->getUserLogin($row["userID"]);
		$row["diaryID"]=$this->getUserLogin($row["diaryID"]);
	$q="insert into cache_entrylist values('".$row["ID"]."', '".$row["ownerID"]."', '".$row["userID"]."', '".$row["diaryID"]."', '".$row["createDate"]."', '".$row["access"]."', '".$row["body"]."', now()) on duplicate key update lastUsed=now()";
	$this->db->mquery($q);
	}
	$user->entriesLoaded="1";
	$user->commit();
}

public function cacheCommentsOfEntries($ID)
{
	$this->log->debug2("Caching comments for entries of $ID");
	$user=$this->context->requestUserDAL($ID);
	if($user->commentsOfEntriesLoaded=="2") return;
	$user->commentsOfEntriesLoaded="2";
	$user->commit();
	$comments=$this->loadCommentsOfEntries($ID);
	for($i=0;$i<sizeof($comments);$i++)
	{
		$row=$comments[$i];
		$row["ownerID"]=$ID;
		$row["body"]=addslashes(iconv_substr(strip_tags($row["body"]),0,48));
		$row["userID"]=$this->getUserLogin($row["userID"]);
		$row["diaryID"]=$this->getUserLogin($row["diaryID"]);
	$q="insert into cache_commentlist values('commentsOfEntries','".$row["ID"]."', '".$row["ownerID"]."', '".$row["userID"]."', '".$row["diaryID"]."', '".$row["entryID"]."', '".$row["createDate"]."', '".$row["access"]."', '".$row["body"]."', now()) on duplicate key update body='".$row["body"]."', lastUsed=now()";
	$this->db->mquery($q);
	}
	$user->commentsOfEntriesLoaded="1";
	$user->commit();
}

public function cacheMyComments($ID)
{
	$this->log->debug2("Caching comments for entries of $ID");
	$user=$this->context->requestUserDAL($ID);
	if($user->myCommentsLoaded=="2") return;
	$user->myCommentsLoaded="2";
	$user->commit();
	$comments=$this->loadMyComments($ID);
	for($i=0;$i<sizeof($comments);$i++)
	{
		$row=$comments[$i];
		$row["ownerID"]=$ID;
		$row["body"]=addslashes(iconv_substr(strip_tags($row["body"]),0,48));
		$row["userID"]=$this->getUserLogin($row["userID"]);
		$row["diaryID"]=$this->getUserLogin($row["diaryID"]);
	$q="insert into cache_commentlist values('myComments','".$row["ID"]."', '".$row["ownerID"]."', '".$row["userID"]."', '".$row["diaryID"]."', '".$row["entryID"]."', '".$row["createDate"]."', '".$row["access"]."', '".$row["body"]."', now()) on duplicate key update body='".$row["body"]."', lastUsed=now()";
	$this->db->mquery($q);
	}
	$user->myCommentsLoaded="1";
	$user->commit();
}

public function logout()
{
	$this->log->debug2("Logout -->");
	$ID=$this->context->user->ID;
	$num=$this->db->querynum("select count(*) from sessions where userID='$ID' and activationDate>now() - interval 1 hour");
	if($num<2)   //this is the only login to the system
	{
	   $this->cleanCache($ID);
	}
	$this->context->session->logout();
}

public function cleanCache($ID)
{
	$this->log->debug2("cleanCache($ID) -->");
	$q="delete from cache_commentlist where ownerID='$ID'";
	$this->db->mquery($q);
	$q="delete from cache_entrylist where ownerID='$ID'";
	$this->db->mquery($q);
	if(rand(0,100)<5)
	{
		$this->log->debug2("cleaning the whole table");
		$q="delete from cache_entrylist where lastUsed<now() - interval 1 hour";
		$this->db->mquery($q);
		$q="delete from cache_commentlist where lastUsed<now() - interval 1 hour";
		$this->db->mquery($q);
	}
}

private function genCommentsQuery($ID,$fields,$dateCriteria,$banstr,$banstr2,$friendstr)
{
	$user=$this->context->requestUserDAL($ID);
	$friendL=$user->friendOfs;
				if(sizeof($friendL))
				{
					$value=" e.diaryID in (".implode(", ",$friendL).")";
				}
				else
				{
						$value=" false";
				}

	$q="select $fields where $dateCriteria
		c.isTerminated='N' and  c.entryID in
	(
		SELECT  ID FROM entries as e where
		(userID='$ID' or diaryID='$ID' or (access='ALL') or
		(access='REGISTERED') or
		(access='FRIENDS' and $value ) or
		(access='LIST' and exists (select * from entryaccess where entryaccess.userID='$ID' and entryaccess.entryID=e.ID))
 )
$banstr2 \n $friendstr \n
)
$banstr
and c.dayDate<=now() and  c.createDate<now()
order by c.createDate desc";
return($q);
}

private function genEntriesQuery($ID,$fields,$dateCriteria,$banstr2,$friendstr)
{
	$user=$this->context->requestUserDAL($ID);
	$friendL=$user->friendOfs;
				if(sizeof($friendL))
				{
					$value=" e2.diaryID in (".implode(", ",$friendL).")";
				}
				else
				{
						$value=" false";
				}

	$q="SELECT  $fields where
		isTerminated='N' and  $dateCriteria
		(userID='$ID' or diaryID='$ID' or (access='ALL') or
		(access='REGISTERED') or
		(access='FRIENDS' and $value ) or
		(access='LIST' and exists (select * from entryaccess where entryaccess.userID='$ID' and entryaccess.entryID=e2.ID))
)
$banstr2 $friendstr
and e2.dayDate<=now() and  e2.createDate<now()
order by e2.createDate desc";
return($q);
}

public function loadComments($ID)
{
	$friendstr=$dateCriteria=$banstr=$banstr2="";

	$this->log->debug2("Loading comments");
	$fields="c.ID, c.entryID, c.userID,c.createDate, c.body, e2.diaryID, e2.access from comments as c left join entries as e2 on c.entryID=e2.ID";
	$user=$this->context->requestUserDAL($ID);
	$bans=$user->bans;
	$bans=array_merge($bans,$user->banOfs);
	$this->log->debug("bans merged, number of bans is: ".sizeof($bans));
	$banstr=$friendstr="";
	if(sizeof($bans))
	{
		$bans=array_unique($bans);
		for($i=0;$i<sizeof($bans);$i++) $bans[$i]="'".$bans[$i]."'";
		$banstr=" and c.userID not in (".implode(",",$bans).") ";
		$banstr2="and e.diaryID not in(".implode(",",$bans).") and e.diaryID not in (".implode(",",$bans).") ";

		$this->log->debug("adding banned criteria into the string: $banstr");
	}
	if($user->friendListOnly=='Y')
	{
		$friendstr=" and (e.diaryID in (select userID from friends where friendOf='$ID')) ";
	}
	$dateCriteria=" c.dayDate>=now()-interval {$this->dayRange} day and ";

	$q=$this->genCommentsQuery($ID,$fields,$dateCriteria,$banstr,$banstr2,$friendstr);
	$data=$this->db->queryall($q." limit 30");
	if(sizeof($data)<30)
	{
		$this->log->warning("Number of records is ".sizeof($data));
		$q=$this->genCommentsQuery($ID,$fields,"",$banstr,$banstr2,$friendstr);
		$data=$this->db->queryall($q." limit 30");
	}
	return $data;
}


public function loadEntries($ID)
{
	$friendstr=$dateCriteria=$banstr=$banstr2="";
	$this->log->debug2("Loading comments");
	$fields="e2.ID, e2.userID, e2.diaryID, e2.createDate, concat(e2.title, ' ',e2.body) as body, e2.access from entries as e2 ";
	$user=$this->context->requestUserDAL($ID);
	$bans=$user->bans;
	$bans=array_merge($bans,$user->banOfs);
	$this->log->debug("bans merged, number of bans is: ".sizeof($bans));
	$banstr=$friendstr="";
	if(sizeof($bans))
	{
		$bans=array_unique($bans);
		for($i=0;$i<sizeof($bans);$i++) $bans[$i]="'".$bans[$i]."'";
		$banstr2="and e2.diaryID not in(".implode(",",$bans).") and e2.diaryID not in (".implode(",",$bans).") ";

		$this->log->debug("adding banned criteria into the string: $banstr2");
	}
	if($user->friendListOnly=='Y')
	{
		$friendstr=" and (e2.userID in (select userID from friends where friendOf='$ID')) ";
	}
	$dateCriteria=" e2.dayDate>=now()-interval {$this->dayRange} day and ";

	$q=$this->genEntriesQuery($ID,$fields,$dateCriteria,$banstr2,$friendstr);
	$data=$this->db->queryall($q." limit 30");
	if(sizeof($data)<30)
	{
		$this->log->warning("Number of records is ".sizeof($data));
		$q=$this->genEntriesQuery($ID,$fields,"",$banstr2,$friendstr);
		$data=$this->db->queryall($q." limit 30");
	}
	return $data;
}

public function loadCommentsOfEntries($ID)
{
	$banstr=$banstr2="";
	$this->log->debug2("Loading comments");
	if(!$this->db->querynum("SELECT count( * ) as num FROM entries WHERE (userID = '$ID' or diaryID= '$ID') and isTerminated='N'"))
	{
		$this->log->debug("No entries written by user, returning empty array");
		return(array());
	}
	$user=$this->context->requestUserDAL($ID);
	$bans=$user->bans;
	$bans=array_merge($bans,$user->banOfs);
	$this->log->debug("bans merged, number of bans is: ".sizeof($bans));
	$banstr=$friendstr="";
	if(sizeof($bans))
	{
		$bans=array_unique($bans);
		for($i=0;$i<sizeof($bans);$i++) $bans[$i]="'".$bans[$i]."'";
		$banstr=" and c.userID not in (".implode(",",$bans).") ";

		$this->log->debug("adding banned criteria into the string: $banstr");
	}
	$dayRange=$this->dayRange*3;
	$dateCriteria=" c.dayDate>=now() - interval $dayRange day and ";

	$q="select c.ID, c.entryID, c.userID,c.createDate, c.body, e2.diaryID, e2.access
from comments as c left join entries as e2 on c.entryID=e2.ID
where $dateCriteria c.isTerminated='N' and e2.isTerminated='N' and (e2.userID='$ID' or e2.diaryID='$ID') $banstr and c.dayDate<=now() and c.createDate<now()
order by c.createDate desc";
	$data=$this->db->queryall($q." limit 30");
	if(sizeof($data)<30)
		{
			$this->log->warning("Number of records is ".sizeof($data));
			$q="select c.ID, c.entryID, c.userID,c.createDate, c.body, e2.diaryID, e2.access
			from comments as c left join entries as e2 on c.entryID=e2.ID
			where  (e2.userID='$ID' or e2.diaryID='$ID') and e2.isTerminated='N' $banstr and c.isTerminated='N' and c.createDate<now()
			order by c.createDate desc";
		$data=$this->db->queryall($q." limit 30");
		}

	return $data;
}

public function loadMyComments($ID)
{
	$this->log->debug2("getMyComments($ID) -->start");
	$q="select c.ID, c.entryID, c.userID,c.createDate, c.body, e2.diaryID, e2.access
	from comments as c left join entries as e2 on c.entryID=e2.ID
	where c.userID='$ID' and  c.isTerminated='N' and c.createDate<now() and e2.isTerminated='N'
	order by c.createDate desc limit 30";
	$data=$this->db->queryall($q);
	return($data);
}




}
?>
