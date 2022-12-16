<?php

class blogMapAction extends Action{
	protected $mysql;
	protected $bf;
public function execute() {
    	$this->log->debug("Running blogMapAction");
		$session=singletonloader::getInstance("sessionfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$this->bf=singletonloader::getInstance("blogfacade");
		$tp=new textProcessor();
		$data=array();

		$params=$this->context->ActionPage->params;
		$data["isLoggedIn"]=($this->context->user->ID!=1);
		$entyCodes=array();
		if(isset($this->context->ActionPage->entryCode))
		{
			$entryCodes=array($this->context->ActionPage->entryCode);
			$query=$this->bf->canViewEntries($entryCodes,$this->context->user,$this->context->blog,true);
			$rows=$this->mysql->queryall($query);
		}
		else
		{
			$rows=$this->bf->getMapCoordsForBlog($this->context->user,$this->context->blog);
		}
		
		
if(sizeof($rows)>0)
{
			$coords=array();
			$trans = array("\n" => "<br/>", "\r" => "");
			foreach($rows as $row)
			{
				$coord["posX"]=$row["posX"];
				$coord["posY"]=$row["posY"];
				//$text=
				$coord["text"]="<a href=/users/{$this->context->blog->login}/m_{$row['ID']}/ >#".$row['ID'].": ".$this->context->blog->login."</a><br/>".iconv_substr(strtr(strip_tags($row["title"]." ".$row["createDate"]." ".$row["body"]),$trans),0,50);
				$this->log->debug("popup text is:"+$coord["text"]);
			$coords[]=$coord;
			}
			
		$data["entryID"]=$row["ID"];
		$data["coords"]=$coords;
		$data["userLogin"]=$logtable[$row["userID"]];
		$data["userLoginName"]=$logtable[$row["userID"]];
		$data["myLoginName"]=$this->context->user->login;
		//$this->log->debug("myLoginName is ".$data["myLoginName"]);
		$data["blogName"]=$this->context->blog->blogName;
		$data["diaryLogin"]=$this->context->blog->login;
		
    	//print_r($data);
    	return $data;
    
}
}
}
?>