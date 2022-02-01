<?php

class sidebarAction extends Action{
protected $sessionFacade;
protected $db;
 	function execute()
 	{
		$bf=singletonloader :: getInstance("blogfacade");
		$text=$this->context->blog->blogText;
		$out["text"]=$text;
		$out["blogLogin"]=$this->context->blog->login;
		$tags=$bf->getTagList($this->context->blog->ID);
		$out["rows"]=$tags["tags"];
		return $out;
 	}
}
?>