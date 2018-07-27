<?php

class ActionPage {
public $actionList=array();
public $templateName="default";
protected $log, $context;

    function ActionPage() {
		$this->log=singletonloader::getInstance("log");
		$this->context=singletonloader::getInstance("contextClass");
		$this->context->ActionPage=$this;
		//$this->context=singletonloader::getInstance("context");
    }

	public function execute($tileList)
	{
		$data=array();

		foreach($tileList as $key=>$value)
		{
		for($i=0;$i<sizeof($value);$i++)
		{
			$data[$value[$i]]=singletonloader::getInstance($value[$i]."Action")->execute();

		}
		}
		return $data;
	}

	public function init()
	{
		$this->log->debug("running ActionPage-> init()");
		if(!$this->canRun()) return false;
		return true;
	}

	public function getTemplate()
	{
		return $this->templateName;
	}

public function canRun()
{
	return true;
}
}
?>