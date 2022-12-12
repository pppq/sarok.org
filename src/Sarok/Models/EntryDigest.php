<?php declare(strict_types=1);

namespace Sarok\Models;

use DateTime;
use Sarok\Util;
use Sarok\Models\AccessType;

/**
 * Represents a shortened version of each entry visible to a user, displayed on the dashboard.
 * 
 * Table structure for `cache_entrylist`:
 *
 * ```sql
 * `ID`         int(11)          NOT NULL DEFAULT '0',
 * `ownerID`    int(10) unsigned NOT NULL DEFAULT '0',
 * `userID`     char(30)         NOT NULL DEFAULT '',
 * `diaryID`    char(30)         NOT NULL DEFAULT '',
 * `createDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `access`     enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
 * `body`       char(60)         NOT NULL DEFAULT '',
 * `lastUsed`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * ```
 */
class EntryDigest
{
    const FIELD_ID          = 'ID';
    const FIELD_OWNER_ID    = 'ownerID';
    const FIELD_USER_ID     = 'userID';
    const FIELD_DIARY_ID    = 'diaryID';
    const FIELD_CREATE_DATE = 'createDate';
    const FIELD_ACCESS      = 'access';
    const FIELD_BODY        = 'body';
    const FIELD_LAST_USED   = 'lastUsed';
    
    private int        $ID           = 0;
    private int        $ownerID      = 0;
    private string     $userID       = '';
    private string     $diaryID      = '';
    private DateTime   $_createDate;
    private AccessType $_access      = AccessType::ALL;
    private string     $body         = '';
    private DateTime   $_lastUsed;
    
    public function __construct()
    {
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::zeroDateTime();
        }

        if (!isset($this->_lastUsed)) {
            $this->_lastUsed = Util::zeroDateTime();
        }
    }
    
    public function __set(string $name, $value) : void
    {
        if (self::FIELD_CREATE_DATE === $name && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_LAST_USED === $name && is_string($value)) {
            $this->setLastUsed(Util::utcDateTimeFromString($value));
        }

        if (self::FIELD_ACCESS === $name && is_string($value)) {
            $this->setAccess(AccessType::from($value));
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

    public function getOwnerID() : int
    {
        return $this->ownerID;
    }

    public function setOwnerID(int $ownerID) : void
    {
        $this->ownerID = $ownerID;
    }

    public function getUserID() : string
    {
        return $this->userID;
    }

    public function setUserID(string $userID) : void
    {
        $this->userID = $userID;
    }

    public function getDiaryID() : string
    {
        return $this->diaryID;
    }

    public function setDiaryID(string $diaryID) : void
    {
        $this->diaryID = $diaryID;
    }

    public function getCreateDate() : DateTime
    {
        return $this->_createDate;
    }

    public function setCreateDate(DateTime $createDate) : void
    {
        $this->_createDate = $createDate;
    }

    public function getAccess() : AccessType
    {
        return $this->_access;
    }

    public function setAccess(AccessType $access) : void
    {
        $this->_access = $access;
    }

    public function getBody() : string
    {
        return $this->body;
    }

    public function setBody(string $body) : void
    {
        $this->body = $body;
    }

    public function getLastUsed() : DateTime
    {
        return $this->_lastUsed;
    }

    public function setLastUsed(DateTime $lastUsed) : void
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
            self::FIELD_ACCESS      => $this->_access->value,
            self::FIELD_BODY        => $this->body,
            self::FIELD_LAST_USED   => Util::dateTimeToString($this->_lastUsed),
        );
    }
}
