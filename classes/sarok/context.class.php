<?php
class contextClass {
	public $users = array (); //hash of loaded users
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
	public function __construct() {
		$this->log = singletonloader :: getInstance("log");
		}

	public function requestUserDAL($ida) {
		$this->log->debug2("requestUserDAL($ida)");
		$id=userDAL::findID($ida);
		if (!array_key_exists($id, $this->users)) {
			new userDAL($id);
			$this->log->debug("$id added to container");
		}

		return ($this->users[$id]);
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
				$this->blog = $this->requestUserDAL($p[1]);
				array_shift($p);
			}
			else
			{
				$this->blog = $this->requestUserDAL("all");
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
?>