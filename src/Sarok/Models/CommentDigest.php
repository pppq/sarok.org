<?php declare(strict_types=1);

namespace Sarok\Models;

use Sarok\Util;
use Sarok\Models\CommentDigestCategory;
use Sarok\Models\AccessType;
use DateTime;

/**
 * Represents a shortened version of a comment, displayed on the dashboard.
 * 
 * Table structure for `cache_commentlist`:
 * 
 * ```sql
 * `category`   enum('comments','commentsOfEntries','myComments') NOT NULL DEFAULT 'comments',
 * `ID`         int(11)          NOT NULL DEFAULT '0',
 * `ownerID`    int(10) unsigned NOT NULL DEFAULT '0',
 * `userID`     char(30)         NOT NULL DEFAULT '',
 * `diaryID`    char(30)         NOT NULL DEFAULT '',
 * `entryID`    int(11)          NOT NULL DEFAULT '0',
 * `createDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `access`     enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
 * `body`       char(60)         NOT NULL DEFAULT '',
 * `lastUsed`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * ```
 */
class CommentDigest
{
    const FIELD_CATEGORY    = 'category';
    const FIELD_ID          = 'ID';
    const FIELD_OWNER_ID    = 'ownerID';
    const FIELD_USER_ID     = 'userID';
    const FIELD_DIARY_ID    = 'diaryID';
    const FIELD_ENTRY_ID    = 'entryID';
    const FIELD_CREATE_DATE = 'createDate';
    const FIELD_ACCESS      = 'access';
    const FIELD_BODY        = 'body';
    const FIELD_LAST_USED   = 'lastUsed';
    
    private CommentDigestCategory $category     = CommentDigestCategory::COMMENTS;
    private int                   $ID           = 0;
    private int                   $ownerID      = 0;
    private string                $userID       = ''; // XXX: this is the user's login name
    private string                $diaryID      = ''; // XXX: this is the diary owner's login name
    private int                   $entryID      = 0;
    private DateTime              $_createDate;
    private AccessType            $access       = AccessType::ALL;
    private string                $body         = '';
    private DateTime              $_lastUsed;

    public function __construct()
    {
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::zeroDateTime();
        }

        if (!isset($this->_lastUsed)) {
            $this->_lastUsed = Util::zeroDateTime();
        }
    }
    
    public function __set(string $name, mixed $value) : void
    {
        if (self::FIELD_CREATE_DATE === $name && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_LAST_USED === $name && is_string($value)) {
            $this->setLastUsed(Util::utcDateTimeFromString($value));
        }
    }
    
    public function getCategory() : CommentDigestCategory
    {
        return $this->category;
    }

    public function setCategory(CommentDigestCategory $category) : void
    {
        $this->category = $category;
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

    public function getEntryID() : int
    {
        return $this->entryID;
    }

    public function setEntryID(int $entryID) : void
    {
        $this->entryID = $entryID;
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
        return $this->access;
    }

    public function setAccess(AccessType $access) : void
    {
        $this->access = $access;
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
            self::FIELD_CATEGORY    => $this->category->value,
            self::FIELD_ID          => $this->ID,
            self::FIELD_OWNER_ID    => $this->ownerID,
            self::FIELD_USER_ID     => $this->userID,
            self::FIELD_DIARY_ID    => $this->diaryID,
            self::FIELD_ENTRY_ID    => $this->entryID,
            self::FIELD_CREATE_DATE => Util::dateTimeToString($this->_createDate),
            self::FIELD_ACCESS      => $this->access->value,
            self::FIELD_BODY        => $this->body,
            self::FIELD_LAST_USED   => Util::dateTimeToString($this->_lastUsed),
        );
    }
}
