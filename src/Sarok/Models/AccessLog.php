<?php namespace Sarok\Models;

use DateTime;
use Sarok\Util;

/*
 * Table structure for `accesslog`:
 *
 * `datum`      datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `micros`     int(3)       NOT NULL DEFAULT '0',
 * `sessid`     bigint(15)   NOT NULL DEFAULT '0',
 * `action`     varchar(64)  NOT NULL DEFAULT '',
 * `referrer`   varchar(120) NOT NULL DEFAULT '',
 * `ip`         varchar(16)  NOT NULL DEFAULT '',
 * `userCode`   int(11)      NOT NULL DEFAULT '0',
 * `runTime`    int(7)       NOT NULL DEFAULT '0',
 * `numQueries` int(6)       NOT NULL DEFAULT '0',
 */
class AccessLog
{
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
    
    public function __construct()
    {
        // Initialize only if not already set by fetch_object()
        if (!isset($this->_datum)) {
            $this->_datum = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, $value)
    {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_DATUM && is_string($value)) {
            $this->setDatum(Util::utcDateTimeFromString($value));
        }
    }
    
    public function getDatum() : DateTime
    {
        return $this->_datum;
    }

    public function setDatum(DateTime $datum)
    {
        $this->_datum = $datum;
    }

    public function getMicros() : int
    {
        return $this->micros;
    }

    public function setMicros(int $micros)
    {
        $this->micros = $micros;
    }

    public function getSessid() : string
    {
        return $this->sessid;
    }

    public function setSessid(string $sessid)
    {
        $this->sessid = $sessid;
    }

    public function getAction() : string
    {
        return $this->action;
    }

    public function setAction(string $action)
    {
        $this->action = $action;
    }

    public function getReferrer() : string
    {
        return $this->referrer;
    }

    public function setReferrer(string $referrer)
    {
        $this->referrer = $referrer;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getUserCode() : int
    {
        return $this->userCode;
    }

    public function setUserCode(int $userCode)
    {
        $this->userCode = $userCode;
    }

    public function getRunTime() : int
    {
        return $this->runTime;
    }

    public function setRunTime(int $runTime)
    {
        $this->runTime = $runTime;
    }

    public function getNumQueries() : int
    {
        return $this->numQueries;
    }

    public function setNumQueries(int $numQueries)
    {
        $this->numQueries = $numQueries;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_DATUM       => Util::dateTimeToString($this->_datum),
            self::FIELD_MICROS      => $this->micros,
            self::FIELD_SESSID      => $this->sessid,
            self::FIELD_ACTION      => $this->action,
            self::FIELD_REFERRER    => $this->referrer,
            self::FIELD_IP          => $this->ip,
            self::FIELD_USER_CODE   => $this->userCode,
            self::FIELD_RUNTIME     => $this->runTime,
            self::FIELD_NUM_QUERIES => $this->numQueries,
        );
    }
}
