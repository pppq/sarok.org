<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

/*
 * Table structure for `favourites`:
 * 
 * `userID`      int(10) unsigned NOT NULL DEFAULT '0',
 * `entryID`     int(10) unsigned NOT NULL DEFAULT '0',
 * `lastVisited` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `newComments` int(10) unsigned NOT NULL DEFAULT '0',
 */
class Favourite
{
    const FIELD_USER_ID = 'userID';
    const FIELD_ENTRY_ID = 'entryID';
    const FIELD_LAST_VISITED = 'lastVisited';
    const FIELD_NEW_COMMENTS = 'newComments';
    
    // Assignment requires conversion via magic method (__set)
    private DateTime $_lastVisited;
    
    // Assignment from string directly supported
    private int $userID = 0;
    private int $entryID = 0;
    private int $newComments = 0;

    public function __construct()
    {
        if (!isset($this->_lastVisited)) {
            $this->_lastVisited = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, $value)
    {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_LAST_VISITED && is_string($value)) {
            $this->setDatum(Util::utcDateTimeFromString($value));
        }
    }

    public function getUserID() : int
    {
        return $this->userID;
    }

    public function setUserID(int $userID)
    {
        $this->userID = $userID;
    }
    
    public function getEntryID() : int
    {
        return $this->entryID;
    }

    public function setEntryID(int $entryID)
    {
        $this->entryID = $entryID;
    }
    
    public function getLastVisited() : DateTime
    {
        return $this->_lastVisited;
    }
    
    public function setLastVisited(DateTime $lastVisited)
    {
        $this->_lastVisited = $lastVisited;
    }
    
    public function getNewComments() : int
    {
        return $this->newComments;
    }
    
    public function setNewComments(int $newComments)
    {
        $this->newComments = $newComments;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_USER_ID      => $this->userID,
            self::FIELD_ENTRY_ID     => $this->entryID,
            self::FIELD_LAST_VISITED => Util::dateTimeToString($this->_lastVisited),
            self::FIELD_NEW_COMMENTS => $this->newComments,
        );
    }
}
