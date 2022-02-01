<?php

class navigationAction extends Action{
	protected $mysql;
public function execute() {
    	$this->log->debug("Running entryAction");
		$session=singletonloader::getInstance("sessionfacade");
		$bf=singletonloader::getInstance("blogfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$tp=new textProcessor();
		$data=array();
		$params=array();

		$params=$this->context->ActionPage->params;
		if(!is_array($params)) $params=array();
		$data["blogName"]=$this->context->blog->login;
		$data["blogTitle"]=$this->context->blog->blogName;
		if(!strlen($data["blogTitle"])) $data["blogTitle"]=$data["blogName"];
		$data["email"]=$this->context->blog->email;
		$data["entriesPerPage"]=$this->context->blog->entriesPerPage;
		$data["numRows"]=sizeof($this->context->ActionPage->rows);
		$data["params"]=$params;
		//$path=$bf->analyzePath($params,$this->context->user,$this->context->blog);
		$data=array_merge($data,$params);
		$data["months"]=$bf->getBlogMonths($this->context->blog,$this->context->user);

    	return $data;
    }
}
?>
