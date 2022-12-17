<?php

class headerAction extends Action{

 	function execute()
 	{
		$google=$this->context->blog->google;
		$blogName=strip_tags($this->context->blog->blogName);
		if($google=='N')
			$out["robots"]="";
		if($blogName!='')
		 $blogName.=" -";
		else{
			$blogName=$this->context->blog->login." -";
		}
		$out["title"]="$blogName Sarok.org";
		$out["login"]=$this->context->blog->login;
		$addr=$_GET['p'];
		if($addr[strlen($addr)-1]!='/') $addr.="/";
		$addr.="rss/";
		$out["rss"]="/".$addr;
		
		$out["entriesPerPage"]=$this->context->blog->entriesPerPage;
		$out["numRows"]=sizeof($this->context->ActionPage->rows ?? array());
		$out["blogName"]=$this->context->blog->login;
		$params=$this->context->ActionPage->params;
		if(!is_array($params)) $params=array();
			$out["params"]=$params;
		$out=array_merge($out,$params);
		return $out;
 	}
}
?>
