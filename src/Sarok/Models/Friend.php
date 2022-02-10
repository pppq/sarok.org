<?php namespace Sarok\Models;

class Friend {

    const TYPE_FRIEND = 'friend';
    const TYPE_BANNED = 'banned';
    const TYPE_READER = 'banned';
    
    const FIELD_FRIEND_OF = 'friendOf';
    const FIELD_USER_ID = 'userID';
    const FIELD_FRIEND_TYPE = 'friendType';
    
    private int $friendOf = 0;
    private int $userID = 0;
    private string $friendType = self::TYPE_FRIEND;

    public function getFriendOf() : int {
        return $this->friendOf;
    }

    public function setFriendOf(int $friendOf) {
        $this->friendOf = $friendOf;
    }

    public function getUserID() : int {
        return $this->userID;
    }

    public function setUserID(int $userID) {
        $this->userID = $userID;
    }

    public function getFriendType() : string {
        return $this->friendType;
    }

    public function setFriendType(string $friendType) {
        $this->friendType = $friendType;
    }

    public function toArray() : array {
        return array(
            $this->sourceID,
            $this->destinationID,
            $this->friendType,
        );
    }
}
