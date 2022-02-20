<?php namespace Sarok\Models;

use DateTime;
use Sarok\Util;
use Sarok\Models\AccessType;

/*
 * Table structure for `cache_entrylist`:
 *
 * `ID`         int(11)          NOT NULL DEFAULT '0',
 * `ownerID`    int(10) unsigned NOT NULL DEFAULT '0',
 * `userID`     char(30)         NOT NULL DEFAULT '',
 * `diaryID`    char(30)         NOT NULL DEFAULT '',
 * `createDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `access`     enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
 * `body`       char(60)         NOT NULL DEFAULT '',
 * `lastUsed`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 */
class EntryDigest
{
    const FIELD_ID = 'ID';
    const FIELD_OWNER_ID = 'ownerID';
    const FIELD_USER_ID = 'userID';
    const FIELD_DIARY_ID = 'diaryID';
    const FIELD_CREATE_DATE = 'createDate';
    const FIELD_ACCESS = 'access';
    const FIELD_BODY = 'body';
    const FIELD_LAST_USED = 'lastUsed';
    
    // Assignment requires conversion via magic method (__set)
    private DateTime $_createDate;
    private DateTime $_lastUsed;
    
    // Assignment from string directly supported
    private int $ID = 0; // int(11)
    private int $ownerID = 0; // int(10) unsigned, but we'll probably not see 2 billion+ users
    private string $userID = '';
    private string $diaryID = '';
    private string $access = AccessType::ALL;
    private string $body = '';

    public function __construct()
    {
        // Initialize only if not already set by fetch_object()
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::zeroDateTime();
        }

        if (!isset($this->_lastUsed)) {
            $this->_lastUsed = Util::zeroDateTime();
        }
    }
    
    public function __set(string $name, $value)
    {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_CREATE_DATE && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === self::FIELD_LAST_USED && is_string($value)) {
            $this->setLastUsed(Util::utcDateTimeFromString($value));
        }
    }
    
    public function getID() : int
    {
        return $this->ID;
    }

    public function setID(int $ID)
    {
        $this->ID = $ID;
    }

    public function getOwnerID() : int
    {
        return $this->ownerID;
    }

    public function setOwnerID(int $ownerID)
    {
        $this->ownerID = $ownerID;
    }

    public function getUserID() : string
    {
        return $this->userID;
    }

    public function setUserID(string $userID)
    {
        $this->userID = $userID;
    }

    public function getDiaryID() : string
    {
        return $this->diaryID;
    }

    public function setDiaryID(string $diaryID)
    {
        $this->diaryID = $diaryID;
    }

    public function getCreateDate() : DateTime
    {
        return $this->_createDate;
    }

    public function setCreateDate(DateTime $createDate)
    {
        $this->_createDate = $createDate;
    }

    public function getAccess() : string
    {
        return $this->access;
    }

    public function setAccess(string $access)
    {
        $this->access = $access;
    }

    public function getBody() : string
    {
        return $this->body;
    }

    public function setBody(string $body)
    {
        $this->body = $body;
    }

    public function getLastUsed() : DateTime
    {
        return $this->_lastUsed;
    }

    public function setLastUsed(DateTime $lastUsed)
    {
        $this->_lastUsed = $lastUsed;
    }
    
    public function toArray() : array
    {
        return array(
            self::FIELD_ID          => $this->ID,
            self::FIELD_OWNER_ID    => $this->ownerID,
            self::FIELD_USER_ID     => $this->userID,
            self::FIELD_DIARY_ID    => $this->diaryID,
            self::FIELD_CREATE_DATE => Util::dateTimeToString($this->_createDate),
            self::FIELD_ACCESS      => $this->access,
            self::FIELD_BODY        => $this->body,
            self::FIELD_LAST_USED   => Util::dateTimeToString($this->_lastUsed),
        );
    }
}
