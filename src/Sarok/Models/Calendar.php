<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Represents a calendar day for a user (and their diary), summarizing the number of 
 * entries and messages created.
 * 
 * Table structure for `calendar`:
 * 
 * ```sql
 * `userID`           int(10) unsigned NOT NULL DEFAULT '0',
 * `y`                int(10) unsigned NOT NULL DEFAULT '0',
 * `m`                int(10) unsigned NOT NULL DEFAULT '0',
 * `d`                int(10) unsigned NOT NULL DEFAULT '0',
 * `numPublic`        int(10) unsigned NOT NULL DEFAULT '0',
 * `numRegistered`    int(10) unsigned NOT NULL DEFAULT '0',
 * `numFriends`       int(10) unsigned NOT NULL DEFAULT '0',
 * `numAll`           int(10) unsigned NOT NULL DEFAULT '0',
 * `numMailsReceived` int(10) unsigned NOT NULL DEFAULT '0',
 * `numMailsSent`     int(10) unsigned NOT NULL DEFAULT '0',
 * ```
 */
class Calendar
{
    const FIELD_USER_ID            = 'userID';
    const FIELD_Y                  = 'y';
    const FIELD_M                  = 'm';
    const FIELD_D                  = 'd';
    const FIELD_NUM_PUBLIC         = 'numPublic';
    const FIELD_NUM_REGISTERED     = 'numRegistered';
    const FIELD_NUM_FRIENDS        = 'numFriends';
    const FIELD_NUM_ALL            = 'numAll';
    const FIELD_NUM_MAILS_RECEIVED = 'numMailsReceived';
    const FIELD_NUM_MAILS_SENT     = 'numMailsSent';

    private int $userID           = 0;
    private int $y                = 0;
    private int $m                = 0;
    private int $d                = 0;
    private int $numPublic        = 0;
    private int $numRegistered    = 0;
    private int $numFriends       = 0;
    private int $numAll           = 0;
    private int $numMailsReceived = 0;
    private int $numMailsSent     = 0;
    
    public function getUserID() : int
    {
        return $this->userID;
    }

    public function setUserID(int $userID)
    {
        $this->userID = $userID;
    }

    public function getY() : int
    {
        return $this->y;
    }

    public function setY(int $y) : void
    {
        $this->y = $y;
    }

    public function getM() : int
    {
        return $this->m;
    }

    public function setM(int $m) : void
    {
        $this->m = $m;
    }

    public function getD() : int
    {
        return $this->d;
    }

    public function setD(int $d) : void
    {
        $this->d = $d;
    }

    public function getNumPublic() : int
    {
        return $this->numPublic;
    }

    public function setNumPublic(int $numPublic) : void
    {
        $this->numPublic = $numPublic;
    }

    public function getNumRegistered() : int
    {
        return $this->numRegistered;
    }

    public function setNumRegistered(int $numRegistered) : void
    {
        $this->numRegistered = $numRegistered;
    }

    public function getNumFriends() : int
    {
        return $this->numFriends;
    }

    public function setNumFriends(int $numFriends) : void
    {
        $this->numFriends = $numFriends;
    }

    public function getNumAll() : int
    {
        return $this->numAll;
    }

    public function setNumAll(int $numAll) : void
    {
        $this->numAll = $numAll;
    }

    public function getNumMailsReceived() : int
    {
        return $this->numMailsReceived;
    }

    public function setNumMailsReceived(int $numMailsReceived) : void
    {
        $this->numMailsReceived = $numMailsReceived;
    }

    public function getNumMailsSent() : int
    {
        return $this->numMailsSent;
    }

    public function setNumMailsSent(int $numMailsSent) : void
    {
        $this->numMailsSent = $numMailsSent;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_USER_ID            => $this->userID,
            self::FIELD_Y                  => $this->y,
            self::FIELD_M                  => $this->m,
            self::FIELD_D                  => $this->d,
            self::FIELD_NUM_PUBLIC         => $this->numPublic,
            self::FIELD_NUM_REGISTERED     => $this->numRegistered,
            self::FIELD_NUM_FRIENDS        => $this->numFriends,
            self::FIELD_NUM_ALL            => $this->numAll,
            self::FIELD_NUM_MAILS_RECEIVED => $this->numMailsReceived,
            self::FIELD_NUM_MAILS_SENT     => $this->numMailsSent,
        );
    }
}
