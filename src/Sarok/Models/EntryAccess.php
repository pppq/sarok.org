<?php namespace Sarok\Models;

class EntryAccess {

    const FIELD_ENTRY_ID = 'entryID';
    const FIELD_USER_ID = 'userID';
    
    private int $entryID = 0;
    private int $userID = 0;

    public function __construct(int $entryID = 0, int $userID = 0) {
        $this->entryID = $entryID;
        $this->userID = $userID;
    }
    
    public function getEntryID() : int {
        return $this->entryID;
    }

    public function setEntryID(int $entryID) {
        $this->entryID = $entryID;
    }

    public function getUserID() : int {
        return $this->userID;
    }

    public function setUserID(int $userID) {
        $this->userID = $userID;
    }

    public function toArray() : array {
        return array(
            $this->entryID,
            $this->userID,
        );
    }
}
