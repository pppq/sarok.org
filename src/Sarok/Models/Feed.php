<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

class FeedStatus
{
    const ALLOWED = 'allowed';
    const BANNED = 'banned';
    const DEFAULT = '-';
}

/*
 * Table structure for `feeds`:
 *
 * `ID`           int(10) unsigned NOT NULL AUTO_INCREMENT,
 * `feedURL`      varchar(255)     NOT NULL DEFAULT '',
 * `blogID`       int(10) unsigned NOT NULL DEFAULT '0',
 * `lastUpdate`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `nextUpdate`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `lastEntry`    varchar(255)     NOT NULL DEFAULT '',
 * `contactEmail` varchar(255)     NOT NULL DEFAULT '',
 * `status`       enum('allowed','banned','-') NOT NULL DEFAULT '-',
 * `comment`      varchar(255)     NOT NULL DEFAULT '',
 */
class Feed
{
    const FIELD_ID = 'ID';
    const FIELD_FEED_URL = 'feedURL';
    const FIELD_BLOG_ID = 'blogID';
    const FIELD_LAST_UPDATE = 'lastUpdate';
    const FIELD_NEXT_UPDATE = 'nextUpdate';
    const FIELD_LAST_ENTRY = 'lastEntry';
    const FIELD_CONTACT_EMAIL = 'contactEmail';
    const FIELD_STATUS = 'status';
    const FIELD_COMMENT = 'comment';
    
    // Assignment requires conversion via magic method (__set)
    private DateTime $_lastUpdate;
    private DateTime $_nextUpdate;
    
    // Assignment from string directly supported
    private int $ID = -1;
    private string $feedURL = '';
    private int $blogID = 0;
    private string $lastEntry = '';
    private string $contactEmail = '';
    private string $status = FeedStatus::DEFAULT;
    private string $comment;
    
    public function __construct()
    {
        if (!isset($this->_lastUpdate)) {
            $this->_lastUpdate = Util::zeroDateTime();
        }
        
        if (!isset($this->_nextUpdate)) {
            $this->_nextUpdate = Util::zeroDateTime();
        }
    }
    
    public function __set(string $name, $value)
    {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_LAST_UPDATE && is_string($value)) {
            $this->setLastUpdate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === self::FIELD_NEXT_UPDATE && is_string($value)) {
            $this->setNextUpdate(Util::utcDateTimeFromString($value));
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

    public function getFeedURL() : string
    {
        return $this->feedURL;
    }

    public function setFeedURL(string $feedURL)
    {
        $this->feedURL = $feedURL;
    }
    
    public function getBlogID() : int
    {
        return $this->blogID;
    }
    
    public function setBlogID(int $blogID)
    {
        $this->blogID = $blogID;
    }
    
    public function getLastUpdate() : DateTime
    {
        return $this->_lastUpdate;
    }
    
    public function setLastUpdate(DateTime $lastUpdate)
    {
        $this->_lastUpdate = $lastUpdate;
    }
    
    public function getNextUpdate() : DateTime
    {
        return $this->_nextUpdate;
    }
    
    public function setNextUpdate(DateTime $nextUpdate)
    {
        $this->_nextUpdate = $nextUpdate;
    }

    public function getLastEntry() : string
    {
        return $this->lastEntry;
    }

    public function setLastEntry(string $lastEntry)
    {
        $this->lastEntry = $lastEntry;
    }

    public function getContactEmail() : string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    public function getStatus() : string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getComment() : string
    {
        return $this->comment;
    }

    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_ID            => $this->ID,
            self::FIELD_FEED_URL      => $this->feedURL,
            self::FIELD_BLOG_ID       => $this->blogID,
            self::FIELD_LAST_UPDATE   => Util::dateTimeToString($this->_lastUpdate),
            self::FIELD_NEXT_UPDATE   => Util::dateTimeToString($this->_nextUpdate),
            self::FIELD_LAST_ENTRY    => $this->lastEntry,
            self::FIELD_CONTACT_EMAIL => $this->contactEmail,
            self::FIELD_STATUS        => $this->status,
            self::FIELD_COMMENT       => $this->comment,
        );
    }
}
