<?php

class settings_makeMagicAction extends Action{
protected $sessionFacade;
protected $blogFacade;
 	function execute()
 	{
		if(!sizeof($_POST)) return;
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$this->blogFacade=singletonloader::getInstance("blogfacade");
		$context=singletonloader::getInstance("contextClass");
		$db=singletonloader::getInstance("mysql");
		$this->log->debug("Running makeMagicAction");
		extract($_POST);
		$codes=$_POST["codes"];
		//print_r($codes);
		$diaryID=$this->context->user->ID;
		if(!sizeof($codes)) return;
		$codeList=implode(",",$codes);
		$q="select ID from entries where ID in ($codeList) and diaryID='$diaryID' and isTerminated='N' limit ".sizeof($codes);
		$rows=$db->queryall($q);
		$rows=array();
		foreach($rows as $r) $codes[]=$r["ID"];
		if(sizeof($codes)!=sizeof($_POST["codes"]))
		{
			$this->log->security("settings_makeMagicAction: number of codes allowed is smaller then number of codes queried!");
			return;
		}
		if(isset($tags) and strlen($tags))
		{
			$tagList=split("[, ;]+",strip_tags($tags));
			$this->log->debug("Tags is $tags, Taglist is: ".implode(", ",$tagList));
		}
		try{
		switch($act){
			case 'changeaccess': $this->changeAccess($codes,$access);
									break;
			case 'changereadaccess': $this->changeReadAccess($codes,$access);
									break;						
			case 'addtags': $this->addTags($codes,$tagList);
							break;
			case 'deltags': $this->delTags($codes,$tagList);
							break;
			case 'save': $out["xml"]=$this->saveEntries($diaryID,$codes,$comments=='Y');
							break;
			case 'nothing': $out["action"]="";
							break;
			case 'delete': $this->delEntries($codes);
							break;
			default: throw new inputException("Invalid action $action");
				}
			$out["action"]=$act;
		}
		catch(inputException $e)
		{
			$this->log->error($e->msg);
			$out["action"]="";
		}

		return $out;
 	}

 private function changeAccess($codes,$access)
 {
	$this->log->debug("changeAccess(".implode(", ",$codes).", $access)");
		$this->blogFacade->changeAccess($codes,$access,$this->context->user->ID);

 }
  private function changeReadAccess($codes,$access)
 {
	$this->log->debug("changeReadAccess(".implode(", ",$codes).", $access)");
		$this->blogFacade->changeReadAccess($codes,$access,$this->context->user->ID);

 }

 private function addTags($codes,$tags)
 {
 	$this->log->debug("addTags(".implode(", ",$codes).",".implode(", ",$tags).")");
 	foreach($codes as $code){
 		foreach($tags as $tag){
 			$this->blogFacade->addTag($code,$tag);
 		}
 }
 			$this->blogFacade->clearTagList($this->context->user->ID);
}

private function delTags($codes,$tags)
 {
 	$this->log->debug("delTags(".implode(", ",$codes).",".implode(", ",$tags).")");
 	foreach($codes as $code){
 		foreach($tags as $tag){
 			$this->blogFacade->delTag($code,$tag);
 		}
 }
 $this->blogFacade->clearTagList($this->context->user->ID);
}

private function delEntries($codes)
{
$this->log->debug("delEntries(".implode(", ",$codes).")");
	foreach($codes as $code){
 			$this->blogFacade->removeEntry($code);
 		}
}

private function saveEntries($diaryID,$codes,$withComments)
{
$this->log->debug("saveEntries($diaryID,".implode(", ",$codes).", $withComments)");
$ef=singletonloader::getInstance("exportfacade");
	return($ef->getDiary($diaryID,$codes,$withComments));
}

}
?>
