<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

class CommentRating {

    const RATE_POSITIVE = 'rulez';
    const RATE_NEGATIVE = 'sux';
    
    const FIELD_USER_ID = 'userID';
    const FIELD_COMMENT_ID = 'commentID';
    const FIELD_RATE = 'rate';
    const FIELD_CREATE_DATE = 'createDate';

    // Assignment requires conversion via magic method (__set)
    private DateTime $_createDate;
    
    // Assignment from string directly supported
    private int $userID = 0;
    private int $commentID = 0;
    private string $rate = self::RATE_POSITIVE;
    
    public function __construct() {
        // Initialize only if not already set by fetch_object()
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, $value) {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_CREATE_DATE && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
    }
        
    public function getUserID() : int {
        return $this->userID;
    }

    public function setUserID(int $userID) {
        $this->userID = $userID;
    }
    
    public function getCommentID() : int {
        return $this->commentID;
    }

    public function setCommentID(int $commentID) {
        $this->commentID = $commentID;
    }

    public function getCreateDate() : DateTime {
        return $this->createDate;
    }

    public function setCreateDate(DateTime $createDate) {
        $this->_createDate = $createDate;
    }

    public function toArray() : array {
        return array(
            $this->userID,
            $this->commentID,
            $this->rate,
            Util::dateTimeToString($this->_createDate),
        );
    }
}
