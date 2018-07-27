<?php
class viewHandler {
	private $data;
	private $output;
	private $buf, $log;
	private $templateName;
	private $tiles;
	public function viewHandler() {
		$this->buf = singletonloader :: getInstance("outputHandler");
		$this->log = singletonloader :: getInstance("log");
		$this->log->debug2("viewHandler initialized");
	}

	public function setTemplate($templateName) {
		$this->log->debug("Setting template $templateName");
		if (file_exists("../templates/$templateName/index.php")) {
			$this->templateName = $templateName;
		} else {
			$this->log->warning("$templateName does not exist");
			$this->templateName = "default";
		}
	}
	private function addAction($actionName, $data) {
		$this->data[$actionName] = $data;
	}

	public function addActions($actions) {
		if (!is_array($actions))
			return;
		foreach ($actions as $key => $value) {
			$this->addAction($key, $value);
		}
	}

	private function processAction($actionName) {

		$hint = "../templates/{$this->templateName}/$actionName.php";
		$this->log->debug("hint is $hint");
		if (!file_exists($hint)) {
			$hint = "../templates/default/$actionName.php";
		}
		$this->log->debug("getting content for the $hint");
		return($this->buf->getContent($hint, $this->data[$actionName]));

	}
	public function process($tiles) {
		if (is_array($tiles)) {
			foreach ($tiles as $key => $value) {
				$data[$key] = "";
				for ($i = 0; $i < sizeof($value); $i ++) {
					$this->log->debug("processing $key");
					$data[$key] .= $this->processAction($value[$i]);
				}
			}
		}
		//$out="";
		$out = $this->buf->getContent("../templates/{$this->templateName}/index.php", $data);
		return ($out);
	}
}
?>