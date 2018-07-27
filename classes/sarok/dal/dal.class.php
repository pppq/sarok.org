<?php
abstract class dal {

	protected $values = array ();
	protected $data;
	protected $log;
	protected $dbFacade, $db;
	protected $context;
	public function dal() {
		$this->log = singletonloader :: getInstance("log");
		$this->dbFacade = singletonloader :: getInstance("dbfacade");
		$this->db = singletonloader :: getInstance("mysql");
		$this->context = singletonloader :: getInstance("contextClass");
		$this->log->debug2("initializing dal");
		for ($i = 0; $i < sizeof($this->values); $i ++) {
			$this->data[$this->values[$i]]["value"] = null;
			$this->data[$this->values[$i]]["isLoaded"] = false;
			$this->data[$this->values[$i]]["isDirty"] = false;
		}
	}
	public function __get($member) {
		//$this->log->debug2("Calling getter for $member");

		if (!$this->isKey($member)) {
			$this->log->error("No such field: $member");
			throw new InvalidFieldException("No such field: $member");
		}
		if (!$this->data[$member]["isLoaded"]) {
			$this->update($member);
		}
		return ($this->data[$member]["value"]);
	}

	public function __set($member, $value) {
		$this->log->debug2("Calling setter for $member");
		if (!$this->isKey($member)) {
			$this->log->error("No such field: $member");
			throw new InvalidFieldException("No such field: $member");
		}
		$this->data[$member]["value"] = $value;
		$this->data[$member]["isDirty"] = true;
		$this->data[$member]["isLoaded"] = true;
	}

	public function loadRow($row) {
		$this->log->debug2("Adding a row");
		foreach ($row as $key => $value) {
			$this->data[$key]["value"] = $value;
			$this->data[$key]["isDirty"] = false;
			$this->data[$key]["isLoaded"] = true;
		}
	}

	protected function isKey($key) {
		return in_array($key, $this->values);
	}

	abstract public function update($member);
	abstract public function commit();

	protected function cleanDirty() {
		foreach ($this->data as $key => $val) {
			$this->data[$key]["isDirty"] = false;
		}
	}

}
?>