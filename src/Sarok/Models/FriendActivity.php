<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

class FriendActivity
{
    private int $userID = 0;
    private string $login = '';
    private DateTime $_activationDate;

    public function __construct()
    {
        if (!isset($this->_activationDate)) {
            $this->_activationDate = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, $value)
    {
        if ($name === 'activationDate' && is_string($value)) {
            $this->setActivationDate(Util::utcDateTimeFromString($value));
        }
    }

    public function getUserID() : int
    {
        return $this->userID;
    }

    public function getLogin() : string
    {
        return $this->login;
    }

    public function getActivationDate() : DateTime
    {
        return $this->_activationDate;
    }
}
