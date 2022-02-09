<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

class Feed {

    const STATUS_ALLOWED = 'allowed';
    const STATUS_BANNED = 'banned';
    const STATUS_DEFAULT = '-';
    
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
    private string $status = self::STATUS_DEFAULT;
    private string $comment;
    
    public function __construct() {
        if (!isset($this->_lastUpdate)) {
            $this->_lastUpdate = Util::zeroDateTime();
        }
        
        if (!isset($this->_nextUpdate)) {
            $this->_nextUpdate = Util::zeroDateTime();
        }
    }
    
    public function __set(string $name, $value) {
        // Support conversion from string for fetch_object()
        if ($name === Feed::FIELD_LAST_UPDATE && is_string($value)) {
            $this->setLastUpdate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === Feed::FIELD_NEXT_UPDATE && is_string($value)) {
            $this->setNextUpdate(Util::utcDateTimeFromString($value));
        }
    }
    
    public function getID() : int {
        return $this->ID;
    }

    public function setID(int $ID) {
        $this->ID = $ID;
    }

    public function getFeedURL() : string {
        return $this->feedURL;
    }

    public function setFeedURL(string $feedURL)
    {
        $this->feedURL = $feedURL;
    }
    
    public function getBlogID() : int {
        return $this->blogID;
    }
    
    public function setBlogID(int $blogID) {
        $this->blogID = $blogID;
    }
    
    public function getLastUpdate() : DateTime {
        return $this->_lastUpdate;
    }
    
    public function setLastUpdate(DateTime $lastUpdate) {
        $this->_lastUpdate = $lastUpdate;
    }
    
    public function getNextUpdate() : DateTime {
        return $this->_nextUpdate;
    }
    
    public function setNextUpdate(DateTime $nextUpdate) {
        $this->_nextUpdate = $nextUpdate;
    }

    public function getLastEntry() : string
    {
        return $this->lastEntry;
    }

    public function setLastEntry(string $lastEntry) {
        $this->lastEntry = $lastEntry;
    }

    public function getContactEmail() : string  {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail) {
        $this->contactEmail = $contactEmail;
    }

    public function getStatus() : string {
        return $this->status;
    }

    public function setStatus(string $status) {
        $this->status = $status;
    }

    public function getComment() : string {
        return $this->comment;
    }

    public function setComment(string $comment) {
        $this->comment = $comment;
    }

    public function toArray() : array {
        return array(
            $this->ID,
            $this->feedURL,
            $this->blogID,
            Util::dateTimeToString($this->_lastUpdate),
            Util::dateTimeToString($this->_nextUpdate),
            $this->lastEntry,
            $this->contactEmail,
            $this->status,
            $this->comment,
        );
    }
}
