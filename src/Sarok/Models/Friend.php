<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Describes relationships between users (readers, friends, banned users).
 * 
 * Table structure for `friends`:
 *
 * ```sql
 * `friendOf`   int(10) unsigned NOT NULL DEFAULT '0',
 * `userID`     int(10) unsigned NOT NULL DEFAULT '0',
 * `friendType` enum('friend','banned','read') NOT NULL DEFAULT 'friend',
 * ```
 */
class Friend
{
    const FIELD_FRIEND_OF   = 'friendOf';
    const FIELD_USER_ID     = 'userID';
    const FIELD_FRIEND_TYPE = 'friendType';
    
    private int        $friendOf   = 0;
    private int        $userID     = 0;
    private FriendType $friendType = FriendType::FRIEND;

    public static function create(int $friendOf, int $userID, FriendType $friendType = FriendType::FRIEND) : Friend
    {
        $friend = new Friend();
        $friend->friendOf = $friendOf;
        $friend->userID = $userID;
        $friend->friendType = $friendType;
        return $friend;
    }

    public function getFriendOf() : int
    {
        return $this->friendOf;
    }

    public function getUserID() : int
    {
        return $this->userID;
    }

    public function getFriendType() : FriendType
    {
        return $this->friendType;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_FRIEND_OF   => $this->friendOf,
            self::FIELD_USER_ID     => $this->userID,
            self::FIELD_FRIEND_TYPE => $this->friendType->value,
        );
    }
}
