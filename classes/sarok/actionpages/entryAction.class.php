<?php

class entryAction extends Action{
	protected $mysql, $bf;
public function execute() {
    	$this->log->debug("Running entryAction");
		$session=singletonloader::getInstance("sessionfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$this->bf=singletonloader::getInstance("blogfacade");
		$tp=new textProcessor();
		$data=array();

		$params=$this->context->ActionPage->params;
		//$query=$this->context->ActionPage->queryString;
		$rows=$this->context->ActionPage->rows;
		$this->log->debug("Length of the rows is ".sizeof($rows));
		$v=array();
		for($i=0;$i<sizeof($rows);$i++)
		{
			$v[]=$rows[$i]["userID"];
			$v[]=$rows[$i]["diaryID"];
		}
		$logTable=$session->getUserLogins($v);
		$count=0;
		for($i=0;$i<sizeof($rows);$i++)
		{
			$this->log->debug("Processing entry #".$rows[$i][ID]);
			if(!$this->bf->canViewEntry2($rows[$i],$this->context->user)) {continue; };
			$rows2[$count]=$rows[$i];
			$rows2[$count]["body"]=$tp->postFormat($rows[$i]["body"]);
			$rows2[$count]["body2"]=$tp->postFormat($rows[$i]["body2"]);
			$rows2[$count]["canChange"]=$this->bf->canChangeEntry($rows[$i],$this->context->user->ID);
			$rows2[$count]["userLogin"]=$logTable[$rows[$i]["userID"]];
			$rows2[$count]["diaryLogin"]=$logTable[$rows[$i]["diaryID"]];
			$rows2[$count]["posX"]=$rows[$i]["posX"];
			$rows2[$count]["posY"]=$rows[$i]["posY"];
			$count++;
//			$logTable
		}
		$data["entries"]=$rows2;
		$data["blogName"]=$this->context->blog->blogName;
		$data["diaryLogin"]=$this->context->blog->login;
		$data["tags"]=$this->context->ActionPage->tags;
    	return $data;
    }
}
?>