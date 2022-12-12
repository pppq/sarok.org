<?php declare(strict_types=1);

namespace Sarok\Models;

use Sarok\Util;
use DateTime;

/**
 * Represents a comment on an entry.
 * 
 * Table structure for `comments`:
 *
 * ```sql
 * `ID`           int(10) unsigned NOT NULL AUTO_INCREMENT,
 * `isTerminated` enum('Y','N')    NOT NULL DEFAULT 'N',
 * `parentID`     int(10) unsigned NOT NULL DEFAULT '0',
 * `entryID`      int(10) unsigned NOT NULL DEFAULT '0',
 * `userID`       int(10) unsigned NOT NULL DEFAULT '0',
 * `createDate`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `body`         longtext         NOT NULL DEFAULT '',
 * `IP`           varchar(255)     NOT NULL DEFAULT '',
 * `dayDate`      date             NOT NULL DEFAULT '0000-00-00',
 * `rate`         int(11)          NOT NULL DEFAULT '0',
 * ```
 */
class Comment
{
    const FIELD_ID            = 'ID';
    const FIELD_IS_TERMINATED = 'isTerminated';
    const FIELD_PARENT_ID     = 'parentID';
    const FIELD_ENTRY_ID      = 'entryID';
    const FIELD_USER_ID       = 'userID';
    const FIELD_CREATE_DATE   = 'createDate';
    const FIELD_BODY          = 'body';
    const FIELD_IP            = 'IP';
    const FIELD_DAY_DATE      = 'dayDate';
    const FIELD_RATE          = 'rate';
    
    private int      $ID             = 0;
    private bool     $_isTerminated  = false;
    private int      $parentID       = 0;
    private int      $entryID        = 0;
    private int      $userID         = 0;
    private DateTime $_createDate;
    private string   $body           = '';
    private string   $IP             = '';
    private DateTime $_dayDate;
    private int      $rate           = 0;

    public function __construct()
    {
        // Initialize only if not already set by fetch_object()
        if (!isset($this->_isTerminated)) {
            $this->_isTerminated = false;
        }

        if (!isset($this->_createDate)) {
            $this->_createDate = Util::utcDateTimeFromString();
        }

        if (!isset($this->_dayDate)) {
            $this->_dayDate = $this->_createDate;
        }
    }
    
    public function __set(string $name, $value) : void
    {
        // Support conversion from string for fetch_object()
        if (self::FIELD_IS_TERMINATED === $name && is_string($value)) {
            $this->setTerminated(Util::yesNoToBool($value));
        }

        if (self::FIELD_CREATE_DATE === $name && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_DAY_DATE === $name && is_string($value)) {
            $this->setDayDate(Util::utcDateTimeFromString($value));
        }
    }

    public function getID() : int
    {
        return $this->ID;
    }

    public function setID(int $ID) : void
    {
        $this->ID = $ID;
    }

    public function isTerminated() : bool
    {
        return $this->_isTerminated;
    }

    public function setTerminated(bool $terminated) : void
    {
        $this->_isTerminated = $terminated;
    }

    public function getParentID() : int
    {
        return $this->parentID;
    }

    public function setParentID(int $parentID) : void
    {
        $this->parentID = $parentID;
    }

    public function getEntryID() : int
    {
        return $this->parentID;
    }

    public function setEntryID(int $parentID) : void
    {
        $this->parentID = $parentID;
    }

    public function getUserID() : int
    {
        return $this->userID;
    }

    public function setUserID(int $userID) : void
    {
        $this->userID = $userID;
    }

    public function getCreateDate() : DateTime
    {
        return $this->_createDate;
    }

    public function setCreateDate(DateTime $createDate) : void
    {
        $this->_createDate = $createDate;
    }

    public function getBody() : string
    {
        return $this->body;
    }

    public function setBody(string $body) : void
    {
        $this->body = $body;
    }

    public function getIP() : string
    {
        return $this->IP;
    }

    public function setIP(string $IP) : void
    {
        $this->IP = $IP;
    }

    public function getDayDate() : DateTime
    {
        return $this->_dayDate;
    }

    public function setDayDate(DateTime $dayDate) : void
    {
        $this->_dayDate = $dayDate;
    }
    
    public function getRate() : int
    {
        return $this->rate;
    }

    public function setRate(int $rate) : void
    {
        $this->rate = $rate;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_ID => $this->ID,
            self::FIELD_IS_TERMINATED => Util::boolToYesNo($this->_isTerminated),
            self::FIELD_PARENT_ID => $this->parentID,
            self::FIELD_ENTRY_ID => $this->entryID,
            self::FIELD_USER_ID => $this->userID,
            self::FIELD_CREATE_DATE => Util::dateTimeToString($this->_createDate),
            self::FIELD_BODY => $this->body,
            self::FIELD_IP => $this->IP,
            // XXX: "dayDate" is stored as a full DateTime, but its time portion is ignored
            self::FIELD_DAY_DATE => Util::dateToString($this->_dayDate),
            self::FIELD_RATE => $this->rate,
        );
    }
}
