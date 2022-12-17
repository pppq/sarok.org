<?php

class mysql 
{
	private int $counter = 0;
	private mysqli $dbcon;
	private log $log;

	public function __construct() 
	{
		global $db_host, $db_name, $db_user, $db_password, $db_port;

		$this->log = singletonloader::getInstance('log');
		$this->log->debug("Connecting to ${db_host}:${db_port} (${db_name}) with user ${db_user}, password ${db_password}");
		$this->dbcon = mysqli_connect($db_host, $db_user, $db_password);
		
		if ($this->dbcon === false) {
			$connect_errno = mysqli_connect_errno();
			$connect_error = mysqli_connect_error();
			$this->log->halt("dbFacade initalization failed. Error ${connect_errno}: ${connect_error}");
			exit;
		}

		mysqli_select_db($this->dbcon, $db_name);

		$this->log->debug("mysql initialized, database connected");
	}

	public function close() : void
	{
		$counter = $this->getCounter();
		$this->log->debug("closing db connection, ${counter} connections made");

		mysqli_close($this->dbcon);
	}

	public function mquery(string $query) : mysqli_result|bool
	{
		$this->log->debug("mquery: ${query}");
		
		$result = mysqli_query($this->dbcon, $query);
		$errno = $this->mysqli_errno();

		if ($errno > 0) {
			$error = $this->mysqli_error();
			$this->log->security("${query} mquery: error ${errno}: ${error}");
			throw new mysqlException("${errno}: ${error}");
		}

		$this->counter++;
		return $result;
	}

	public function queryone(string $query) : array|false|null
	{
		$result = $this->mquery($query);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_free_result($result);
		
		return $row;
	}

	public function querynum($query) : mixed
	{
		$row = $this->queryone($query);

		if (is_array($row)) {
			// Use the first value in the array
			$val = current($row);
			$this->log->debug("Result for querynum is: ${val}");
			return $val;
		} else {
			$this->log->error("querynum: Output row for query is not an array");
			return '';
		}
	}

	public function queryall($query) : array
	{
		$result = $this->mquery($query);

		$resrow = array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$resrow[] = $row;
		}

		mysqli_free_result($result);
		
		$numResults = count($resrow);
		$this->log->debug("queryall: returned ${numResults} rows");
		return $resrow;
	}

	public function mysqli_errno() : int
	{
		return mysqli_errno($this->dbcon);
	}

	public function mysqli_error() : string
	{
		return mysqli_error($this->dbcon);
	}

	public function mysqli_insert_id() : string|int
	{
		return mysqli_insert_id($this->dbcon);
	}

	public function mysqli_affected_rows() : string|int
	{
		return mysqli_affected_rows($this->dbcon);
	}

	public function getCounter() : int
	{
		return $this->counter;
	}
}
