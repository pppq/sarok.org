<?php

class entry_editAction extends Action{
protected $sessionFacade;
protected $bf,$mysql;

 	function execute()
 	{
		$blog=$this->context->blog;
		$user=$this->context->user;
		$out=array();
		if($user->login=="Anonim") return array();
		$this->bf=singletonloader::getInstance("blogfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$params=$this->context->ActionPage->params;

		$query=$this->bf->canViewEntry($this->context->ActionPage->entryCode,$this->context->user,$this->context->blog);
		$row=array();
		$row=$this->mysql->queryone($query);
		$row["body"]=$this->bf->mergeBodies($row["body"],$row["body2"]);
		if($this->bf->canChangeEntry($row,$user->ID))
		{
		$out["entry"]=$row;
		$out["blogName"]=$this->context->blog->blogName;
		$out["blogLogin"]=$this->context->blog->login;
		$out["userLogin"]=$user->login;
		$out["userID"]=$user->ID;
		$out["userLogin"]=$user->login;
		$out["messageAccess"]=$row["access"];
		$out["commentAccess"]=$row["comments"];
		$out["posX"]=$row["posX"];
		$out["posY"]=$row["posY"];
		if(is_numeric($out["posX"]) && is_numeric($out["posY"]) && ($out["posX"]*$out["posY"]!=0))
		{
			$out["needsMap"]=true;
		}
		else
		{
				$out["needsMap"]=false;
		}
		$out["list"]="";
		if($row["access"]=="LIST")
		{
			$list=array();
			$row=$this->mysql->queryall("select login from entryaccess left join users on users.ID=entryaccess.userID  where entryID='".$this->context->ActionPage->entryCode."' order by login");
			foreach($row as $r)
			{
				$list[]=$r["login"];
			}
			$out["list"]=implode(", ",$list);
		}
		$out["tags"]=$this->bf->getTags($this->context->ActionPage->entryCode);

		}

		return $out;
 	}
}
?>