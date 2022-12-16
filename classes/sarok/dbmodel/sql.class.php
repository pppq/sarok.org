<?
class mysql {
	public $counter = 0;
	public $dbcon;
	public $log;
	public function __construct() {
		global $db_host, $db_name, $db_user, $db_password, $db_port;
		$this->log = singletonloader :: getInstance("log");
		$this->log->debug("Connecting to $db_host:$db_port ($db_name) with user $db_user, password $db_password");
		$this->dbcon = mysqli_connect($db_host, $db_user, $db_password);
		if ($this->dbcon === FALSE) {
			$this->log->halt("dbFacade initilization failed. Error: ".mysqli_error());
			exit;
		}
		mysqli_select_db($db_name,$this->dbcon);
		$this->log->debug("mysql initialized, database connected");
	}

	public function close() {
		$this->log->debug2("closing db connection, $this->counter connections made");
		mysqli_close($this->dbcon);
	}

	public function mquery($query) {
		$this->log->debug("mquery: ".$query);
		$result = mysqli_query($query);
		if (mysqli_errno()) {
			$this->log->security("$query mquery: error ".mysqli_errno().": ".mysqli_error());
			throw new mysqlException(mysqli_errno().": ".mysqli_error());
		}
		$this->counter++;
		return ($result);
	}

	public function queryone($query) {
		$res = $this->mquery($query);
		$row = mysqli_fetch_array($res, MYSQLI_ASSOC);
		mysqli_free_result($res);
		return ($row);
	}

	public function querynum($query) {
		$row = $this->queryone($query);
		//print_r($row);
		if (is_array($row)) {
			list ($key, $val) = each($row);
			$this->log->debug("Result for querynum is: ".$val);
			return ($val);
		} else {
			$this->log->error("querynum: Output row for query is not an array");
			return ('');
		}
	}

	public function queryall($query) {
		global $result;
		$i = 0;

		$result = $this->mquery($query);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$resrow[$i] = $row;
			$i ++;
		}
		mysqli_free_result($result);
		$this->log->debug("queryall: returned $i rows");
		return (isset ($resrow) ? $resrow : null);
	}

	public function now() {
		return (date("Y-m-d G:i:s"));
	}
	public function Yearmonth() {
		return (date("Y-m"));
	}

	public function human_date($date) {
		global $honapok;
		$dates = explode("-", $date);
		$out = $dates[2].". ".$honapok[(int) $dates[1]]." ".$dates[0];
		return ($out);
	}

	public function human_time($date) {
		$dd = explode(" ", $date);
		$out = $dd[1].", ".human_date($dd[0]);
		return ($out);
	}

	public function weekend($num) {
		$n = ($num -1) * 7;
		$result = $this->mquery("select '2002-01-06' + interval $n day  as d");
		$d = mysqli_fetch_array($result);
		return ($d["d"]);
	}

	public function year() {
		return (date("Y"));
	}

	public function month() {
		return (date("m"));
	}

	public function day() {
		return (date("d"));
	}

	public function insertquery($table, $fieldstr) {
		$fields = explode(",", $fieldstr);
		$instr = "insert into $table($fieldstr) values(";
		for ($i = 0; $i < sizeof($fields); $i ++) {
			global $$fields[$i];
			$instr .= "'".$$fields[$i]."'";
			if ($i != sizeof($fields) - 1)
				$instr .= ",";
		}
		$instr .= ")";

		return ($instr);
	}

	public function insertarray($table, $datarow) {
		$i = 0;
		$str = "insert into $table(";
		while (list ($key, $val) = each($datarow)) {
			$keys[$i] = $key;
			$values[$i] = $val;
			//  $query.=" $key='$val',";
			$i ++;
		}
		for ($i = 0; $i < sizeof($keys); $i ++) {
			$str .= $keys[$i];
			if ($i != sizeof($keys) - 1)
				$str .= ", ";
		}
		$str .= ") values(";
		for ($i = 0; $i < sizeof($values); $i ++) {
			$str .= "'".$values[$i]."'";
			if ($i != sizeof($values) - 1)
				$str .= ", ";
		}
		return $str.")";
	}

	public function updatequery($TabName, $datarow, $Code, $Codename = "Code") {
			//print_r($datarow);
	$query = "update $TabName SET";
		reset($datarow);
		$i = 0;
		while (list ($key, $val) = each($datarow)) {
			$keys[$i] = $key;
			$values[$i] = $val;
			$query .= " $key='$val',";
			$i ++;
		}
		$query[strlen($query) - 1] = ' ';
		$query .= "where $Codename='$Code' LIMIT 1";

		/*$query.=" values(";
		for($i=0;$i<sizeof($values);$i++) $query.=" '".$values[$i]."',";
		$query[strlen($query)-1]=')';*/
		return ($query);
	}

	public function mysqli_affected_rows() {
		return mysqli_affected_rows($this->dbcon);
	}
}
?>