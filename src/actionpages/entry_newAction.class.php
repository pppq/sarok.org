<?php

class entry_newAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$blog=$this->context->blog;
		$user=$this->context->user;
		$out["blogLogin"]=$blog->login;
		if($user->login=="Anonim") return array();
		$out["userID"]=$user->ID;
		$out["userLogin"]=$user->login;
		$out["messageAccess"]=$blog->messageAccess;
		$out["commentAccess"]=$blog->commentAccess;
		$out["createDate"]=now();
		$out["body"]=$user->backup;
		$out["posX"]=$user->posX;
		$out["posY"]=$user->posY;
		$out["bindToMap"]=$user->bindToMap;
		return $out;
 	}
}
?>