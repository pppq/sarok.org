<?php namespace Sarok\Service;

use Sarok\Logger;
use Sarok\Exceptions\DBException;
use mysqli;
use mysqli_result;

class DB {
	private Logger $logger;
    private int $queryCount = 0;
	private mysqli $conn;
	
	public function __construct(
	    Logger $logger, 
	    string $db_host, 
	    string $db_name, 
	    string $db_user, 
	    string $db_password,
	    int $db_port = 3306) {
	    
		$this->logger = $logger;
		$this->logger->debug("Connecting to $db_host:$db_port ($db_name) with user $db_user");
		
		mysqli_report(MYSQLI_REPORT_OFF);
		
		$this->conn = new mysqli($db_host, $db_user, $db_password, $db_name, $db_port);
		if ($this->conn->connect_errno) {
		    throw new DBException(
		        'Database connection failed. Error: ' . $this->conn->connect_error,
		        $this->conn->connect_errno);
		}
		
		$this->conn->set_charset('utf8mb4');
		if ($this->conn->errno) {
		    throw new DBException(
		        'Failed to set character set for database connection. Error: ' . $this->conn->error,
		        $this->conn->errno);
		}
		
		$this->conn->autocommit(true);
    	$this->logger->debug("Database connected");
	}

	public function close() {
		$this->logger->debug("Closing database connection after $this->queryCount queries");
		$this->conn->close();
	}
	
	public function __destruct() {
	    $this->close();
	}
	
	private function queryOrExecute(string $query, string $format, array &$params) {
	    $stmt = $this->conn->prepare($query);
	    if ($stmt === false) {
	        throw new DBException(
	            "Failed to prepare statement for query '$query'. Error: " . $this->conn->error,
	            $this->conn->errno);
	    }

	    if (strlen($format) > 0) {
	        if ($stmt->bind_param($format, ...$params) === false) {
	            throw new DBException(
	                "Failed to bind parameters for query '$query'. Error: " . $this->conn->error,
	                $this->conn->errno);
	        }
	    }
	    
	    if ($stmt->execute() === false) {
	        throw new DBException(
	            "Failed to execute statement for query '$query'. Error: " . $this->conn->error,
	            $this->conn->errno);
	    }
	        
	    $this->queryCount++;
	    
	    $result = $stmt->get_result();
	    if ($result === false) {
    	    // We know that execute succeeded, so return the number of affected rows
	        return $stmt->affected_rows;
	    } else {
	        return $result;
	    }
	}
	
	public function query(string $query, string $format = '', &...$params) : mysqli_result {
	    $this->logger->debug("query: $query");
	    return $this->queryOrExecute($query, $format, $params);
	}
	
	public function execute(string $query, string $format = '', &...$params) : int {
	    $this->logger->debug("execute: $query");
	    return $this->queryOrExecute($query, $format, $params);
	}
	
	private function queryOrExecuteWithParams(string $query, array &$stringParams) {
	    $stmt = $this->conn->prepare($query);
	    if ($stmt === false) {
	        throw new DBException(
	            "Failed to prepare statement for query '$query'. Error: " . $this->conn->error,
	            $this->conn->errno);
	    }
	    
	    if ($stmt->execute($stringParams) === false) {
	        throw new DBException(
	            "Failed to execute statement for query '$query'. Error: " . $this->conn->error,
	            $this->conn->errno);
	    }
	        
	    $this->queryCount++;
	    $result = $stmt->get_result();
	    if ($result === false) {
	        // We know that execute succeeded, so return the number of affected rows
	        return $stmt->affected_rows;
	    } else {
	        return $result;
	    }
	}
	
	public function queryWithParams(string $query, array &$stringParams) : mysqli_result {
	    $this->logger->debug("queryWithParams: $query");
	    return $this->queryOrExecuteWithParams($query, $stringParams);
	}
	
	public function executeWithParams(string $query, array &$stringParams) : int {
	    $this->logger->debug("executeWithParams: $query");
	    return $this->queryOrExecuteWithParams($query, $stringParams);
	}

	private function toObjects(mysqli_result $result, string $className) : array {
	    $rows = array();
	    while ($row = $result->fetch_object($className)) {
	        $rows[] = $row;
	    }
	    
	    $numObjects = count($rows);
	    $this->logger->debug("queryObjectArray: returning $numObjects objects");
	    return $rows;
	}
	
	public function queryObjects(string $query, string $className, string $format = '', &...$params) : array {
	    $result = $this->query($query, $format, ...$params);
	    return $this->toObjects($result, $className);
	}
	
	public function queryObjectsWithParams(string $query, string $className, array &$stringParams) : array {
	    $result = $this->queryWithParams($query, $stringParams);
	    return $this->toObjects($result, $className);
	}
	
// 	public function mquery($query) {
// 		$this->logger->debug("mquery: ".$query);
// 		$result = mysql_query($query);
// 		if (mysql_errno()) {
// 			$this->logger->security("$query mquery: error ".mysql_errno().": ".mysql_error());
// 			throw new mysqlException(mysql_errno().": ".mysql_error());
// 		}
// 		$this->queryCount++;
// 		return ($result);
// 	}

// 	public function queryone($query) {
// 		$res = $this->mquery($query);
// 		$row = mysql_fetch_array($res, MYSQL_ASSOC);
// 		mysql_free_result($res);
// 		return ($row);
// 	}

// 	public function querynum($query) {
// 		$row = $this->queryone($query);
// 		//print_r($row);
// 		if (is_array($row)) {
// 			list ($key, $val) = each($row);
// 			$this->logger->debug("Result for querynum is: ".$val);
// 			return ($val);
// 		} else {
// 			$this->logger->error("querynum: Output row for query is not an array");
// 			return ('');
// 		}
// 	}

// 	public function queryall($query) {
// 		global $result;
// 		$i = 0;

// 		$result = $this->mquery($query);
// 		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
// 			$resrow[$i] = $row;
// 			$i ++;
// 		}
// 		mysql_free_result($result);
// 		$this->logger->debug("queryall: returned $i rows");
// 		return (isset ($resrow) ? $resrow : null);
// 	}

// 	public function now() {
// 		return (date("Y-m-d G:i:s"));
// 	}
// 	public function Yearmonth() {
// 		return (date("Y-m"));
// 	}

// 	public function human_date($date) {
// 		global $honapok;
// 		$dates = explode("-", $date);
// 		$out = $dates[2].". ".$honapok[(int) $dates[1]]." ".$dates[0];
// 		return ($out);
// 	}

// 	public function human_time($date) {
// 		$dd = explode(" ", $date);
// 		$out = $dd[1].", ".human_date($dd[0]);
// 		return ($out);
// 	}

// 	public function weekend($num) {
// 		$n = ($num -1) * 7;
// 		$result = $this->mquery("select '2002-01-06' + interval $n day  as d");
// 		$d = mysql_fetch_array($result);
// 		return ($d["d"]);
// 	}

// 	public function year() {
// 		return (date("Y"));
// 	}

// 	public function month() {
// 		return (date("m"));
// 	}

// 	public function day() {
// 		return (date("d"));
// 	}
}
