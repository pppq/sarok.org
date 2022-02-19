<?php namespace Sarok\Models;

class FriendType
{
    const FRIEND = 'friend';
    const BANNED = 'banned';
    const READER = 'banned';
};

class Friend {

    const FIELD_FRIEND_OF = 'friendOf';
    const FIELD_USER_ID = 'userID';
    const FIELD_FRIEND_TYPE = 'friendType';
    
    private int $friendOf = 0;
    private int $userID = 0;
    private string $friendType = FriendType::FRIEND;

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
            self::FIELD_FRIEND_OF   => $this->friendOf,
            self::FIELD_USER_ID     => $this->userID,
            self::FIELD_FRIEND_TYPE => $this->friendType,
        );
    }
}
