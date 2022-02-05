<?php namespace Sarok\Models;

use DateTime;
use Sarok\Util;

class EntryDigest {
    
    // Allowed values for 'access'
    const ACCESS_ALL = 'ALL';
    const ACCESS_REGISTERED = 'REGISTERED';
    const ACCESS_FRIENDS = 'FRIENDS';
    const ACCESS_PRIVATE = 'PRIVATE';
    const ACCESS_LIST = 'LIST';
    
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
    private string $access = self::ACCESS_ALL;
    private string $body = '';

    public function __construct() {
        // Initialize only if not already set by fetch_object()
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::zeroDateTime();
        }

        if (!isset($this->_lastUsed)) {
            $this->_lastUsed = Util::zeroDateTime();
        }
    }
    
    public function __set(string $name, $value) {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_CREATE_DATE && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === self::FIELD_LAST_USED && is_string($value)) {
            $this->setLastUsed(Util::utcDateTimeFromString($value));
        }
    }
    
    public function getID() : int {
        return $this->ID;
    }

    public function setID(int $ID) {
        $this->ID = $ID;
    }

    public function getOwnerID() : int {
        return $this->ownerID;
    }

    public function setOwnerID(int $ownerID) {
        $this->ownerID = $ownerID;
    }

    public function getUserID() : string {
        return $this->userID;
    }

    public function setUserID(string $userID) {
        $this->userID = $userID;
    }

    public function getDiaryID() : string {
        return $this->diaryID;
    }

    public function setDiaryID(string $diaryID) {
        $this->diaryID = $diaryID;
    }

    public function getCreateDate() : DateTime {
        return $this->_createDate;
    }

    public function setCreateDate(DateTime $createDate) {
        $this->_createDate = $createDate;
    }

    public function getAccess() : string {
        return $this->access;
    }

    public function setAccess(string $access) {
        $this->access = $access;
    }

    public function getBody() : string {
        return $this->body;
    }

    public function setBody(string $body) {
        $this->body = $body;
    }

    public function getLastUsed() : DateTime {
        return $this->_lastUsed;
    }

    public function setLastUsed(DateTime $lastUsed) {
        $this->_lastUsed = $lastUsed;
    }
    
    public function toArray() : array {
        return array(
            $this->ID,
            $this->ownerID,
            $this->userID,
            $this->diaryID,
            Util::dateTimeToString($this->_createDate),
            $this->access,
            $this->body,
            Util::dateTimeToString($this->_lastUsed),
        );
    }
}
