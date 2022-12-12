<?php declare(strict_types=1);

namespace Sarok\Models;

use Sarok\Util;
use DateTime;

/**
 * Represents a single access log entry in the database.
 * 
 * Table structure for `accesslog`:
 * 
 * ```sql
 * `datum`      datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `micros`     int(3)       NOT NULL DEFAULT '0',
 * `sessid`     bigint(15)   NOT NULL DEFAULT '0',
 * `action`     varchar(64)  NOT NULL DEFAULT '',
 * `referrer`   varchar(120) NOT NULL DEFAULT '',
 * `ip`         varchar(16)  NOT NULL DEFAULT '',
 * `userCode`   int(11)      NOT NULL DEFAULT '0',
 * `runTime`    int(7)       NOT NULL DEFAULT '0',
 * `numQueries` int(6)       NOT NULL DEFAULT '0',
 * ```
 */
class AccessLog
{
    const FIELD_DATUM       = 'datum';
    const FIELD_MICROS      = 'micros';
    const FIELD_SESSID      = 'sessid';
    const FIELD_ACTION      = 'action';
    const FIELD_REFERRER    = 'referrer';
    const FIELD_IP          = 'ip';
    const FIELD_USER_CODE   = 'userCode';
    const FIELD_RUNTIME     = 'runTime';
    const FIELD_NUM_QUERIES = 'numQueries';
    
    private DateTime $_datum;
    private int      $micros     = 0;
    private int      $sessid     = 0;
    private string   $action     = '';
    private string   $referrer   = '';
    private string   $ip         = '';
    private int      $userCode   = 0;
    private int      $runTime    = 0;
    private int      $numQueries = 0;

    public function __construct()
    {
        if (!isset($this->_datum)) {
            $this->_datum = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, mixed $value) : void
    {
        if (self::FIELD_DATUM === $name && is_string($value)) {
            $this->setDatum(Util::utcDateTimeFromString($value));
        }
    }
    
    public function getDatum() : DateTime
    {
        return $this->_datum;
    }

    public function setDatum(DateTime $datum) : void
    {
        $this->_datum = $datum;
    }

    public function getMicros() : int
    {
        return $this->micros;
    }

    public function setMicros(int $micros) : void
    {
        $this->micros = $micros;
    }

    public function getSessid() : int
    {
        return $this->sessid;
    }

    public function setSessid(int $sessid) : void
    {
        $this->sessid = $sessid;
    }

    public function getAction() : string
    {
        return $this->action;
    }

    public function setAction(string $action) : void
    {
        $this->action = $action;
    }

    public function getReferrer() : string
    {
        return $this->referrer;
    }

    public function setReferrer(string $referrer) : void
    {
        $this->referrer = $referrer;
    }

    public function getIp() : string
    {
        return $this->ip;
    }

    public function setIp(string $ip) : void
    {
        $this->ip = $ip;
    }

    public function getUserCode() : int
    {
        return $this->userCode;
    }

    public function setUserCode(int $userCode) : void
    {
        $this->userCode = $userCode;
    }

    public function getRunTime() : int
    {
        return $this->runTime;
    }

    public function setRunTime(int $runTime) : void
    {
        $this->runTime = $runTime;
    }

    public function getNumQueries() : int
    {
        return $this->numQueries;
    }

    public function setNumQueries(int $numQueries) : void
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
