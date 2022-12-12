<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Stores information about users who are allowed to access a particular entry (access type
 * is usually set to "list" in this case).
 *
 * Table structure for `entryaccess`:
 * 
 * ```sql
 * `entryID` int(10) unsigned NOT NULL DEFAULT '0',
 * `userID`  int(10) unsigned NOT NULL DEFAULT '0',
 * ```
 */
class EntryAccess
{
    const FIELD_ENTRY_ID = 'entryID';
    const FIELD_USER_ID  = 'userID';
    
    private int $entryID = 0;
    private int $userID  = 0;

    public function __construct(int $entryID, int $userID)
    {
        $this->entryID = $entryID;
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

    public function getUserID() : int
    {
        return $this->userID;
    }

    public function setUserID(int $userID) : void
    {
        $this->userID = $userID;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_ENTRY_ID => $this->entryID,
            self::FIELD_USER_ID  => $this->userID,
        );
    }
}
