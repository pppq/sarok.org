<?php
class settingsAP extends ActionPage {

	function init() {
		global $tileList;
		if(!parent :: init()) return false;
		$tileList["friendlist"] = array ();


		//print_r($this->context->params);
		$p = (sizeof($this->context->params)>0)?$this->context->params[0]:"";
		switch ($p) {
			case "blog" :
				$action = "settings_blog";
				break;
			case "uploads" :
				$action = "settings_uploads";
				break;
			case "friends" :
				$action = "settings_friends";
				break;
			case "skin" :
				$action = "settings_skin";
				break;
			case "ski" :
				$action = "settings_ski";
				break;	
			case "images" :
				$action = "settings_images";
				break;
			case "magic" :
				$action = "settings_magic";
				break;
			case "map" :
				$action = "settings_map";
				break;
			case "other" :
				$action = "settings_other";
				break;
			case "stats" :
				$action = "settings_stats";
				break;	
			case "makeMagic" :
				$action = "settings_makeMagic";
				if(isset($_POST["act"]) and $_POST["act"]=="save")
					$this->context->ActionPage->templateName = "empty";
				break;
			case "import" :
				$action = "settings_import";
				break;
			case "makeImport" :
				$action = "settings_makeImport";
				break;
			default :
				$action = "settings_info";
		}
		if (sizeof($this->context->params)>1 and $this->context->params[1] == "set" and isset ($_POST)) {
			$action = "set";
			$this->context->ActionPage->templateName = "empty";
		}
		$this->log->debug("Action is $action");
		$this->actionList["main"][] = $action;
		return $this->actionList;
	}

	public function canRun() {
		$this->log->debug("running canRun for settingsAP");
		$this->log->debug("Loggedin is ".$this->context->props["loggedin"]);
		if($this->context->props["loggedin"]==true)
		 {
		 $this->log->debug("return true");
		 return true;
		 }
		else
		{
			$this->log->debug("return false");
		return false;
		}

	}
}
?>