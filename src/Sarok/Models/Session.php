<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

class Session {

    const FIELD_ID = 'ID';
    const FIELD_USER_ID = 'userID';
    const FIELD_CREATE_DATE = 'createDate';
    const FIELD_LOGIN_DATE = 'loginDate';
    const FIELD_ACTIVATION_DATE = 'activationDate';
    const FIELD_IP = 'IP';
    
    // Assignment requires conversion via magic method (__set)
    private DateTime $_createDate;
    private DateTime $_loginDate;
    private DateTime $_activationDate;
    
    // Assignment from string directly supported
    private string $ID;
    private int $userID;
    private string $IP;
    
    public function __construct() {
        // Default values are "zero date", but "now" is more appropriate
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
    
    public function __set(string $name, $value) {
        // Support conversion from string for fetch_object()
        if ($name === Feed::FIELD_CREATE_DATE && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === Feed::FIELD_LOGIN_DATE && is_string($value)) {
            $this->setLoginDate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === Feed::FIELD_ACTIVATION_DATE && is_string($value)) {
            $this->setActivationDate(Util::utcDateTimeFromString($value));
        }
    }
    
    public function getID() : string {
        return $this->ID;
    }
    
    public function setID(string $ID) {
        $this->ID = $ID;
    }
    
    public function getUserID() : int {
        return $this->userID;
    }
    
    public function setUserID(int $userID) {
        $this->userID = $userID;
    }
    
    public function getCreateDate() : DateTime {
        return $this->_createDate;
    }

    public function setCreateDate(DateTime $createDate) {
        $this->_createDate = $createDate;
    }

    public function getLoginDate() : DateTime {
        return $this->_loginDate;
    }

    public function setLoginDate(DateTime $loginDate) {
        $this->_loginDate = $loginDate;
    }

    public function getActivationDate() : DateTime {
        return $this->_activationDate;
    }

    public function setActivationDate(DateTime $activationDate) {
        $this->_activationDate = $activationDate;
    }

    public function getIP() : string {
        return $this->IP;
    }

    public function setIP(string $IP) {
        $this->IP = $IP;
    }
    
    public function toArray() : array {
        return array(
            $this->ID,
            $this->userID,
            Util::dateTimeToString($this->_createDate),
            Util::dateTimeToString($this->_loginDate),
            Util::dateTimeToString($this->_activationDate),
            $this->IP,
        );
    }
}
