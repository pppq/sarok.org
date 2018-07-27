<?php

class mailAP extends ActionPage{
public $action,$mailCode,$reply,$userCode,$fromto,$year,$month,$day,$skip;
private $params;
    function init() {
//	$this->params=$params;
	parent :: init();
	$this->fromto=$this->year=$this->month=$this->day=$this->skip="";
	$action="mail_list";
	$this->reply=false;
	$this->params=$this->context->params;
	$this->log->debug("mailAP params are: ".implode(", ",$this->params));
	if(sizeof($this->params)>1)
	switch($this->params[1])
	{
		case "compose":
		case "new":
			$this->log->debug("Composing new mail");
			$action="mail_compose";
			if(sizeof($this->params)>3 && $this->params[2]=="to")
			{
				$this->log->debug("Recipient: ".$this->params[3]);
				$this->userCode=$this->params[3];
			}
			break;
		case "send":
			if(isset($_POST["body"]))
			{
				$this->log->debug("Sending mail");
				$action="mail_send";
			}
			break;
		case "from":
			if(sizeof($this->params)>2)
			{
				$this->log->debug("FromTo: ".$this->params[2]);
				$this->fromto=$this->params[2];
			}
		default:
		if(ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})",$this->params[1],$regs))
		{
			$this->year=$regs[1];
			$this->month=$regs[2];
			$this->day=$regs[3];
			$action="mail_list";
		}
		elseif(ereg("[0-9]+",$this->params[1]))
		{
			$this->log->debug("Mail code is specified");
			$this->mailCode=$this->params[1];
			$action="mail_read";
			if(sizeof($this->params)>2)
			{
				if($this->params[2]=="reply")
				{
					$this->log->debug("Replying to the mail");
					$action="mail_compose";
					$this->isReply=true;
				}
			}
		}
		$length=sizeof($this->params);
		if($length>=2 && $this->params[$length-2]=="skip" && is_numeric($this->params[$length-1]))
		{
			$this->skip=$this->params[$length-1];
		}


	}
	$this->actionList["main"][] = $action;
	$this->actionList["leftMenu"][] = "mail_partnerList";
	$this->log->debug("Action is $action");
	return $this->actionList;
    }

    public function canRun() {
		return $this->context->props["loggedin"];
	}
}
?>