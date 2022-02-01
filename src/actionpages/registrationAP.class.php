<?php
class registrationAP extends ActionPage {
public $queryString;
public $params;


	function init() {
		parent :: init();
		$this->log->debug2("Initializing registrationAP, params: ".implode(", ", $this->context->params));
		$bf=singletonloader::getInstance("blogfacade");
		$mysql=singletonloader::getInstance("mysql");
		$user = $this->context->user;
		$blog = $this->context->blog;
		$login=$user->login;
		$action="step1";
		//print_r($this->context->params);
		if(isset($this->context->params[0]) and isset($_POST) and sizeof($_POST>1))
		{
			$action=$this->context->params[0];
		}

		$this->log->debug("Main action is $action");
		$this->actionList["main"][] = "registration_".$action;
		//$this->actionList["friendlist"][]="logoutForm";

		return $this->actionList;
	}

	public function canRun() {
		return false;
	//	return !$this->context->props["loggedin"];
	}
}
?>
