<?php declare(strict_types=1);

namespace Sarok\Models;

use Sarok\Util;
use DateTime;

/**
 * Captures a user's identifier, login name and last activity date for user lists.
 * 
 * This object can be obtained by querying either `sessions` or `users`, but in the latter case
 * the SELECT statement needs to retrieve column `ID` as `userID`:
 * 
 * ```sql
 * `userID`         int(10) unsigned NOT NULL DEFAULT '0',
 * `login`          char(30)         NOT NULL DEFAULT '',
 * `activationDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * ```
 */
class FriendActivity
{
    private int      $userID = 0;
    private string   $login  = '';
    private DateTime $_activationDate;

    public static function create(int $userID, string $login, DateTime $activationDate) : FriendActivity
    {
        $friendActivity = new FriendActivity();
        $friendActivity->userID = $userID;
        $friendActivity->login = $login;
        $friendActivity->_activationDate = $activationDate;
        return $friendActivity;
    }

    public function __construct()
    {
        if (!isset($this->_activationDate)) {
            $this->_activationDate = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, mixed $value) : void
    {
        if (Session::FIELD_ACTIVATION_DATE === $name && is_string($value)) {
            $this->_activationDate = Util::utcDateTimeFromString($value);
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
