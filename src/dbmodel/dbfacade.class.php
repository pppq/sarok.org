<?php


/**
 *  dbFacade
 */

class dbFacade {
	/**
	 *  Initializes db connection
	 */
	private $dbcon;
	private $log;
	private $db;
	public function dbFacade() {

		$this->log = singletonloader :: getInstance("log");
		$this->db = singletonloader :: getInstance("mysql");
		$this->log->info("dbFacade initialized");

	}

	//----------------------------Methods for work with users

	/**
	 * addUser
	 *
	 */
	public function addUser($login, $password, $email) {
		$this->log->info("addUser: adding user $ID with password '$password', email $email");
		try {
			$this->db->mquery("insert into users (login, pass, createDate) values('$login',old_password('$password'),now())");
			$ID=mysql_insert_id();
			//TODO add to all's friends, add different data's''
			$this->db->mquery("insert into userdata values ('$ID','email','$email')");
		} catch (mysqlException $e) {
			$this->log->error("addUser: user $login already exists");
		}
	}

	/**
	 *
	 *  2005.09.25.
	 */

	public function terminateUser($userID) {
		$this->log->info("terminateUser($userID)");
		try {
			$this->mquery("update users set isTerminated='Y' where ID='$userID' limit 1");
			//TODO implement deleting comments, privates and messages of user.
		} catch (mysqlException $e) {
			$this->log->error("terminateUser($userID): failure");

		}

	}

	public function userExists($ID) {
		$this->log->info("isUserExist: checking $ID");
		try {
			$num = $this->db->querynum("select count(*) as num from users where ID='$ID'");
			if ($num) {
				$this->log->debug("$ID exists");
				return true;
			} else {
				$this->log->debug("$ID does not exists");
				return false;
			}

		} catch (mysqlException $e) {
			$this->log->error("isUserExists: failure");
		}
	}

	/**
	 * getUserProperty
	 */

	public function getUserProperty($ID, $property) {
		$this->log->info("getUserProperty: ID: $ID, property: $property");
		try {
			$data = $this->db->querynum("select value from userdata where userID='$ID' and name='$property'");
			return ($data);
		} catch (mysqlException $e) {
			$this->log->error("getUserProperty: failure");

		}
	}

	/**
	 *  getUserData($userID)
	 *  2005.09.25.
	 */

	public function getUserData($userID) {
		$this->log->info("getUserData($userID)");
		try {
			$q = "select * from users where ID='$userID' limit 1";
			$row = $this->db->queryone($q);
			if (!is_array($row)) {
				$this->log->warning("user $userID does not exist");
				throw new dbFacadeException("user $userID does not exist");
			}

			return ($row);
		} catch (mysqlException $e) {
			$this->log->error(": failure");

		}

	}

	/**
	 *
	 */

	public function getUserProperties($ID) {
		$this->log->info("getUserProperties: ID: $ID");
		$data=array();
		try {
			$rows = $this->db->queryall("select name, value from userdata where userID='$ID'");

			for ($i = 0; $i < sizeof($rows); $i ++) {
				$data[$rows[$i]["name"]] = $rows[$i]["value"];
			}

			return ($data);
		} catch (mysqlException $e) {
			$this->log->error("getUserProperties: failure");
		}

	}

	/**
	 *  setUserProperty($ID,$name,$property)
	 *  2005.09.24.
	 */

	public function setUserProperty($ID, $name, $property) {
		$this->log->info("setUserProperty($ID,$name,$property)");
		try {
			//$q = "replace userdata set value='$property' , userID='$ID' , name='$name'";
			$q = "insert into userdata values('$ID','$name','$property') on duplicate key update value='$property'";
			$this->db->mquery($q);
			$num = mysql_affected_rows();
		} catch (mysqlException $e) {
			if ($e->getMessage() != 1062)
				$this->log->error("setUserProperty: failure: ".$e->getMessage());
		}
	}

	/**
	 *  setUserProperies($ID,$properties)
	 *  2005.09.24.
	 */

	public function setUserProperties($ID, $properties) {
		$this->log->info("setUserProperties($ID,$properties)");
		try {
			if (!is_array($properties)) {
				$this->log->error("properties is not an array");
				throw new dbFacadeException("properties is not an array");
			}
			foreach ($properties as $key => $value) {
				$this->setUserProperty($ID, $key, $value);
			}

		} catch (mysqlException $e) {
			if ($e->getCode() != 1062)
				$this->log->error("setUserProperties($ID,$properties): failure: ".$e->getMessage());

		}

	}

	/**
	 *  getFriendType($userID,$friendOf)
	 *  2005.09.24.
	 */

	public function getFriendType($userID, $friendOf) {
		$this->log->info("getFriendType($userID,$friendOf)");
		try {
			$retval = "select friendType from friends where userID='$userID' and friendOf='$friendOf'";
			return ($retval);

		} catch (mysqlException $e) {
			$this->log->error("getFriendType: failure");
		}

	}

	/**
	 *  getFriends($userID)
	 *  2005.09.24.
	 */

	public function getFriends($ID, $type = "friend") {
		if ($type != "friend"  and  $type != "read")
			$type = "banned";
		$retval = array ();
		$this->log->info("getFriends($ID,$type)");
		try {
			$rows = $this->db->queryall("select userID from friends left join users on users.ID=friends.userID where friendOf='$ID' and friendType='$type' order by login");
			for ($i = 0; $i < sizeof($rows); $i ++) {
				$retval[] = $rows[$i]["userID"];
			}
			return ($retval);

		} catch (mysqlException $e) {
			$this->log->error("getFriends: failure");
		}
	}

	/**
	 *  getFriendOfs($userID)
	 *  2005.09.24.
	 */

	public function getFriendOfs($ID, $type = "friend") {
		if ($type != "friend" and  $type != "read")
			$type = "banned";
		$retval = array ();
		$this->log->info("getFriends($ID,$type)");
		try {
			$rows = $this->db->queryall("select friendOf from friends where userID='$ID' and friendType='$type'");
			for ($i = 0; $i < sizeof($rows); $i ++) {
				$retval[] = $rows[$i]["friendOf"];
			}
			return ($retval);

		} catch (mysqlException $e) {
			$this->log->error("getFriendOfs: failure");
		}
	}

	public function setFriends($ID, $friendsar, $type = "friend") {
		$this->log->info("setFriends($ID,$type)");
		if ($type != "friend"  and  $type != "read")
			$type = "banned";
		$friends = array_unique($friendsar);
		$this->db->mquery("delete from friends where friendOf='$ID' and friendType='$type'");
		foreach ($friends as $key => $friend) {
			$this->setFriend($ID, $friend, $type);
		}
	}

	public function setFriendOfs($ID, $friendsar, $type = "friend") {
		$this->log->info("setFriends($ID,$type)");
		if ($type != "friend" and  $type != "read")
			$type = "banned";
		$friends = array_unique($friendsar);
		$this->db->mquery("delete from friends where userID='$ID' and friendType='$type'");

		foreach ($friends as $key => $friend) {
			$this->setFriend($friend, $ID, $type);
		}
	}

	/**
	 *  addFriend($userID,$friendOf, $friendType)
	 *  2005.09.24.
	 */

	public function setFriend($userID, $friendOf, $friendType) {
		$this->log->info("setFriend($userID,$friendOf, $friendType)");
		if ($friendType != "friend" && $friendType != "banned"  &&  $friendType != "read") {
			$this->log->error("Unknown friend type $friendType");
			throw dbFacadeException("Unknown friend type $friendType");
		}

		try {
			//$this->db->mquery("delete from friends where userID='$userID' and friendOf='$friendOf' and friendType='$friendType')");
			$this->log->debug("friendType is $friendType");
			if($this->userExists($userID) && $this->userExists($friendOf))
				{
				$this->db->mquery("insert into friends values('$userID','$friendOf','$friendType')");
				}

		} catch (mysqlException $e) {
			$this->log->error("setFriend($userID,$friendOf, $friendType): failure");

		}

	}

	public function getFavourites($userID,$onlyNew=false)
	{
		$out=array();
		if($onlyNew)
		{
			$q="select entryID from favourites as f where userID='$userID' and exists (select ID from entries where ID=f.entryID and isTerminated='N' and lastComment>=f.lastVisited)";
		}
		else
		{
			$q="select entryID from favourites as f where userID='$userID' and exists (select ID from entries where ID=f.entryID and isTerminated='N'  and lastComment<f.lastVisited)";
		}
		$favourites=$this->db->queryall($q);
		if(is_array($favourites))
		{
			$favouritesList=array();
			foreach($favourites as $f)
			{
				$favouritesList[]=$f["entryID"];
			}

			$q="select ID, diaryID, userID, title, lastComment from entries where ID in (".implode(",",$favouritesList).") order by lastComment desc";
			$out=$this->db->queryall($q);
		}
	return $out;
	}

public function getUserList( $offset, $limit, $orderBy='activationDate desc')
{
	$this->log->debug("Getting list of users, offset $offsetm ");
	$q="select login, createDate, activationDate from users where ID!=1 order by $orderBy limit $offset, $limit";
	$rows=$this->db->queryall($q);
	return $rows;
}

public function getUserStats()
{
	$q="select count(*) as num from users";
	$out["numUsers"]=$this->db->querynum($q);
	
	$q="select count(*) as num from users where activationDate>now()-interval 1 month";
	$out["numUsersLastMonth"]=$this->db->querynum($q);
	
	$q="select count(*) as num from entries";
	$out["numEntries"]=$this->db->querynum($q);
	
	$q="select count(distinct userID) from comments";
	$out["numActiveUsers"]=$this->db->querynum($q);
	
	
	$q="select count(*) as num from comments";
	$out["numComments"]=$this->db->querynum($q);
	
	return($out);
}


	/**
	 * END
	 */
}
?>
