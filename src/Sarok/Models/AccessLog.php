<?php namespace Sarok\Models;

use DateTime;
use DateTimeZone;

class AccessLog {

    const FIELD_DATUM = 'datum';
    const FIELD_MICROS = 'micros';
    const FIELD_SESSID = 'sessid';
    const FIELD_ACTION = 'action';
    const FIELD_REFERRER = 'referrer';
    const FIELD_IP = 'ip';
    const FIELD_USER_CODE = 'userCode';
    const FIELD_RUNTIME = 'runTime';
    const FIELD_NUM_QUERIES = 'numQueries';
    
    // Assignment requires conversion via magic method (__set)
    private DateTime $_datum;
    
    // Assignment from string directly supported
    private int $micros = 0;
    private string $sessid = ''; // bigint(15), does not fit in an int on 32-bit platforms
    private string $action = '';
    private string $referrer = '';
    private string $ip = '';
    private int $userCode = 0;
    private int $runTime = 0;
    private int $numQueries = 0;
    
    public function __construct() {
        // Initialize only if not already set by fetch_object()
        if (!isset($this->_datum)) {
            $this->_datum = new DateTime('now', new DateTimeZone("UTC"));
        }
    }
    
    public function __set(string $name, $value) {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_DATUM && is_string($value)) {
            $this->setDatum(new DateTime($value, new DateTimeZone("UTC")));
        }
    }
    
    public function getDatum() : DateTime {
        return $this->_datum;
    }

    public function setDatum(DateTime $datum) {
        $this->_datum = $datum;
    }

    public function getMicros() : int {
        return $this->micros;
    }

    public function setMicros(int $micros) {
        $this->micros = $micros;
    }

    public function getSessid() : string {
        return $this->sessid;
    }

    public function setSessid(string $sessid) {
        $this->sessid = $sessid;
    }

    public function getAction() : string {
        return $this->action;
    }

    public function setAction(string $action) {
        $this->action = $action;
    }

    public function getReferrer() : string {
        return $this->referrer;
    }

    public function setReferrer(string $referrer) {
        $this->referrer = $referrer;
    }

    public function getIp() {
        return $this->ip;
    }

    public function setIp($ip) {
        $this->ip = $ip;
    }

    public function getUserCode() : int {
        return $this->userCode;
    }

    public function setUserCode(int $userCode) {
        $this->userCode = $userCode;
    }

    public function getRunTime() : int {
        return $this->runTime;
    }

    public function setRunTime(int $runTime) {
        $this->runTime = $runTime;
    }

    public function getNumQueries() : int {
        return $this->numQueries;
    }

    public function setNumQueries(int $numQueries) {
        $this->numQueries = $numQueries;
    }

    public function toArray() : array {
        return array(
            $this->_datum->format('Y-m-d H:i:s'),
            $this->micros,
            $this->sessid,
            $this->action,
            $this->referrer,
            $this->ip,
            $this->userCode,
            $this->runTime,
            $this->numQueries,
        );
    }
}
