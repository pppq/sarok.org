<?php

class userListAction extends Action{
protected $db;
 	function execute()
 	{

		$offset=0;
		if(isset($this->context->params[2]))
		{

		$offset=$this->context->params[2];
		$this->log->debug("Setting offset to $offset");
		}
		$this->log->debug("Running userListAction");
		$db=singletonloader:: getInstance("dbfacade");
		$users=$db->getUserList($offset,300);
		$out["users"]=$users;
		
		$out["stat"]=$db->getUserStats();
		
		return $out;
 	}
}
?>