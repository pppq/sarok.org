<?php declare(strict_types=1);

namespace Sarok;

use mysqli_result;
use mysqli;
use mysqli_stmt;
use Sarok\Logger;
use Sarok\Exceptions\DBException;

final class DB
{
    private const CHARSET = 'utf8mb4';

    private Logger $logger;
    private int $queryCount = 0;
    private mysqli $conn;
    
    public function __construct(
        Logger $logger,
        string $db_host,
        string $db_name,
        string $db_user,
        string $db_password,
        int $db_port = 3306
    ) {
        $this->logger = $logger;
        $this->logger->debug("Connecting to $db_host:$db_port ($db_name) with user $db_user");
        
        mysqli_report(MYSQLI_REPORT_OFF);
        
        $this->conn = new mysqli($db_host, $db_user, $db_password, $db_name, $db_port);
        if ($this->conn->connect_errno) {
            throw new DBException(
                'Database connection failed. Error: ' . $this->conn->connect_error,
                $this->conn->connect_errno
            );
        }
        
        $this->conn->set_charset(self::CHARSET);
        if ($this->conn->errno) {
            throw new DBException(
                'Failed to set character set for database connection. Error: ' . $this->conn->error,
                $this->conn->errno
            );
        }
        
        $this->conn->autocommit(true);
        $this->logger->debug('Database connected');
    }

    public function close() : void
    {
        $queryCount = $this->queryCount;
        $this->logger->debug("Closing database connection after ${queryCount} queries");
        $this->conn->close();
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    private function queryOrExecute(string $query, string $format, array $params) : mysqli_stmt
    {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new DBException(
                "Failed to prepare statement for query '${query}'. Error: " . $this->conn->error,
                $this->conn->errno
            );
        }

        if ($format !== '' && $stmt->bind_param($format, ...$params) === false) {
            throw new DBException(
                "Failed to bind parameters for query '${query}'. Error: " . $this->conn->error,
                $this->conn->errno
            );
        }
        
        if ($stmt->execute() === false) {
            throw new DBException(
                "Failed to execute statement for query '${query}'. Error: " . $this->conn->error,
                $this->conn->errno
            );
        }
            
        $this->queryCount++;
        return $stmt;
    }
    
    public function query(string $query, string $format = '', ...$params) : mysqli_result|false
    {
        $this->logger->debug("query: ${query}");
        return $this
            ->queryOrExecute($query, $format, $params)
            ->get_result();
    }
    
    public function execute(string $query, string $format = '', ...$params) : int
    {
        $this->logger->debug("execute: ${query}");
        return $this
            ->queryOrExecute($query, $format, $params)
            ->affected_rows;
    }
    
    private function queryOrExecuteWithParams(string $query, array $stringParams) : mysqli_stmt
    {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new DBException(
                "Failed to prepare statement for query '${query}'. Error: " . $this->conn->error,
                $this->conn->errno
            );
        }
        
        // XXX: each element is treated as a string parameter
        if ($stmt->execute($stringParams) === false) {
            throw new DBException(
                "Failed to execute statement for query '${query}'. Error: " . $this->conn->error,
                $this->conn->errno
            );
        }
            
        $this->queryCount++;
        return $stmt;
    }
    
    public function queryWithParams(string $query, array $stringParams) : mysqli_result|false
    {
        $this->logger->debug("queryWithParams: ${query}");
        return $this
            ->queryOrExecuteWithParams($query, $stringParams)
            ->get_result();
    }
    
    public function executeWithParams(string $query, array $stringParams) : int
    {
        $this->logger->debug("executeWithParams: ${query}");
        return $this
            ->queryOrExecuteWithParams($query, $stringParams)
            ->affected_rows;
    }

    private function toObjects(mysqli_result $result, string $className) : array
    {
        $rows = array();
        while ($row = $result->fetch_object($className)) {
            $rows[] = $row;
        }
        
        $numObjects = count($rows);
        $this->logger->debug("toObjects: returning ${numObjects} objects");
        return $rows;
    }
    
    public function queryObjects(string $query, string $className, string $format = '', ...$params) : array
    {
        $result = $this->query($query, $format, ...$params);
        if ($result === false) {
            return array();
        } else {
            return $this->toObjects($result, $className);
        }
    }
    
    public function queryObjectsWithParams(string $query, string $className, array $stringParams) : array
    {
        $result = $this->queryWithParams($query, $stringParams);
        if ($result === false) {
            return array();
        } else {
            return $this->toObjects($result, $className);
        }
    }

    public function querySingleObject(string $query, string $className, string $format = '', ...$params) : ?object
    {
        $result = $this->query($query, $format, ...$params);
        if ($result !== false && ($row = $result->fetch_object($className))) {
            return $row;
        } else {
            return null;
        }
    }

    private function toArray(mysqli_result $result) : array
    {
		$values = array();
		while ($row = $result->fetch_row()) {
			$values[] = $row[0];
		}

		$numValues = count($values);
        $this->logger->debug("toArray: returning ${numValues} values");
        return $values;
    }

    public function queryArray(string $query, string $format = '', ...$params) : array
    {
        $result = $this->query($query, $format, ...$params);
        if ($result === false) {
            return array();
        } else {
            return $this->toArray($result);
        }
    }
    
    public function queryArrayWithParams(string $query, array $stringParams) : array
    {
        $result = $this->queryWithParams($query, $stringParams);
        if ($result === false) {
            return array();
        } else {
            return $this->toArray($result);
        }
    }

    public function queryString(string $query, string $defaultValue, string $format = '', ...$params) : string
    {
        $result = $this->query($query, $format, ...$params);
        if ($result !== false && ($row = $result->fetch_row())) {
            return (string) $row[0];
        } else {
            return $defaultValue;
        }
    }

    public function queryInt(string $query, int $defaultValue, string $format = '', ...$params) : int
    {
        $result = $this->query($query, $format, ...$params);
        if ($result !== false && ($row = $result->fetch_row())) {
            return (int) $row[0];
        } else {
            return $defaultValue;
        }
    }

    public function getLastInsertID() : int|string
    {
        return $this->conn->insert_id;
    }
}
