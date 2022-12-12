<?php declare(strict_types=1);

namespace Sarok\Models;

use Sarok\Util;
use DateTime;

/**
 * Represents a single browser session.
 *
 * Table structure for `sessions`:
 *
 * ```sql
 * `ID`             bigint(15)       NOT NULL DEFAULT '0',
 * `userID`         int(10) unsigned NOT NULL DEFAULT '0',
 * `createDate`     datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `loginDate`      datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `activationDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `IP`             char(100)        NOT NULL DEFAULT '',
 * ```
 */
class Session
{
    const FIELD_ID              = 'ID';
    const FIELD_USER_ID         = 'userID';
    const FIELD_CREATE_DATE     = 'createDate';
    const FIELD_LOGIN_DATE      = 'loginDate';
    const FIELD_ACTIVATION_DATE = 'activationDate';
    const FIELD_IP              = 'IP';
    
    private int $ID                    = 0;
    private int $userID                = 0;
    private DateTime $_createDate;
    private DateTime $_loginDate;
    private DateTime $_activationDate;
    private string $IP                 = '';
    
    public function __construct()
    {
        // MySQL's default values are "zero date", but "now" is more appropriate
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::utcDateTimeFromString();
        }
        
        if (!isset($this->_loginDate)) {
            $this->_loginDate = Util::utcDateTimeFromString();
        }
        
        if (!isset($this->_activationDate)) {
            $this->_activationDate = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, mixed $value) : void
    {
        if (self::FIELD_CREATE_DATE === $name && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_LOGIN_DATE === $name && is_string($value)) {
            $this->setLoginDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_ACTIVATION_DATE === $name && is_string($value)) {
            $this->setActivationDate(Util::utcDateTimeFromString($value));
        }
    }
    
    public function getID() : int
    {
        return $this->ID;
    }
    
    public function setID(int $ID) : void
    {
        $this->ID = $ID;
    }
    
    public function getUserID() : int
    {
        return $this->userID;
    }
    
    public function setUserID(int $userID) : void
    {
        $this->userID = $userID;
    }
    
    public function getCreateDate() : DateTime
    {
        return $this->_createDate;
    }

    public function setCreateDate(DateTime $createDate) : void
    {
        $this->_createDate = $createDate;
    }

    public function getLoginDate() : DateTime
    {
        return $this->_loginDate;
    }

    public function setLoginDate(DateTime $loginDate) : void
    {
        $this->_loginDate = $loginDate;
    }

    public function getActivationDate() : DateTime
    {
        return $this->_activationDate;
    }

    public function setActivationDate(DateTime $activationDate) : void
    {
        $this->_activationDate = $activationDate;
    }

    public function getIP() : string
    {
        return $this->IP;
    }

    public function setIP(string $IP) : void
    {
        $this->IP = $IP;
    }
    
    public function toArray() : array
    {
        return array(
            self::FIELD_ID              => $this->ID,
            self::FIELD_USER_ID         => $this->userID,
            self::FIELD_CREATE_DATE     => Util::dateTimeToString($this->_createDate),
            self::FIELD_LOGIN_DATE      => Util::dateTimeToString($this->_loginDate),
            self::FIELD_ACTIVATION_DATE => Util::dateTimeToString($this->_activationDate),
            self::FIELD_IP              => $this->IP,
        );
    }
}
