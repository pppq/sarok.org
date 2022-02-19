<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

/*
 * Table structure for `users`:
 *
 * `ID`             int(6) unsigned  NOT NULL AUTO_INCREMENT,
 * `login`          char(30)         NOT NULL DEFAULT '',
 * `pass`           char(42)         NOT NULL DEFAULT 'this-is-not-a-valid-password-hash',
 * `createDate`     datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `loginDate`      datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `activationDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `isTerminated`   enum('Y','N')    NOT NULL DEFAULT 'N',
 *
 * Table structure for `userdata`:
 *
 * `userID`         int(10) unsigned NOT NULL DEFAULT '0',
 * `name`           varchar(60)      NOT NULL DEFAULT '',
 * `value`          longtext         NOT NULL DEFAULT '',
 */
class User
{
    // "users" fields
    const FIELD_ID = 'ID';
    const FIELD_LOGIN = 'login';
    const FIELD_PASS = 'pass';
    const FIELD_CREATE_DATE = 'createDate';
    const FIELD_LOGIN_DATE = 'loginDate';
    const FIELD_ACTIVATION_DATE = 'activationDate';
    const FIELD_IS_TERMINATED = 'isTerminated';
    
    // "userdata" fields
    const FIELD_USER_ID = 'userID';
    const FIELD_NAME = 'name';
    const FIELD_VALUE = 'value';

    // "userdata" keys
    const KEY_BIRTH_YEAR = 'birthYear';
    const KEY_CITY = 'city';
    const KEY_COUNTRY = 'country';
    const KEY_DISTRICT = 'district';
    const KEY_EMAIL = 'email';
    const KEY_EYE_COLOR = 'eyeColor';
    const KEY_HAIR_COLOR = 'hairColor';
    const KEY_ICQ = 'ICQ';
    const KEY_MSN = 'MSN';
    const KEY_SKYPE = 'skype';
    const KEY_NAME = 'name';
    const KEY_KEYWORDS = 'keywords';
    const KEY_OCCUPATION = 'occupation';
    const KEY_PHONE = 'phone';
    const KEY_PUBLIC_INFO = 'publicInfo';
    const KEY_SEX = 'sex';
    const KEY_STATE = 'state';
    const KEY_WIW = 'WIW';
    const KEY_BIRTHDATE = 'birthDate'; // MM/DD format
    const KEY_DESCRIPTION = 'description';

    const KEY_BLOG_ACCESS = 'blogAccess';
    const KEY_BLOG_NAME = 'blogName';
    const KEY_COMMENT_ACCESS = 'commentAccess';
    const KEY_COPYRIGHT = 'copyright';
    const KEY_GOOGLE = 'google';
    const KEY_MESSAGE_ACCESS = 'messageAccess';
    const KEY_STATISTICS = 'statistics';
    const KEY_ENTRIES_PER_PAGE = 'entriesPerPage';
    const KEY_BLOG_TEXT = 'blogText';
    const KEY_COPYRIGHT_TEXT = 'copyrightText';

    const KEY_COMMENTS_LOADED = 'commentsLoaded';
    const KEY_ENTRIES_LOADED = 'entriesLoaded';
    const KEY_COMMENTS_OF_ENTRIES_LOADED = 'commentsOfEntriesLoaded';
    const KEY_MY_COMMENTS_LOADED = 'myCommentsLoaded';

    const KEY_TO_MAIN_PAGE = 'toMainPage';
    const KEY_FRIEND_LIST_ONLY = 'friendListOnly';
    const KEY_WYSIWYG = 'wysiwyg';
    const KEY_RSS = 'rss';
    const KEY_CSS = 'css';
    const KEY_SKIN_NAME = 'skinName';
    const KEY_BIND_TO_MAP = 'bindToMap';
    const KEY_POS_X = 'posX';
    const KEY_POS_Y = 'posY';

    // Assignment requires conversion via magic method (__set)
    private DateTime $_createDate;
    private DateTime $_loginDate;
    private DateTime $_activationDate;
    private bool $_isTerminated;
    
    // Assignment from string directly supported
    private int $ID;
    private string $login;
    private string $pass;
    
    // User data as associative array
    private array $userData;
    private array $unsavedKeys;
    
    public function __construct()
    {
        // Default values are "zero date" in the DB schema for all three date columns,
        // but current time is more appropriate for the creation date
        if (!isset($this->_createDate)) {
            $this->_createDate = Util::utcDateTimeFromString();
        }
        
        if (!isset($this->_loginDate)) {
            $this->_loginDate = Util::zeroDateTime();
        }
        
        if (!isset($this->_activationDate)) {
            $this->_activationDate = Util::zeroDateTime();
        }
        
        if (!isset($this->_isTerminated)) {
            $this->_isTerminated = false;
        }
        
        $this->ID = -1;
        $this->login = '';
        $this->pass = '';
        $this->userData = array();
        $this->unsavedKeys = array();
    }
    
    public function __set(string $name, $value)
    {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_CREATE_DATE && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === self::FIELD_LOGIN_DATE && is_string($value)) {
            $this->setLoginDate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === self::FIELD_ACTIVATION_DATE && is_string($value)) {
            $this->setActivationDate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === self::FIELD_IS_TERMINATED && is_string($value)) {
            $this->setTerminated(Util::yesNoToBool($value));
        }
    }
    
    public function getID() : int
    {
        return $this->ID;
    }
    
    public function setID(int $ID)
    {
        $this->ID = $ID;
    }
    
    public function getLogin() : string
    {
        return $this->login;
    }
    
    public function setLogin(string $login)
    {
        $this->login = $login;
    }
    
    public function getPass() : string
    {
        return $this->pass;
    }
    
    public function setPass(string $pass)
    {
        $this->pass = $pass;
    }
    
    public function getCreateDate() : DateTime
    {
        return $this->_createDate;
    }

    public function setCreateDate(DateTime $createDate)
    {
        $this->_createDate = $createDate;
    }

    public function getLoginDate() : DateTime
    {
        return $this->_loginDate;
    }

    public function setLoginDate(DateTime $loginDate)
    {
        $this->_loginDate = $loginDate;
    }

    public function getActivationDate() : DateTime
    {
        return $this->_activationDate;
    }

    public function setActivationDate(DateTime $activationDate)
    {
        $this->_activationDate = $activationDate;
    }

    public function isTerminated() : bool
    {
        return $this->_isTerminated;
    }

    public function setTerminated(bool $isTerminated)
    {
        $this->_isTerminated = $isTerminated;
    }
    
    public function resetUserData(array $userData = array())
    {
        $this->userData = $userData;
        $this->unsavedKeys = array();
    }
    
    public function setUserData(string $key, string $value) : string
    {
        // Returns the previous value if one was already set
        if (!isset($this->userData[$key])) {
            $oldValue = '';
        } else {
            $oldValue = $this->userData[$key];
        }
        
        if ($value !== $oldValue) {
            $this->userData[$key] = $value;
            $this->unsavedKeys[$key] = true;
        }
    
        return $oldValue;
    }
    
    public function getUserData(string $key) : string
    {
        if (!isset($this->userData[$key])) {
            return '';
        } else {
            return $this->userData[$key];
        }
    }
    
    public function flushUserData() : array
    {
        // Returns key-value pairs with unsaved content and clears unsaved keys
        $unsavedData = array_replace($this->unsavedKeys, $this->userData);
        $this->unsavedKeys = array();
        return $unsavedData;
    }
    
    public function toArray() : array
    {
        return array(
            self::FIELD_ID              => $this->ID,
            self::FIELD_LOGIN           => $this->login,
            self::FIELD_PASS            => $this->pass,
            self::FIELD_CREATE_DATE     => Util::dateTimeToString($this->_createDate),
            self::FIELD_LOGIN_DATE      => Util::dateTimeToString($this->_loginDate),
            self::FIELD_ACTIVATION_DATE => Util::dateTimeToString($this->_activationDate),
            self::FIELD_IS_TERMINATED   => Util::boolToYesNo($this->_isTerminated),
        );
    }
}
