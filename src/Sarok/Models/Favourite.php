<?php declare(strict_types=1);

namespace Sarok\Models;

use DateTime;
use Sarok\Util;

/**
 * Represents a bookmarked entry. It stores information about the bookmarking user's last visit
 * as well as any comments the entry received in the meantime.
 * 
 * Table structure for `favourites`:
 * 
 * ```sql
 * `userID`      int(10) unsigned NOT NULL DEFAULT '0',
 * `entryID`     int(10) unsigned NOT NULL DEFAULT '0',
 * `lastVisited` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `newComments` int(10) unsigned NOT NULL DEFAULT '0',
 * ```
 */
class Favourite
{
    const FIELD_USER_ID      = 'userID';
    const FIELD_ENTRY_ID     = 'entryID';
    const FIELD_LAST_VISITED = 'lastVisited';
    const FIELD_NEW_COMMENTS = 'newComments';
    
    private int      $userID        = 0;
    private int      $entryID       = 0;
    private DateTime $_lastVisited;
    private int      $newComments   = 0;

    public function __construct()
    {
        if (!isset($this->_lastVisited)) {
            $this->_lastVisited = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, $value) : void
    {
        if (self::FIELD_LAST_VISITED === $name && is_string($value)) {
            $this->setLastVisited(Util::utcDateTimeFromString($value));
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
    
    public function getEntryID() : int
    {
        return $this->entryID;
    }

    public function setEntryID(int $entryID) : void
    {
        $this->entryID = $entryID;
    }
    
    public function getLastVisited() : DateTime
    {
        return $this->_lastVisited;
    }
    
    public function setLastVisited(DateTime $lastVisited) : void
    {
        $this->_lastVisited = $lastVisited;
    }
    
    public function getNewComments() : int
    {
        return $this->newComments;
    }
    
    public function setNewComments(int $newComments) : void
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
