<?php declare(strict_types=1);

namespace Sarok\Models;

use DateTime;
use Sarok\Util;

/**
 * Represents a Useless Internet Point awarded to a comment (positive and negative 
 * rating are both allowed).
 * 
 * Table structure for `commentrates`:
 * 
 * ```sql
 * `userID`     int(11) NOT NULL,
 * `commentID`  int(11) NOT NULL,
 * `rate`       enum('rulez','sux') NOT NULL DEFAULT 'rulez',
 * `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 * ```
 */
class CommentRating
{
    const RATE_POSITIVE = 'rulez';
    const RATE_NEGATIVE = 'sux';
    
    const FIELD_USER_ID     = 'userID';
    const FIELD_COMMENT_ID  = 'commentID';
    const FIELD_RATE        = 'rate';
    const FIELD_CREATE_DATE = 'createDate';
    
    private int      $userID       = 0;
    private int      $commentID    = 0;
    private string   $rate         = self::RATE_POSITIVE;
    private DateTime $_createDate;
    
    public function __construct()
    {
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, mixed $value) : void
    {
        if (self::FIELD_CREATE_DATE === $name && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
    }
        
    public function getUserID() : int
    {
        return $this->userID;
    }

    public function setUserID(int $userID) : void
    {
        $this->userID = $userID;
    }
    
    public function getCommentID() : int
    {
        return $this->commentID;
    }

    public function setCommentID(int $commentID) : void
    {
        $this->commentID = $commentID;
    }

    public function getRate() : string
    {
        return $this->rate;
    }

    public function setRate(string $rate) : void
    {
        $this->rate = $rate;
    }

    public function getCreateDate() : DateTime
    {
        return $this->_createDate;
    }

    public function setCreateDate(DateTime $createDate) : void
    {
        $this->_createDate = $createDate;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_USER_ID     => $this->userID,
            self::FIELD_COMMENT_ID  => $this->commentID,
            self::FIELD_RATE        => $this->rate,
            self::FIELD_CREATE_DATE => Util::dateTimeToString($this->_createDate),
        );
    }
}
