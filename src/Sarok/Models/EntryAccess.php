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

    public static function create(int $entryID, int $userID) : EntryAccess
    {
        $entryAccess = new EntryAccess();
        $entryAccess->entryID = $entryID;
        $entryAccess->userID = $userID;
        return $entryAccess;
    }
    
    public function getEntryID() : int
    {
        return $this->entryID;
    }

    public function getUserID() : int
    {
        return $this->userID;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_ENTRY_ID => $this->entryID,
            self::FIELD_USER_ID  => $this->userID,
        );
    }
}
