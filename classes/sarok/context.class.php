<?php

use sarok\models\TrackedMap;
use sarok\models\TrackedSet;

class contextClass 
{
    /**
     * Users referenced during the request, keyed by user ID (integer)
     * @var array<int, userDAL>
     */
	public $users = array();

    /**
     * User properties referenced during the request, keyed by 
     * user ID (integer)
     * @var array<int, TrackedMap>
     */
	public $userProperties = array();

    /**
     * User connections (friends, bans, reads) referenced during the request, 
     * keyed by user ID and connection type, eg. `4383-friends` (string)
     * @var array<string, TrackedSet>
     */
	public $userLinks = array();

	public $session; // sessionClass that stores current session values
	public $entries = array (); // hash of loaded entries
	public $comments = array (); //hash of loaded comments
	public $mails = array (); // hash of loaded mails
	public $user; // current user
	public $blog; // current blog
	public $props = array (); // hash properties boolean values
	private $log; // logger class
	public $params; // paramaeters from the URL
	public $ActionPage; //action to execute

    public function __construct() 
    {
		$this->log = singletonloader :: getInstance("log");
	}

    private function getUserID(int|string $IDOrLogin) : int
    {
        if (is_numeric($IDOrLogin)) {
            return (int) $IDOrLogin;
        } 

        $db = singletonloader::getInstance('mysql');
        $userID = (int) $db->querynum("SELECT `ID` FROM `users` WHERE `login` = '${IDOrLogin}' LIMIT 1");
        return $userID;
    }

	public function getUser(int|string $IDOrLogin) : object
    {
		$this->log->debug2("getUser(${IDOrLogin})");
		
        $id = $this->getUserID($IDOrLogin);
		if (!array_key_exists($id, $this->users)) {
			$user = new userDAL($id);
            $this->users[$id] = $user;
			$this->log->debug("${id} added to container");
		}

		return $this->users[$id];
	}

    public function getUserData(int $id, array $keys = array()) : TrackedMap
    {
        $this->log->debug("getUserData({$id}, {$keys})");

        if (!isset($this->userProperties[$id])) {
            
            if (!empty($keys)) {
                $df = singletonloader::getInstance('dbfacade');
                $props = $df->getUserProperties($id, $keys);
            } else {
                $props = array();
            }

            $trackedProps = new TrackedMap($props);
            $this->userProperties[$id] = $trackedProps;
        }

        return $this->userProperties[$id];
    }

    public function saveUserData(int $id) : void
    {
        $this->log->debug("saveUserData({$id})");

        if (!isset($this->userProperties[$id])) {
            return;
        }

        $df = singletonloader::getInstance('dbfacade');
        $changedProps = $this->userProperties[$id]->flush();
        $df->setUserProperties($id, $changedProps);
    }

    public function getUserLinks(int $id, string $type = 'friends') : TrackedSet
    {
        $this->log->debug("getUserLinks({$id}, {$type})");

        $key = "{$id}-{$type}";
        if (!isset($this->userLinks[$key])) {
            $df = singletonloader::getInstance('dbfacade');

            switch ($type) {
                // TODO: eliminate plural-to-singular link type name conversion
                case 'friends':
                    $value = $df->getFriends($id);
                    break;
                case 'friendOfs':
                    $value = $df->getFriendOfs($id);
                    break;
                case 'bans':
                    $value = $df->getFriends($id, 'banned');
                    break;
                case 'banOfs':
                    $value = $df->getFriendOfs($id, 'banned');
                    break;
                case 'reads':
                    $value = $df->getFriends($id, 'read');
                    break;
                case 'readOfs':
                    $value = $df->getFriendOfs($id, 'read');
                    break;
                default:
                    throw new DomainException("Unsupported user link type '{$type}'.");
            }

            $trackedLinks = new TrackedSet($value);
            $this->userLinks[$key] = $trackedLinks;
        }

        return $this->userLinks[$key];
    }    

    public function saveUserLinks(int $id, string $type = 'friends') : void
    {
        $this->log->debug("saveUserLinks({$id}, {$type})");

        $key = "{$id}-{$type}";
        if (!isset($this->userLinks[$key])) {
            return;
        }
         
        $df = singletonloader::getInstance('dbfacade');
        $links = $this->userLinks[$key]->flush();

        switch ($type) {
            // TODO: eliminate plural-to-singular link type name conversion
            case 'friends':
                $df->setFriends($id, $links);
                break;
            case 'friendOfs':
                $df->setFriendOfs($id, $links);
                break;
            case 'bans':
                $df->setFriends($id, $links, 'banned');
                break;
            case 'banOfs':
                $df->setFriendOfs($id, $links, 'banned');
                break;
            case 'reads':
                $df->setFriends($id, $links, 'read');
                break;
            case 'readOfs':
                $df->setFriendOfs($id, $links, 'read');
                break;
            default:
                throw new DomainException("Unsupported user link type '{$type}'.");
        }
	}

	public function getProperty($name){
		$this->log->debug("checking: ".$this->props[$name]);
		return(isset($this->props[$name])?$this->props[$name]:false);
	}

	public function parseURL($url) {
		$this->log->debug2("parsing url $url");
		if (strlen($url) == 0) {
			if($this->getProperty("loggedin")==false)
				$ActionPage = "splash";
			else
				$ActionPage = "default";
			$this->log->debug("Action Page is {$ActionPage}");
			return $ActionPage;
		}
		$p = explode("/", $url);
		$this->params=$p;
		if ($p[sizeof($p) - 1] == "")
			unset ($p[sizeof($p) - 1]);
		if ($p[0] == "users") {
			if(sizeof($p) > 1 and $p[1]!='rss')
			{
				$this->blog = $this->getUser($p[1]);
				array_shift($p);
			}
			else
			{
				$this->blog = $this->getUser("all");
			}
			$this->props["blog"] = true;
			$this->log->debug("blog is set");

			array_shift($p);
			$this->params=$p;
			$ActionPage = "blog";
			return $ActionPage;
		}
		if($p[0]=="mail" or $p[0]=="privates")
		{
			$ActionPage = "mail";

			return $ActionPage;
		}

		if (class_exists($p[0]."AP")) {
			$ActionPage = $p[0];
			array_shift($p);
		} else {
			$ActionPage = "error";
		}
		$this->params=$p;
		$this->log->debug("Action Page is {$ActionPage}");
	return $ActionPage;
	}
}
