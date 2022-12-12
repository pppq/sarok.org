<?php declare(strict_types=1);

namespace Sarok\Models;

use Sarok\Util;
use Sarok\Models\AccessType;
use DateTime;

/**
 * Represents a diary entry.
 * 
 * Table structure for `entries`:
 *
 * ```sql
 * `ID`               int(10) unsigned NOT NULL AUTO_INCREMENT,
 * `diaryID`          int(10) unsigned NOT NULL DEFAULT '0',
 * `userID`           int(10) unsigned NOT NULL DEFAULT '0',
 * `createDate`       datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `modifyDate`       datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `access`           enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
 * `comments`         enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
 * `title`            varchar(255)     NOT NULL DEFAULT '',
 * `body`             longtext         NOT NULL DEFAULT '',
 * `body2`            longtext         NOT NULL DEFAULT '',
 * `numComments`      int(10) unsigned NOT NULL DEFAULT '0',
 * `lastComment`      datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `lastVisit`        datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `isTerminated`     enum('Y','N')    NOT NULL DEFAULT 'N',
 * `moderatorComment` varchar(255)     NOT NULL DEFAULT '',
 * `category`         int(10) unsigned NOT NULL DEFAULT '0',
 * `dayDate`          date             NOT NULL DEFAULT '0000-00-00',
 * `rssURL`           varchar(255)     NOT NULL DEFAULT '',
 * `posX`             double                    DEFAULT NULL,
 * `posY`             double                    DEFAULT NULL,
 * ```
 */
class Entry
{
    const FIELD_ID                = 'ID';
    const FIELD_DIARY_ID          = 'diaryID';
    const FIELD_USER_ID           = 'userID';
    const FIELD_CREATE_DATE       = 'createDate';
    const FIELD_MODIFY_DATE       = 'modifyDate';
    const FIELD_ACCESS            = 'access';
    const FIELD_COMMENTS          = 'comments';
    const FIELD_TITLE             = 'title';
    const FIELD_BODY_1            = 'body';
    const FIELD_BODY_2            = 'body2';
    const FIELD_NUM_COMMENTS      = 'numComments';
    const FIELD_LAST_COMMENT      = 'lastComment';
    const FIELD_LAST_VISIT        = 'lastVisit';
    const FIELD_IS_TERMINATED     = 'isTerminated';
    const FIELD_MODERATOR_COMMENT = 'moderatorComment';
    const FIELD_CATEGORY          = 'category';
    const FIELD_DAY_DATE          = 'dayDate';
    const FIELD_RSS_URL           = 'rssURL';
    const FIELD_POS_X             = 'posX';
    const FIELD_POS_Y             = 'posY';
    
    private int        $ID               = -1;
    private int        $diaryID          = 0;
    private int        $userID           = 0;
    private DateTime   $_createDate;
    private DateTime   $_modifyDate;
    private AccessType $_access          = AccessType::ALL;
    private AccessType $_comments        = AccessType::ALL;
    private string     $title            = '';
    private string     $body             = '';
    private string     $body2            = '';
    private int        $numComments      = 0;
    private DateTime   $_lastComment;
    private DateTime   $_lastVisit;
    private bool       $_isTerminated    = false;
    private string     $moderatorComment = '';
    private int        $category         = 0;
    private DateTime   $_dayDate;
    private string     $rssURL           = '';
    private ?float     $posX             = null;
    private ?float     $posY             = null;

    public function __construct()
    {
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::utcDateTimeFromString();
        }

        if (!isset($this->_modifyDate)) {
            $this->_modifyDate = Util::zeroDateTime();
        }

        if (!isset($this->_lastComment)) {
            $this->_lastComment = Util::zeroDateTime();
        }

        if (!isset($this->_lastVisit)) {
            $this->_lastVisit = Util::zeroDateTime();
        }

        if (!isset($this->_dayDate)) {
            $this->_dayDate = Util::dateTimeToDate($this->_createDate);
        }
    }
    
    public function __set(string $name, mixed $value) : void
    {
        if (self::FIELD_CREATE_DATE === $name  && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }

        if (self::FIELD_MODIFY_DATE === $name && is_string($value)) {
            $this->setModifyDate(Util::utcDateTimeFromString($value));
        }

        if (self::FIELD_LAST_COMMENT === $name && is_string($value)) {
            $this->setLastComment(Util::utcDateTimeFromString($value));
        }

        if (self::FIELD_LAST_VISIT === $name && is_string($value)) {
            $this->setLastVisit(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_IS_TERMINATED === $name && is_string($value)) {
            $this->setTerminated(Util::yesNoToBool($value));
        }
        
        if (self::FIELD_DAY_DATE === $name && is_string($value)) {
            $this->setDayDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_POS_X === $name && is_string($value)) {
            $this->setPosX((float) $value);
        }
        
        if (self::FIELD_POS_Y === $name && is_string($value)) {
            $this->setPosY((float) $value);
        }

        if (self::FIELD_ACCESS === $name && is_string($value)) {
            $this->setAccess(AccessType::from($value));
        }

        if (self::FIELD_COMMENTS === $name && is_string($value)) {
            $this->setComments(AccessType::from($value));
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

    public function getDiaryID() : int
    {
        return $this->diaryID;
    }

    public function setDiaryID(int $diaryID) : void
    {
        $this->diaryID = $diaryID;
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
    
    public function getModifyDate() : DateTime
    {
        return $this->_modifyDate;
    }

    public function setModifyDate(DateTime $modifyDate) : void
    {
        $this->_modifyDate = $modifyDate;
    }

    public function getAccess() : AccessType
    {
        return $this->_access;
    }

    public function setAccess(AccessType $access) : void
    {
        $this->_access = $access;
    }

    public function getComments() : AccessType
    {
        return $this->_comments;
    }

    public function setComments(AccessType $comments) : void
    {
        $this->_comments = $comments;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    public function getBody() : string
    {
        return $this->body;
    }

    public function setBody(string $body) : void
    {
        $this->body = $body;
    }

    public function getBody2() : string
    {
        return $this->body2;
    }

    public function setBody2(string $body2) : void
    {
        $this->body2 = $body2;
    }

    public function getNumComments() : int
    {
        return $this->numComments;
    }

    public function setNumComments(int $numComments) : void
    {
        $this->numComments = $numComments;
    }

    public function getLastComment() : DateTime
    {
        return $this->_lastComment;
    }

    public function setLastComment(DateTime $lastComment) : void
    {
        $this->_lastComment = $lastComment;
    }
    
    public function getLastVisit() : DateTime
    {
        return $this->_lastVisit;
    }

    public function setLastVisit(DateTime $lastVisit) : void
    {
        $this->_lastVisit = $lastVisit;
    }

    public function isTerminated() : bool
    {
        return $this->_isTerminated;
    }

    public function setTerminated(bool $terminated) : void
    {
        $this->_isTerminated = $terminated;
    }

    public function getModeratorComment() : string
    {
        return $this->moderatorComment;
    }

    public function setModeratorComment(string $moderatorComment) : void
    {
        $this->moderatorComment = $moderatorComment;
    }

    public function getCategory() : int
    {
        return $this->category;
    }

    public function setCategory(int $category) : void
    {
        $this->category = $category;
    }

    public function getDayDate() : DateTime
    {
        return $this->_dayDate;
    }

    public function setDayDate(DateTime $dayDate) : void
    {
        $this->_dayDate = Util::dateTimeToDate($dayDate);
    }

    public function getRssURL() : string
    {
        return $this->rssURL;
    }

    public function setRssURL(string $rssURL) : void
    {
        $this->rssURL = $rssURL;
    }

    public function getPosX() : ?float
    {
        return $this->posX;
    }

    public function setPosX(?float $posX) : void
    {
        $this->posX = $posX;
    }

    public function getPosY() : ?float
    {
        return $this->posY;
    }

    public function setPosY(?float $posY) : void
    {
        $this->posY = $posY;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_ID                => $this->ID,
            self::FIELD_DIARY_ID          => $this->diaryID,
            self::FIELD_USER_ID           => $this->userID,
            self::FIELD_CREATE_DATE       => Util::dateTimeToString($this->_createDate),
            self::FIELD_MODIFY_DATE       => Util::dateTimeToString($this->_modifyDate),
            self::FIELD_ACCESS            => $this->_access->value,
            self::FIELD_COMMENTS          => $this->_comments->value,
            self::FIELD_TITLE             => $this->title,
            self::FIELD_BODY_1            => $this->body,
            self::FIELD_BODY_2            => $this->body2,
            self::FIELD_NUM_COMMENTS      => $this->numComments,
            self::FIELD_LAST_COMMENT      => Util::dateTimeToString($this->_lastComment),
            self::FIELD_LAST_VISIT        => Util::dateTimeToString($this->_lastVisit),
            self::FIELD_IS_TERMINATED     => Util::boolToYesNo($this->_isTerminated),
            self::FIELD_MODERATOR_COMMENT => $this->moderatorComment,
            self::FIELD_CATEGORY          => $this->category,
            self::FIELD_DAY_DATE          => Util::dateToString($this->_dayDate),
            self::FIELD_RSS_URL           => $this->rssURL,
            self::FIELD_POS_X             => $this->posX,
            self::FIELD_POS_Y             => $this->posY,
        );
    }
}
