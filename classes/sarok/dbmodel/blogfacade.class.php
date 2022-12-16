<?
class blogfacade
{
	private $dbcon;
	private $log;
	private $db, $df, $sf;
	private $context;
	public $query;

	private $ownBlogTab= array ();
	private $friendBlogTab= array ();

	function __construct()
	{
		global $day_range;
		$this->log= singletonloader :: getInstance("log");
		$this->db= singletonloader :: getInstance("mysql");
		$this->df= singletonloader :: getInstance("dbfacade");
		$this->sf= singletonloader :: getInstance("sessionfacade");
		$this->context= singletonloader :: getInstance("contextClass");

		$this->log->info("blogfacade initialized");

		$this->fillTabs();
	}

	private function fillTabs()
	{
		$this->ownBlogTab["self"]= array ("ALL" => 1, "REGISTERED" => 1, "FRIENDS" => 1, "PRIVATE" => 1, "LIST" => 1);
		$this->ownBlogTab["friend"]= array ("ALL" => 1, "REGISTERED" => 1, "FRIENDS" => 1, "PRIVATE" => 0, "LIST" => 3);
		$this->ownBlogTab["registered"]= array ("ALL" => 1, "REGISTERED" => 1, "FRIENDS" => 0, "PRIVATE" => 0, "LIST" => 3);
		$this->ownBlogTab["all"]= array ("ALL" => 1, "REGISTERED" => 0, "FRIENDS" => 0, "PRIVATE" => 0, "LIST" => 0);

		$this->friendBlogTab["self"]= array ("ALL" => 1, "REGISTERED" => 1, "FRIENDS" => 2, "PRIVATE" => 0, "LIST" => 3);
		$this->friendBlogTab["friend"]= array ("ALL" => 1, "REGISTERED" => 1, "FRIENDS" => 2, "PRIVATE" => 0, "LIST" => 3);
		$this->friendBlogTab["registered"]= array ("ALL" => 1, "REGISTERED" => 1, "FRIENDS" => 2, "PRIVATE" => 0, "LIST" => 3);
		$this->friendBlogTab["all"]= array ("ALL" => 1, "REGISTERED" => 0, "FRIENDS" => 0, "PRIVATE" => 0, "LIST" => 0);

		$this->log->debug("tabs initialized");

	}

	public function getUserType($user, $blog)
	{
		$this->log->debug2("--> getUserType(".$user->ID.", ".$blog->ID, ")");
		if ($user->ID == "1")
		{
			return ("all");
		}
		if ($user->ID == $blog->ID)
		{
			return ("self");
		}
		if (in_array($user->ID, $blog->friends))
		{
			return ("friend");
		}
		return ("registered");
	}

	public function genBlogQuery($isFriendList, $user, $blog)
	{

		$this->log->debug2("--> genBlogQuery($isFriendList, ".$user->ID.", ".$blog->ID, ")");
		$userType= $this->getUserType($user, $blog);
		$this->log->debug("userType is $userType");
		$ID= $user->ID;

		if ($isFriendList)
		{
			$this->log->debug("using friendBlogTab");
			$tab= $this->friendBlogTab[$userType];
		}
		else
		{
			$this->log->debug("using ownBlogTab");
			$tab= $this->ownBlogTab[$userType];
			if ($userType == "self")
			{
				$this->log->debug("getting all entries");
				return ("  e.isTerminated='N' ");
			}
		}

		$grant= array ();
		$grant[]= " (e.diaryID='$ID' or e.userID='$ID') ";
		foreach ($tab as $key => $value)
		{

			if ($value == 3)
			{
				$value= " exists (select * from entryaccess where entryaccess.userID='$ID' and entryaccess.entryID=e.ID) ";
				//$value = " false";
			}
			elseif ($value == 2)
			{
				$friendL= $user->friendOfs;
				if (sizeof($friendL))
				{
					$value= " e.diaryID in (".implode(", ", $friendL).")";
				}
				else
				{
					$value= " false";
				}
				//$value = " exists (select * from friends where friends.userID='$ID' and friends.friendOf=e.diaryID and friendType='friend') ";

			}
			$this->log->debug("The value for $key is $value");
			if ($value != "0")
			{
				$grant[]= "\n (e.access='$key' and $value) ";
			}
		}
		$q= implode(" or ", $grant);
		$q= "(\n $q \n) and e.isTerminated='N'";
		if ($isFriendList)
		{
			$q .= " and createDate<now() ";
		}
		$this->log->debug("query is $q");
		return ($q);
	}

	public function analyzePath($p, $user, $blog)
	{
		global $search_keyword, $search_tagword;
		$ID= $user->ID;
		$skipnum= "10";
		$skipnum= $blog->entriesPerPage;
		$entryCode= $friends= $search= $keyword= $year= $month= $day= $tags= $tagword= "";
		$skip= 0;
		// Analyzing friends
		//print_r($p);
		if ($blog->login == "all" or (sizeof($p) > 0 and $p[0] == "friends"))
		{
			$this->log->debug("Displaying friends of the user");
			$friends= true;
			if ($p[0] == "friends")
			{
				array_shift($p);
			}
		}

		// Analyzing date

		if (sizeof($p) > 0 and is_numeric($p[0]))
		{
			$year= (int) $p[0];
			$this->log->debug("Year is set to ".$year);
			$entry= "entry_calendar";
			array_shift($p);
			if (sizeof($p) > 0 and is_numeric($p[0]))
			{
				$month= (int) $p[0];
				$entry= "entry";
				$this->log->debug("Month is set to ".$month);
				array_shift($p);
				if (sizeof($p) > 0 and is_numeric($p[0]))
				{
					$day= (int) $p[0];
					$this->log->debug("Day is set to ".$day);
					array_shift($p);
				}
			}

		}

		// Analyzing search

		if (sizeof($p) > 0 and $p[0] == "search")
		{
			$search= true;
			$this->log->debug("Search is set");
			array_shift($p);
			if (sizeof($p))
			{
				$keyword= $p[0];
				$this->log->debug("Keyword is set to ".$keyword);
				array_shift($p);
			}
			else
			{
				$keyword= $_POST["keyword"];
				$this->log->debug("Keyword is set to $keyword via _POST");
			}
			if (sizeof($keyword))
				$search_keyword= $keyword;
		}

		if (sizeof($p) > 0 and $p[0] == "tags")
		{
			$tags= true;
			$this->log->debug("tags is set");
			array_shift($p);
			if (sizeof($p))
			{
				$tagword= $p[0];
				$this->log->debug("Tagword is set to ".$tagword);
				array_shift($p);
			}
			else
			{
				$tagword= $_POST["tagword"];
				$this->log->debug("Tagword is set to $tagword via _POST");
			}
			if (sizeof($tagword))
				$search_tagword= $tagword;
		}

		// Analyzing Skip

		if (sizeof($p) > 0 and $p[0] == "skip")
		{
			$skip= 20;
			$this->log->debug("Skip is set");
			array_shift($p);
			if (sizeof($p))
			{
				$skip= (int) $p[0];
				$this->log->debug("Skip is set to ".$skip);
				array_shift($p);
			}
		}

		$out= array ("entryCode" => $entryCode, "friends" => $friends, "search" => $search, "keyword" => $keyword, "tags" => $tags, "tagword" => $tagword, "year" => $year, "month" => $month, "day" => $day, "skip" => $skip);
		return ($out);
	}

	public function isMainPage($ps)
	{
		$this->log->debug("isMainPage(".implode(", ", $ps).")");
		extract($ps);
		if (strlen($entryCode) or $skip > 0 or strlen($friends) or strlen($search) or strlen($tags) or strlen($year) or strlen($month) or strlen($day))
		{
			return (false);
		}
		else
			return true;

	}

	public function makePath($ID, $data)
	{
		$url= "/users/$ID/";
		if (!is_array($data))
			return ("/users/$ID/");
		extract($data);
		if ($friends)
			$url .= "friends/";
		if ($year > 0)
			$url .= "$year/";
		if ($month > 0)
			$url .= "$month/";
		if ($day > 0)
			$url .= "$day/";
		if ($search)
			$url .= "search/$keyword/";
		if ($tags)
			$url .= "tags/$tagword/";
		if ($skip > 0)
			$url .= "skip/$skip/";
		return ($url);

	}

	public function canViewEntry($entryID, $user, $blog)
	{
		$ID= $user->ID;
		$q= $this->genBlogQuery(false, $user, $blog);
		$q= "select * from entries as e where e.ID='$entryID' and e.diaryID='".$blog->ID."' and $q limit 1";
		$this->log->debug("Query is $q");
		return $q;
	}

	public function canViewEntries($entryIDList, $user, $blog,$onlyMapSet=false)
	{
		$ID= $user->ID;
		$q= $this->genBlogQuery(false, $user, $blog);
		if(is_array($entryIDList))
		{
		$entryIDs=implode(",",$entryIDList);
		}
		else
		{
		$entryIDs="false";	
		}
		if($onlyMapSet==true)
		{
			$mapCrit="and abs(e.posX)>0.0 and abs(posY)>0.0 ";
		}
		else
		{
			$mapCrit="and true ";
		}
		$q= "select * from entries as e where e.ID in ( $entryIDs ) and e.diaryID='".$blog->ID."' $mapCrit and $q";
		$this->log->debug("Query is $q");
		return $q;
		
	}
	
	public function getMapCoordsForBlog($user,$blog)
	{
		$ID= $user->ID;
		$q= $this->genBlogQuery(false, $user, $blog);
		$q= "select ID, title, createDate, posX,posY,body from entries as e where e.diaryID='".$blog->ID."' and abs(e.posX)>0.0 and abs(posY)>0.0 and $q";
		$this->log->debug("Query is $q");
		$rows=$this->db->queryall($q);
		return($rows);
	}
	
	public function getMapCoordsForMonthBlog($user,$blog,$year,$month)
	{
		$ID= $user->ID;
		$q= $this->genBlogQuery(false, $user, $blog);
		$q= "select ID, title, posX,posY,body from entries as e where e.diaryID='".$blog->ID."' and abs(e.posX)>0.0 and abs(posY)>0.0 and $q";
		$this->log->debug("Query is $q");
		$rows=$this->db->queryall($q);
		return($rows);
	}

	public function canViewEntry2($entry, $user)
	{
		$this->log->debug2("canViewEntry2((".$entry["diaryID"]."|".$entry["userID"]."),$user->ID)");
		$entryAuthor= $this->context->requestUserDAL($entry["userID"]);
		$entryOwner= $this->context->requestUserDAL($entry["diaryID"]);
		if($entryAuthor->ID==$entry["diaryID"] or $entryAuthor->ID==$user->ID) 
		{
		$this->log->debug($user->ID." can view the entry ".$entry["ID"]);
			return true;
		}
		
		if ((is_array($entryAuthor->bans) && in_array($user->ID, $entryAuthor->bans)) or (is_array($entryAuthor->banOfs) and in_array($user->ID, $entryAuthor->banOfs)))
		{
			$this->log->debug($user->ID." cannot view the entry, it is in the banlist of the author or diary or backwards".$entry["ID"]);
			return false;
		}
		if ($entry["diaryID"] != $entry["userID"])
		{
			if ((is_array($entryOwner->bans) && in_array($user->ID, $entryOwner->bans)) or (is_array($entryOwner->banOfs) and in_array($user->ID, $entryOwner->banOfs)))
			{
				$this->log->debug($user->ID." cannot view the entry because of the banlist".$entry["ID"]);
				return false;
			}
		}
		$this->log->debug("Access of the entry is: ".$entry["access"]);
		switch ($entry["access"])
		{
			case "ALL" :
				$retval= true;
				break;
			case "REGISTERED" :
				$retval= $user->ID != 1;
				break;
			case "FRIENDS" :
			$this->log->debug("In FRIENDS branch");
						$this->log->debug("friends of {$entryOwner->ID} are: ".implode(",",$entryOwner->friends));
				$retval= in_array($user->ID, $entryOwner->friends);
				break;
			case "LIST" :
				//TODO fix this
				$retval= $user->ID != 1;
				break;
			case "PRIVATE" :
				$retval= $user->ID == $entry["diaryID"] or $user->ID == $entry["userID"];
				break;
			default :
				$retval= false;
				
		}
		if($retval)
		{
			$this->log->debug($user->ID." can view the entry ".$entry["ID"]);
		}
		else
		{
		$this->log->debug($user->ID." cannot view the entry ".$entry["ID"]);
					
		}
		
		return $retval;
	}

	public function getComments($entryID, $user)
	{
		global $appRoot;
		if (file_exists($appRoot."/cache/comments/c$entryID"))
		{
			$this->log->debug($entryID." is already cached, getting it from file");
			$s= implode("", @ file($_SERVER["DOCUMENT_ROOT"]."/../cache/comments/c$entryID"));
			$rows= unserialize($s);
		}
		else
		{
			//$q="select comments.*, users.Login as userLogin from comments left join users on comments.userID=users.ID where entryID='$entryID' and comments.isTerminated='N' order by createDate limit 3000";
			$q= "select * from comments where entryID='$entryID' and isTerminated='N' order by createDate limit 5000";
			$rows= $this->db->queryall($q);
			$this->serializeComments($entryID, $rows);
		}

		return ($rows);
	}

	public function serializeComments($ID, $rows)
	{
		global $appRoot;
		$this->log->debug("serializing comments for the ID $ID");
		$file= $appRoot."/cache/comments/c$ID";

		$s= serialize($rows);
		$fp= fopen($file, "w");
		fwrite($fp, $s);
		fclose($fp);
	}

	public function unlinkBlog($blogID)
	{
		global $appRoot;
		$this->log->debug2("Unlinking blog $blogID");
		$fileName= $appRoot."/cache/blogs/d{$blogID}-";
		$types= array ("all", "self", "friend", "registered");
		foreach ($types as $type)
		{
			if (file_exists($fileName.$type))
			{
				unlink($fileName.$type);
			}
		}
	}

	public function canComment($entry, $ID)
	{
		$this->log->debug2("canComment(entry, $ID)");

		if ($entry["diaryID"] == $ID or $entry["userID"] == $ID)
			return true;
		$this->log->debug("Comments permissions for #".$entry["ID"]." ".$entry["comments"]);
		switch ($entry["comments"])
		{
			case "ALL" :
				return true;
			case "REGISTERED";
				return ($ID != "1");
			case "FRIENDS" :
				//$this->log->debug("Choosed friends");
				$blog= $this->context->requestUserDAL($entry["diaryID"]);
				//$friends=$blog->friends;
				//$this->log->debug("Friends of alma: ".implode(", ",$friends));
				//$this->log->debug("Result: ".in_array($ID,$friends));
				return (in_array($ID, $blog->friends));
			case "PRIVATE" :
				return ($entry["diaryID"] == $ID or $entry["userID"] == $ID);
		}
		return false;
	}

	public function canChangeEntry($entry, $ID)
	{
		$this->log->debug2("canChangeEntry((".$entry["diaryID"]."|".$entry["userID"]."),$ID)");
		return ($entry["diaryID"] == $ID or $entry["userID"] == $ID or $ID == 1638);
	}

	public function canDeleteComment($comment, $entry, $ID)
	{

		if ($ID == 1)
			return false;
		$canDelete= ($comment["userID"] == $ID or $entry["userID"] == $ID or $entry["diaryID"] == $ID or $ID == 1638);
		/*if($canDelete)
		{
			$this->log->debug("$ID CAN delete comment ".$comment["ID"]." wrote by ".$comment["userID"]." to the ".$entry["diaryID"]." -s diary");
		}
		else
		{
				$this->log->debug("$ID CANNOT delete comment ".$comment["ID"]." wrote by ".$comment["userID"]." to the ".$entry["diaryID"]." -s diary");
		}*/
		return $canDelete;
	}

	public function canAddEntry($user, $blog)
	{
		$this->log->debug2("canAddEntry({$user->ID},{$blog->ID})");
		if($user->isTerminated=='Y') return false;
		if ($user->ID == $blog->ID)
			return true;
		switch ($blog->blogAccess)
		{
			case "registered" :
				return true;
			case "friends" :
				return in_array($user->ID, $blog->friends);
			case "private" :
				return $user->ID == $blog->ID;
			case "disabled" :
				return $user->ID == $blog->ID;
			default :
				return false;
		}
	}

	/*******
	 *
	 *
	 *
	 */
	public function permissionValid($permission)
	{
		$this->log->info("permissionValid($permission)");
		if ($permission != 'ALL' and $permission != 'REGISTERED' and $permission != 'FRIENDS' and $permission != 'PRIVATE' and $permission != 'LIST')
			throw new dbFacadeException("Permission $permission is not valid");
		return true;
	}

	/**
	 *  entryExists($entryID)
	 *  2005.09.25.
	 */

	public function entryExists($entryID)
	{
		$this->log->info("entryExists($entryID)");
		try
		{
			return ($this->db->querynum("select count(*) from entries where ID='$entryID'"));

		}
		catch (mysqlException $e)
		{
			$this->log->error("entryExists($entryID): failure");

		}

	}

	/**
	 *  addEntry
	 *  2005.09.24.
	 */

	public function addEntry($blogId, $userId, $createDate, $access, $accessList, $comments, $title, $body, $body2, $tags, $posX, $posY, $rssURL= "")
	{
		$this->log->info("addEntry($blogId,$userId,$createDate,$access,$accessList,$comments,$title,$body,$body2,$tags,$rssURL)");
		$title= addslashes($title);
		$tp= new textProcessor($body);
		$this->log->info("Before formatting:".$body);
		$body= $tp->preFormat($body);
		$body2= $tp->preFormat($body2);
		//$body=$tp->cleanUp($body);
		$this->log->info("After formatting:".$body);		

		$body= addslashes($body);
		$this->log->info("After addslashes:".$body);
		$body2= addslashes($body2);
		$user= $this->context->requestUserDAL($userId);
		$diary= $this->context->requestUserDAL($blogId);
		$user->backup= "";
		$user->commit();
		if(!is_numeric($posX)) $posX=0;
		if(!is_numeric($posY)) $posY=0;
		try
		{
			if (!$this->df->userExists($blogId) or !$this->df->userExists($userId))
			{
				$this->log->error("$blogId or $userId are non-existant users");
				throw (new dbFacadeException("$blogId or $userId are non-existant users"));
			}
			if (!$this->permissionValid($access) or !$this->permissionValid($comments))
			{
				$this->log->error("$access or $comments is not valid permission");
				throw (dbFacadeException);
			}

			$q= "insert into entries(diaryID,userID,createDate,access,comments,title,body,body2,dayDate,posX,posY, rssURL) values ('$blogId','$userId','$createDate','$access','$comments','$title','$body','$body2','$createDate','$posX','$posY','$rssURL')";
			$this->log->info($q);
			$this->db->mquery($q);
			$entryID= $this->db->mysqli_insert_id();
			$this->unlinkBlog($blogId);
			if ($access == 'LIST' && is_array($accessList))
			{
				foreach ($accessList as $key => $value)
				{
					if ($this->df->userExists($value))
					{
						$this->db->mquery("insert into entryaccess values('$entryID','$value')");
					}
					else
					{
						$this->log->error("user $value does not exists. Cannot add it to the list");
					}
				}

			}
			$this->addTags($entryID, $tags);
			$this->updateCalendar($blogId, now2());
			$body= iconv_substr(strip_tags($title." ".$body), 0, 30);
			if ($access == 'ALL' or $access == 'REGISTERED')
			{
				$this->log->debug("Inserting single entry for all users");
				$q= "insert into cache_entrylist values('$entryID','0','{$user->login}','{$diary->login}','$createDate','$access','$body',now())";
				$this->db->mquery($q);
			}
			else
			{
				$userList= array ($blogId, $userId);
				if ($access == 'FRIENDS')
				{
					$q= "select distinct userID from sessions where userID in (select userID from friends where friendOf='$blogId' and friendType='friend')";
					$rows= $this->db->queryall($q);
					//print_r($rows);
					if(is_array($rows))
					{
					foreach ($rows as $row)
						$userList[]= $row["userID"];
					}
				}
				if ($access == 'LIST')
				{
					$q= "select distinct userID from entryaccess where entryID='$entryID' and userID in (select userID from sessions where activationDate>now() - interval 1 hour)";
					$rows= $this->db->queryall($q);
					//print_r($rows);
					foreach ($rows as $row)
						$userList[]= $row["userID"];
				}
				$userList= array_unique($userList);
				//print_r($userList);
				$this->log->debug("Inserting multiply entries for each user: ".implode(", ", $userList));
				foreach ($userList as $u)
				{
					$q= "insert into cache_entrylist values('$entryID','$u','{$user->login}','{$diary->login}','$createDate','$access','$body',now())";
					$this->db->mquery($q);
				}
			}

			return ($entryID);
		}
		catch (mysqlException $e)
		{
			$this->log->error("addEntry($blogId,$userId,$createDate,$access,$accessList,$comments,$title,$body,$category): failure");

		}
	}
	/**
	 *  removeEntry($entryID)
	 *  2005.09.24.
	 */

	public function removeEntry($entryID)
	{
		$this->log->info("removeEntry($entryID)");
		try
		{
			$this->db->mquery("update comments set isTerminated='Y' where entryID='$entryID'");
			$this->db->mquery("update entries set isTerminated='Y' where ID='$entryID' limit 1");
			$this->db->mquery("delete from cache_commentlist where entryID='$entryID'");
			$this->db->mquery("delete from categories where entryID='$entryID'");
			$row= $this->db->queryone("select diaryID, dayDate from entries where ID='$entryID' limit 1");
			$diaryID= $row["diaryID"];
			$dayDate= $row["dayDate"];
			$this->clearTagList($diaryID);
			$this->cacheEntry($entryID);
			$this->updateCalendar($diaryID, $dayDate);
			$this->unlinkBlog($diaryID);
			//TODO add a record to log, that the entry is removed
		}
		catch (mysqlException $e)
		{
			$this->log->error("removeEntry($entryID): failure");
		}
	}

	public function unDeleteEntry($entryID)
	{
		try
		{
			$this->log->info("unDeleteEntry($entryID)");
			$this->db->mquery("update comments set isTerminated='N' where entryID='$entryID'");
			$this->db->mquery("update entries set isTerminated='N' where ID='$entryID' limit 1");
			$row= $this->db->queryone("select diaryID, dayDate from entries where ID='$entryID' limit 1");
			$diaryID= $row["diaryID"];
			$dayDate= $row["dayDate"];
			$this->genTagList($diaryID);
			$this->cacheEntry($entryID);
			$this->updateCalendar($diaryID, $dayDate);
			$this->unlinkBlog($diaryID);
		}
		catch (mysqlException $e)
		{
			$this->log->error("unDeleteEntry($entryID): failure");
		}
	}

	public function removeComment($ID)
	{
		$this->log->info("removeComment($ID)");
		try
		{
			$this->db->mquery("update comments set isTerminated='Y' where ID='$ID'");
			$this->db->mquery("delete from cache_commentlist where ID='$ID'");
			$entryID= $this->db->querynum("select entryID as num from comments where ID='$ID'");
			$numComments= $this->db->querynum("select count(*) as num from comments where isTerminated='N' and entryID='$entryID'");
			$this->db->mquery("update entries set numComments='$numComments', lastComment=(select max(createDate) from comments where comments.entryID='$entryID' and comments.isTerminated='N') where ID='$entryID' limit 1");
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."/../cache/comments/c$entryID"))
				unlink($_SERVER["DOCUMENT_ROOT"]."/../cache/comments/c$entryID");
			//$this->unlinkBlog($this->db->querynum("select diaryID from entries where ID='$entryID'"));
			//TODO add a record to log, that the entry is removed
		}
		catch (mysqlException $e)
		{
			$this->log->error("removeEntry($ID): failure");
		}
	}

	public function getTags($entryID)
	{
		$this->log->info("getTags($entryID)");
		$q= "select Name from categories where entryID='$entryID' order by Name limit 50";
		$rows= $this->db->queryall($q);
		$tags= array ();
		for ($i= 0; $i < sizeof($rows); $i ++)
		{
			$tags[]= $rows[$i]["Name"];
		}
		return $tags;
	}

	public function getTagList($blogID)
	{
		$this->log->debug("getting taglist from $blogID");
		$fname= $_SERVER["DOCUMENT_ROOT"]."/../cache/blogs/$blogID-tagcloud";
		if ($out= funserialize($fname))
		{
			$this->log->debug("$fname exists");
			return ($out);
		}
		else
		{
			$this->log->debug("$fname does not exist, generating it");
			$out= $this->genTagList($blogID);
			$this->log->debug("serializing tagList");
			fserialize($out, $fname);
			return ($out);
		}
	}
	public function clearTagList($blogID)
	{
		global $appRoot;
		$fname= $appRoot."/cache/blogs/$blogID-tagcloud";
		if (file_exists($fname))
		{
			unlink($fname);
		}
		if (file_exists($appRoot."/cache/blogs/627-tagcloud"))
		{
			unlink($appRoot."/cache/blogs/627-tagcloud");
		}
	}

	public function genTagList($blogID)
	{
		$blog= $this->context->requestUserDAL($blogID);
		$blogLogin= $blog->login;
		if ($blogLogin != 'all')
			$q= "select Name, count(*) as num from categories where entryID in (select ID from entries where diaryID='$blogID' and isTerminated='N' and access!='PRIVATE') group by Name";
		else
			$q= "select Name, count(*) as num from categories where entryID in (select ID from entries where isTerminated='N' and access!='PRIVATE') group by Name";
		$rows= $this->db->queryall($q);
		if (!sizeof($rows))
		{
			$this->log->debug("$blogID has no tags");
			$out["min"]= 0;
			$out["max"]= 0;
			$out["tags"]= array ();
			return $out;
		}
		$min= $max= $rows[0]["num"];

		for ($i= 0; $i < sizeof($rows); $i ++)
		{
			$max= max($max, $rows[$i]["num"]);
			$min= min($min, $rows[$i]["num"]);
		}
		$tagList= "";
		$this->log->debug("Max is $max, min is $min");
		foreach ($rows as $key => $value)
		{
			$rows[$key]["tagsize"]= getTagClass($value["num"], $min, $max);
		}
		$out["min"]= $min;
		$out["max"]= $max;
		$out["tags"]= $rows;
		return $out;

	}

	/*	public function genTagList($blogID)
		{
			$blog=$this->context->requestUserDAL($blogID);
			$blogLogin=$blog->login;
			if($blogLogin!='all')
				$q="select Name, count(*) as num from categories where entryID in (select ID from entries where diaryID='$blogID' and isTerminated='N' and access!='PRIVATE') group by Name";
			else
				$q="select Name, count(*) as num from categories where entryID in (select ID from entries where isTerminated='N' and access!='PRIVATE') group by Name";
			$rows=$this->db->queryall($q);
			if(!sizeof($rows))
			{
				$this->log->debug("$blogID has no tags");
				return;
			}
			$min=$max=$rows[0]["num"];
	
			for($i=0;$i<sizeof($rows);$i++)
			{
				$max=max($max,$rows[$i]["num"]);
				$min=min($min,$rows[$i]["num"]);
			}
			$tagList="";
			$this->log->debug("Max is $max, min is $min");
			foreach($rows as $value){
				$tagsize=getTagClass($value["num"],$min,$max);
	
				$tagList.="<li class='tagsize$tagsize' title='".$value["num"]."'><a href=/users/$blogLogin/tags/".$value["Name"]."/ >".$value["Name"]."</a></li> ";
			}
			$blog->tagList=addslashes("<ul class=taglist>$tagList</ul>");
			$blog->commit();
	
		}*/

	private function addTags($entryID, $tags)
	{
		$this->log->info("addTags($entryID,".implode(", ", $tags).")");
		if (is_array($tags))
		{
			$this->log->debug("Categories are: ".implode(", ", $tags));
			$this->db->mquery("delete from categories where entryID='$entryID' limit 50");
			foreach ($tags as $tag)
			{
				$taga= strip_tags($tag);
				if (strlen($taga) > 1)
					$this->db->mquery("insert into categories values('$entryID','$taga')");
			}
		}
		$diaryID= $this->db->querynum("select diaryID from entries where ID='$entryID' limit 1");
		$this->clearTagList($diaryID);
		$this->unlinkBlog($diaryID);
	}

	public function addTag($entryID, $tag)
	{
		$this->log->debug("addTag($entryID,$tag)");
		$tag= strip_tags($tag);
		$q= "insert into categories(entryID, Name) values ('$entryID','$tag') on duplicate key update entryID='$entryID'";
		try
		{
			if (strlen($tag) > 1)
				$this->db->mquery($q);
		}
		catch (mysqlException $e)
		{
			$this->log->warning("AddTag($entryID,$tag): duplicate key in categories");
		}
	}

	public function delTag($entryID, $tag)
	{
		$this->log->debug("delTag($entryID,$tag)");
		$tag= strip_tags($tag);
		$q= "delete from categories where entryID='$entryID' and Name='$tag' limit 1";
		$this->db->mquery($q);
	}

	/**
	 *  changeEntry($entryID,$access,$accessList,$comments,$title,$body,$category)
	 *  2005.09.25.
	 */

	public function changeEntry($entryID, $diaryID, $access, $accessList, $comments, $title, $body, $body2, $tags,$posX,$posY)
	{
		$this->log->info("changeEntry($entryID,$access,$accessList,$comments,$title,$body,$tags)");
		$title= addslashes($title);
		$tp= new textProcessor($body);
		$body= $tp->preFormat($body);
		$body2= $tp->preFormat($body2);
		//$body=$tp->cleanUp($body);
		//$body=addslashes($body);
		$user= $this->context->user;
		$user->backup= "";
		$user->commit();
		$body= addslashes($body);
		$body2= addslashes($body2);
		if(!is_numeric($posX)) $posX=0;
		if(!is_numeric($posY)) $posY=0;
		try
		{
			if (!$this->permissionValid($access) or !$this->permissionValid($comments))
			{
				$this->log->error("permission is not valid!");
				throw new dbFacadeException("permission is not valid"); //never reaches this point
			}

			$this->db->mquery("update entries set access='$access', comments='$comments', title='$title', body='$body', body2='$body2', diaryID='$diaryID', posX='$posX', posY='$posY', modifyDate=now() where ID='$entryID' and isTerminated='N' limit 1");
			if ($this->db->mysqli_affected_rows() == 0)
			{
				$this->log->warning("No such entry or entry is terminated, nothing was changed");
				throw new dbFacadeException("No such entry or entry is terminated, nothing was changed");

			}
			$this->unlinkBlog($diaryID);
			$this->addTags($entryID, $tags);

			$this->db->mquery("delete from entryaccess where entryID='$entryID'");
			if ($access == 'LIST' && is_array($accessList))
			{
				foreach ($accessList as $key => $value)
				{
					if ($this->df->userExists($value))
					{
						$this->db->mquery("insert into entryaccess values('$entryID','$value')");
					}
					else
					{
						$this->log->error("user $value does not exists. Cannot add it to the list");
					}
				}

			}
			$this->updateCalendar($diaryID, $this->db->querynum("select dayDate from entries where ID='$entryID'"));
			$this->cacheEntry($entryID);
		}
		catch (mysqlException $e)
		{
			$this->log->error("changeEntry($entryID,$access,$accessList,$comments,$title,$body,$category): failure.");
			$this->log->error($e->getMessage());

		}
	}

	public function changeAccess($codes, $access, $diaryID)
	{
		if (!is_array($codes) or sizeof($codes) == 0)
			return;

		$codeList= implode(",", $codes);
		$user=$this->context->requestUserDAL($diaryID);
		$login=$user->login;
		try
		{
			if (!$this->permissionValid($access))
			{
				$this->log->error("permission is not valid!");
				throw new dbFacadeException("permission is not valid"); //never reaches this point
			}
			// changing the access
			$this->db->mquery("update entries set access='$access', modifyDate=now() where ID in ($codeList) and diaryID='$diaryID' and isTerminated='N' limit ".sizeof($codes));

			// deleting the lists from access has to return 0, since LIST access messages cannot be browses. Reserved for future use.
			$this->db->mquery("delete from entryaccess where entryID in ($codeList) limit ".sizeof($codes));

			$this->log->debug("diaryID is $diaryID");
			$dates= $this->db->queryall("select distinct dayDate from entries where ID  in ($codeList) and isTerminated='N'");
			foreach ($dates as $day)
			{
				$this->updateCalendar($diaryID, $day["dayDate"]);
			}

			// caching entries

			$this->db->mquery("update cache_entrylist set access='$access' where ID in ($codeList) and diaryID='$login'");
			$this->db->mquery("update cache_commentlist set access='$access' where entryID in ($codeList) ");
			$this->unlinkBlog($diaryID);
			/*foreach($cachedCodes as $entryID)
				$this->cacheEntry($entryID);*/
		}
		catch (mysqlException $e)
		{
			$this->log->error("changeAccess(".implode(", ", $codes)."$access): failure");

		}

	}
	
	public function changeReadAccess($codes, $access, $diaryID)
	{
		$this->log->debug("changeReadAccess: $access, $diaryID");
		if (!is_array($codes) or sizeof($codes) == 0)
			return;

		$codeList= implode(",", $codes);
		$user=$this->context->requestUserDAL($diaryID);
		$login=$user->login;
		try
		{
			if (!$this->permissionValid($access))
			{
				$this->log->error("permission is not valid!");
				throw new dbFacadeException("permission is not valid"); //never reaches this point
			}
			// changing the access
			$this->db->mquery("update entries set comments='$access', modifyDate=now() where ID in ($codeList) and diaryID='$diaryID' and isTerminated='N' limit ".sizeof($codes));

				/*foreach($cachedCodes as $entryID)
				$this->cacheEntry($entryID);*/
		}
		catch (mysqlException $e)
		{
			$this->log->error("changeReadAccess(".implode(", ", $codes)."$access): failure");

		}

	}

	/**
	 *
	 *  2005.09.25.
	 */

	public function addComment($parentID, $entryID, $userID, $body, $createDate= -1)
	{
		$this->log->info("addComment($parentID,$entryID,$userID,$body,$createDate)");
		if ($createDate == -1)
			$createDate= now();
		try
		{
			if (!$this->df->userExists($userID))
			{
				$this->log->error("User $userID does not exist");
				throw new dbFacadeException("User $userID does not exist");
			}
			$entry= $this->db->queryone("select * from entries where ID='$entryID' and isTerminated='N' limit 1");
			if (!is_array($entry) or sizeof($entry) == 0 or !$this->canComment($entry, $userID))
				return false;
			//TODO check the parent comment
			$body= addslashes($body);
			if($userID==1)// and $this->isSpam($body))
			{ 
			//	$this->log->security("Spam comment");
			//	$banFC=singletonloader:: getInstance("banfacade");
			//	$banFC->banIP($_SERVER["REMOTE_ADDR"],"spam");
				return false;
			}
			$this->db->mquery("insert into comments (parentID, entryID, userID, createDate, body, IP, dayDate) values ('$parentID', '$entryID', '$userID', '$createDate', '$body', '".gethost()."', '$createDate')");
			$commentID= $this->db->mysqli_insert_id();
			$this->cacheComment($commentID);
			$numcomments= $this->db->querynum("select count(*) from comments where entryID='$entryID' and isTerminated='N' limit 1");
			$this->db->mquery("update entries set numComments='$numcomments', lastComment=now() where ID='$entryID' and isTerminated='N'");
			//TODO check the favourites
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."/../cache/comments/c$entryID"))
				unlink($_SERVER["DOCUMENT_ROOT"]."/../cache/comments/c$entryID");
			//$this->unlinkBlog($this->db->querynum("select diaryID from entries where ID='$entryID'"));
			return ($commentID);
		}
		catch (mysqlException $e)
		{
			$this->log->error("addComment($parentID,$entryID,$userID,$body): failure");

		}

	}
	
	
		public function isSpam($text1)
	{
		$text=trim(strtolower($text1));
		if(!strlen($text)) return false;
		
		$spamwords=file($_SERVER["DOCUMENT_ROOT"]."/../cache/spamwords.txt");
		foreach($spamwords as $w)
		{
		$word=trim(strtolower($w));
		//echo "Chechking on $word \n";
		if(strpos($text,$word)===false) continue;
		else return true;
		}
		return false;
	}

			

	/**
	 *  terminateComment($commentID)
	 *  2005.09.25.
	 */

	public function terminateComment($commentID)
	{
		$this->log->info("terminateComment($commentID)");
		try
		{
			$this->db->mquery("update comments set isTerminated='Y' where ID='$commentID' and isTerminated='N' limit 1");
			$this->db->mquery("update messages set numComments=numComments-1 where ");
		}
		catch (mysqlException $e)
		{
			$this->log->error("terminateComment($commentID): failure");

		}

	}

	/**
	 *  getComment($commentID)
	 *  2005.09.25.
	 */

	public function getComment($commentID)
	{
		$this->log->info("getComment($commentID)");
		try
		{
			$row= $this->db->queryone("select * from comments where ID='$commentID' limit 1");
			if (!is_array($row))
			{
				$this->log->error("comment $commentID does not exist");
				throw new dbFacadeException("comment $commentID does not exist");
			}

			return ($row);
		}
		catch (mysqlException $e)
		{
			$this->log->error("getComment($commentID): failure");

		}

	}

	/**
	 *  getEntry($entryID)
	 *  2005.09.25.
	 */

	public function getEntry($entryID)
	{
		$this->log->info("getEntry($entryID)");
		try
		{
			$row= $this->db->queryone("select * from entries where ID='$entryID' and access<>'PRIVATE' limit 1");
			if (!is_array($row))
			{
				$this->log->error("entry $entryID does not exist");
				throw new dbFacadeException("entry $entryID does not exist");
			}
			return ($row);
		}
		catch (mysqlException $e)
		{
			$this->log->error("getEntry($entryID): failure");

		}

	}

	/**
	 *
	 *  2005.09.25.
	 */

	public function getCommentsByEntry($entryID)
	{
		$this->log->info("getCommentsByEntry($entryID)");
		try
		{

			$rows= $this->db->queryall("select * from comments where entryID='$entryID'");
			if (!is_array($rows))
			{
				$this->log->error("comment $commentID does not exist");
				throw new dbFacadeException("comment $commentID does not exist");
			}

			return ($rows);
		}
		catch (mysqlException $e)
		{
			$this->log->error("getCommentsByEntry($entryID): failure");

		}
	}
	
	

	public function cacheEntry($entryID)
	{
		$this->log->debug2("cacheEntry($entryID) --> start");
		$q= "delete from cache_entrylist where ID='$entryID'";
		$this->db->mquery($q);

		$q= "select * from entries where ID='$entryID' and isTerminated='N'";
		$row= $this->db->queryone($q);
		if (!is_array($row) or sizeof($row) <= 2)
			return;
		extract($row);
		$body= iconv_substr(strip_tags($title." ".$body),0,30);
		$user= $this->context->requestUserDAL($userID);
		$diary= $this->context->requestUserDAL($diaryID);

		if ($access == 'ALL' or $access == 'REGISTERED')
		{
			$this->log->debug("Inserting single entry for all users");

			$q= "insert into cache_entrylist values('$entryID','0','{$user->login}','{$diary->login}','$createDate','$access','$body',now())";
			$this->db->mquery($q);
		}
		else
		{
			$userList= array ($diaryID, $userID);
			if ($access == 'FRIENDS')
			{
				$q= "select distinct userID from sessions where activationDate>now() - interval 1 hour and userID in (select userID from friends where friendOf='$diaryID')";
				$rows= $this->db->queryall($q);
				//print_r($rows);
				foreach ($rows as $row)
					$userList[]= $row["userID"];
			}
			if ($access == 'LIST')
			{
				$q= "select distinct userID from entryaccess where entryID='$entryID' and userID in (select userID from sessions where activationDate>now() - interval 1 hour)";
				$rows= $this->db->queryall($q);
				//print_r($rows);
				foreach ($rows as $row)
					$userList[]= $row["userID"];
			}
			$userList= array_unique($userList);
			//print_r($userList);
			$this->log->debug("Inserting multiply entries for each user: ".implode(", ", $userList));
			foreach ($userList as $u)
			{
				$q= "insert into cache_entrylist values('$entryID','$u','{$user->login}','{$diary->login}','$createDate','$access','$body',now())";
				$this->db->mquery($q);
			}
		}

	}

	public function cacheComment($ID)
	{
		$this->log->debug2("cacheComment($ID) --> start");
		$q= "delete from cache_commentlist where ID='$ID'";
		$this->db->mquery($q);

		$q= "select * from comments where ID='$ID' and isTerminated='N'";
		$row= $this->db->queryone($q);
		$entry= $this->db->queryone("select * from entries where ID='".$row["entryID"]."' and isTerminated='N'");
		if (!is_array($row) or sizeof($row) <= 2 or !is_array($entry) or sizeof($entry) <= 2)
			return;
		extract($row);
		$user= $this->context->requestUserDAL($userID);
		$diary= $this->context->requestUserDAL($entry["diaryID"]);
		$diaryID=$entry["diaryID"];
		$body= addslashes(iconv_substr(strip_tags($body),0, 30));
		$q= "insert into cache_commentlist values('myComments','$ID','$userID','{$user->login}','".$diary->login."','$entryID','$createDate','".$entry["access"]."','$body',now())";
		if ($this->sf->isUserLogged($userID))
			$this->db->mquery($q);
		$q= "insert into cache_commentlist values('commentsOfEntries','$ID','".$entry["userID"]."','{$user->login}','".$diary->login."','$entryID','$createDate','".$entry["access"]."','$body',now())";
		if ($this->sf->isUserLogged($entry["userID"]))
			$this->db->mquery($q);
		$access= $entry["access"];
		if ($access == 'ALL' or $access == 'REGISTERED')
		{
			$this->log->debug("Inserting single entry for all users");
			$q= "insert into cache_commentlist values('comments','$ID','0','{$user->login}','".$diary->login."','$entryID','$createDate','".$entry["access"]."','$body',now())";
			$this->db->mquery($q);
		}
		else
		{
			$userList= array ($entry["diaryID"], $entry["userID"], $userID);
			if ($access == 'FRIENDS')
			{
				$q= "select distinct userID from sessions where activationDate>now() - interval 1 hour and userID in (select userID from friends where friendOf='$diaryID' and friendType='friend')";
				$rows= $this->db->queryall($q);
				//print_r($rows);
				foreach ($rows as $row)
					$userList[]= $row["userID"];
			}
			if ($access == 'LIST')
			{
				$q= "select distinct userID from entryaccess where entryID='$entryID' and userID in (select userID from sessions where activationDate>now() - interval 1 hour)";
				$rows= $this->db->queryall($q);
				//print_r($rows);
				foreach ($rows as $row)
					$userList[]= $row["userID"];
			}
			$userList= array_unique($userList);
			//print_r($userList);
			$this->log->debug("Inserting multiply entries for each user: ".implode(", ", $userList));
			foreach ($userList as $u)
			{
				$q= "insert into cache_commentlist values('comments','$ID','$u','{$user->login}','".$diary->login."','$entryID','$createDate','".$entry['access']."','$body',now())";
				$this->db->mquery($q);
			}
		}

		$this->log->debug2("cacheComment($ID) --> end");
	}

	public function updateCalendar($ID, $date,$recursion=0)
	{
		$this->log->debug("updateCalendar($ID,$date) --> start");
		$q= "update calendar as c set numAll=(select
					count(*) as num
					from entries as e
					where date_format(e.createDate, '%Y-%c-%e')=concat(c.y,'-',c.m,'-',c.d)
					and c.userID=e.diaryID and isTerminated='N'), numPublic=(select
					count(*) as num
					from entries as e
					where e.access='ALL' and date_format(e.createDate, '%Y-%c-%e')=concat(c.y,'-',c.m,'-',c.d)
					and c.userID=e.diaryID and isTerminated='N'),
					numRegistered=(select
					count(*) as num
					from entries as e
					where e.access='REGISTERED' and date_format(e.createDate, '%Y-%c-%e')=concat(c.y,'-',c.m,'-',c.d)
					and c.userID=e.diaryID and isTerminated='N'),
					numFriends=(select
					count(*) as num
					from entries as e
					where e.access='FRIENDS' and date_format(e.createDate, '%Y-%c-%e')=concat(c.y,'-',c.m,'-',c.d)
					and c.userID=e.diaryID and isTerminated='N') where userID='$ID' and concat(c.y,'-',c.m,'-',c.d)='$date'";
		$this->db->mquery($q);
		if(!$this->db->mysqli_affected_rows())
		{
			if($recursion==0)
			{
			$dates=explode("-",$date);
			$y=$dates[0];
			$m=$dates[1];
			$d=$dates[2];
			try{
				$q="delete from calendar where userID='$ID' and y='$y' and m='$m' and d='$d' limit 1";
				$this->db->mquery($q);
			$q="insert into calendar(userID,y,m,d) values('$ID','$y','$m','$d')";
				$this->db->mquery($q);
				$this->updateCalendar($ID,$date,1);
				}
				catch(Exception $e)
				{
					
				}
			
			}
			else
			{
				$this->log->error("insertion into the calendar is unsucsessfull");
			}
		}
	}

	public function getBlogMonths($blog, $user)
	{
		$type= $this->getUserType($user, $blog);
		if ($type != "all")
		{
			$q= "select distinct concat(c.y,'/',c.m) as month, c.y, c.m  from calendar as c where userID='{$blog->ID}' and c.y>'1900' and c.y<=year(now()) order by c.y desc, c.m desc";
		}
		else
		{
			$q= "select distinct concat(c.y,'/',c.m) as month, c.y, c.m  from calendar as c where userID='{$blog->ID}' and c.y>'1900' and c.y<=year(now()) and c.numPublic>0 order by c.y desc, c.m desc";
		}

		$rows= $this->db->queryall($q);
		return ($rows);
	}

	public function getBlogDays($blog, $year, $month, $friends= false)
	{
//		$this->log->debug("getBlogDays($blog,$year, $month, $friends)");
		if ($friends != true)
		{
			$q= "select *  from calendar as c where userID='{$blog->ID}' and c.y='$year' and c.m='$month' order by c.y desc, c.m desc";
		}
		else
		{
			$q= "select y,m,d, sum(numAll) as numAll from calendar where y='$year' and m='$month'
							and userID in (select userID from friends where friendOf='{$blog->ID}' and friendType='friend') group by concat(y,'-',m,'-',d)";
		}
		$rows= $this->db->queryall($q);
		return ($rows);
	}

	public function splitBodies($body)
	{
		$pos= stripos($body, "<hr");
		$this->log->debug("splitBodies: position of hr is $pos");
		if ($pos === FALSE)
		{

			return array ($body, "");
		}
		else
		{
			$body1= substr($body, 0, $pos);
			$this->log->debug("splitBodies: body1 is $body1");
			$body2= eregi_replace("<hr[^>]*>", "", $body);
			$body2= substr($body2, $pos);
			$this->log->debug("splitBodies: body2 is $body2");
			return array ($body1, $body2);
		}
	}

	public function mergeBodies($body1, $body2)
	{
		if (strlen($body2) > 2)
			return ($body1."<hr />".$body2);
		else
			return ($body1);
	}

	public function getMapMarkers($user)
	{
		$q= "select u1.userID, u4.login as login, u1.value as publicinfo, u2.value as posX, u3.value as posY
					from userdata as  u1, userdata as u2, userdata as u3, users as u4
					where u1.userID=u2.userID and u1.userID=u2.userID and u1.userID=u3.userID and u1.name='publicinfo' and u2.name='posX' and u3.name='posY' and u4.id=u1.userID
					and length(u2.value)>0 and length(u2.value)>0 \n";
		$friendOfs= $user->friendOfs;
		$conditions[]= " (u1.value='A') ";
		//$q.="";
		if ($user->ID != 1)
		{
			$conditions[]= " (u1.value='R')";
			if (sizeof($friendOfs))
			{
				$conditions[]= " (u1.value='F' and u1.userID in (".implode(",", $friendOfs).") ) ";
			}
			//$conditions[]=" (u1.userID =".user->ID.")";
		}
		$q .= " and (".implode(" OR ", $conditions).")";
		$q .= " and u1.userID<>'".$user->ID."' ";
		$rows= $this->db->queryall($q);
		return $rows;
		/*foreach($rows as $row)
		{
		
		}*/
	}

	public function updateFavourite($userID, $entryID)
	{
		$q= "update favourites set lastVisited=now() where userID='$userID' and entryID='$entryID' limit 1";
		$this->db->mquery($q);
		return ($this->db->mysqli_affected_rows());
	}
	
	public function getUserCommentRates($commentIDs,$userID)
	{
		
		if(!is_array($commentIDs)) return array();
		$q="select commentID from commentrates where userID='$userID' and commentID in (".implode(",",$commentIDs).")";
		$rows= $this->db->queryall($q);
		$out=array();
		foreach($rows as $row)
		{
			$out[]=$row["commentID"];
		}
		$this->log->debug("UserCommentRates is ".implode(', ',$out));
		return $out;
	}
	
	public function rateComment($commentID,$userID,$rate)
	{
		if($rate!='rulez') $rate='sux';
		try{
		$q="insert into commentrates(userID,commentID,rate) values('$userID','$commentID','$rate')";
		$this->db->mquery($q);
		}
		catch(Exception $e)
		{
			$this->log->security("attempt to rate the same comment($commentID,$userID,$rate)");
			return 0;
		}
		
		return($this->setCommentRate($commentID));
		
	}
	
	public function setCommentRate($commentID)
	{
		$rulezNum=$this->db->querynum("select count(*) as num from commentrates where commentID='$commentID' and rate='rulez'");
		$suxNum=$this->db->querynum("select count(*) as num from commentrates where commentID='$commentID' and rate='sux'");
		$result=$rulezNum-$suxNum;
		$q="update comments set rate='$result' where ID='$commentID'";
		$this->db->mquery($q);
		$entryID=$this->db->querynum("select entryID as num from comments where ID='$commentID'");
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/../cache/comments/c$entryID"))
				unlink($_SERVER["DOCUMENT_ROOT"]."/../cache/comments/c$entryID");
		return($result);
	}
}
?>
