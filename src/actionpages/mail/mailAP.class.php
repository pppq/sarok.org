<?php

class mailAP extends ActionPage{
public $actionList=array("senderList","searchMail","mailCalendar");
private $params;
    function init() {
	$this->params=$this->context->params;
	switch($this->params[1])
	{

	}
    }
}
?>