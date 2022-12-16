<?php
class userDAL extends dal {
	protected $values = array ("ID", "login", "pass", "createDate", "loginDate", "activationDate", "isTerminated");
	//protected $userDataLoaded=false;
	protected $propertiesLoaded=false;
	public function __construct($ID = null) {
		parent :: dal();
		$this->log->debug2("Started userDAL");
		if ($ID != null) {
			if (array_key_exists($ID, $this->context->users)) {
				//return($this->context->users[$login]);
				throw new Exception("DAL already exists in context");
			} else {
				$this->context->users[$ID] = $this;
			}
			$this->log->debug2("ID is $ID");
			$this->ID = $ID;

		}
	}

static public function findID($login)
{
	if(is_numeric($login)) return($login);
	$db = singletonloader :: getInstance("mysql");
	return($db->querynum("select ID as num from users where login='$login' limit 1"));
}

	public function __get($member) {
		//$this->log->debug2("Calling getter for $member");

		if (!array_key_exists($member, $this->data) or !$this->data[$member]["isLoaded"]) {
			$this->log->debug("$member is not loaded, loading it");
			$this->update($member);
		}
		if(!array_key_exists($member, $this->data))
		{
			$this->data[$member]["value"] = false;
			$this->data[$member]["isDirty"] = false;
			$this->data[$member]["isLoaded"] = true;
		}
		return ($this->data[$member]["value"]);
	}

	public function __set($member, $value) {
		$this->log->debug2("Calling setter for $member");
		if (!array_key_exists($member, $this->data) or $this->data[$member]["value"] != $value) {
			$this->data[$member]["value"] = $value;
			$this->data[$member]["isDirty"] = true;
			$this->data[$member]["isLoaded"] = true;
			if (($member == "friends" or $member == "friendOfs" or $member == "bans" or $member == "banOfs" or $member == "favourites" or $member == "newFavourites" or $member == "reads" or $member == "readOfs")) {
				$this->data[$member]["value"] = array_unique($this->data[$member]["value"]);
			}
		}
	}

	function update($member) {
		//$row
		$ID = $this->data["ID"]["value"];
		$this->log->debug("ID is $ID");
		if ($ID == null) {
			throw new DALException("null primary key!");
		}
		if ($this->isKey($member)) {
			try {
				$row = $this->dbFacade->getUserData($ID);
			} catch (Exception $e) {
				throw new DALException("user $ID does not exist!");
			}

			$this->loadRow($row);
		} else {
			$this->log->debug("Variable to get is not amoung the standard fields, trying it through friends");
			switch ($member) {
				case 'friends' :
					$value = $this->dbFacade->getFriends($ID);
					break;
				case 'friendOfs' :
					$value = $this->dbFacade->getFriendOfs($ID);
					break;
				case 'bans' :
					$value = $this->dbFacade->getFriends($ID, "banned");
					break;
				case 'banOfs' :
					$value = $this->dbFacade->getFriendOfs($ID, "banned");
					break;
				case 'reads' :
					$value = $this->dbFacade->getFriends($ID, "read");
					break;
				case 'readOfs' :
					$value = $this->dbFacade->getFriendOfs($ID, "read");
					break;
				case 'favourites' :
					$value = $this->dbFacade->getFavourites($ID);
					break;
				case 'newFavourites' :
					$value = $this->dbFacade->getFavourites($ID,true);
					break;

			}
			if (isset ($value)) {
				$this->data[$member]["value"] = $value;
				$this->data[$member]["isLoaded"] = true;
				$this->data[$member]["isDirty"] = false;
			} else {
				if($this->propertiesLoaded==false)
				{
				$this->log->debug("Variable to get is not amoung friends, it is from userdata");
				$properties = $this->dbFacade->getUserProperties($ID);
				foreach ($properties as $key => $value) {
					$this->data[$key]["value"] = $value;
					$this->data[$key]["isLoaded"] = true;
					$this->data[$key]["isDirty"] = false;
				}
				$this->propertiesLoaded=true;
				}
			
			}

		}
	}

	function commit() {
		$this->log->debug2("committing userDAL");
		$updates = array ();
		$ID = $this->data["ID"]["value"];
		$q = "update users set ";
		if ($this->data["pass"]["isDirty"])
			$updates[] = "pass=old_password('".$this->data["pass"]["value"]."') ";
		if ($this->data["createDate"]["isDirty"])
			$updates[] = "createDate=now()";
		if ($this->data["loginDate"]["isDirty"])
			$updates[] = "loginDate=now()";
		if ($this->data["activationDate"]["isDirty"])
			$updates[] = "activationDate=now()";
		if (sizeof($updates)) {
			$ups = implode($updates, ", ");
			$q .= $ups. " where ID='$ID' limit 1";
			$this->db->mquery($q);
		}
		/*
				if($this->db->mysqli_affected_rows()==0)
				{

					$this->dbFacade->addUser($this->data["login"]["value"],$this->data["pass"]["value"],$this->data["pass"]["email"]);
					//todo email
				}*/


		if($ID!="1")   //Anonim does not have any properties
		{
		// setting userdata's
		$properties = array ();
		foreach ($this->data as $key => $value) {
			if ($this->isKey($key) or !$value["isDirty"])
				continue;
			$this->log->debug("committing $key");

			switch ($key) {
				case 'friends' :
					$value = $this->dbFacade->setFriends($ID, $value["value"]);
					break;
				case 'friendOfs' :
					$value = $this->dbFacade->setFriendOfs($ID, $value["value"]);
					break;
				case 'bans' :
					$value = $this->dbFacade->setFriends($ID, $value["value"], "banned");
					break;
				case 'banOfs' :
					$value = $this->dbFacade->setFriendOfs($ID, $value["value"], "banned");
					break;
				case 'reads' :
					$value = $this->dbFacade->setFriends($ID, $value["value"], "read");
					break;
				case 'readOfs' :
					$value = $this->dbFacade->setFriendOfs($ID, $value["value"], "read");
					break;
				default :
					$this->log->debug("$key went to userdata");
					$properties[$key] = $value["value"];
					$this->log->debug("$key is changed");
			}

		}
		if (sizeof($properties)) {
			$this->log->debug("userdata is changed for $ID, updating");
			$this->dbFacade->setUserProperties($ID, $properties);
		}
		}

		$this->cleanDirty();
	}
	
	function secret()
	{
		$sf =  singletonloader :: getInstance("sessionfacade");
		$secret=$sf->getSecret($this->data["login"]["value"]);
		return($secret);
	}
}
?>
