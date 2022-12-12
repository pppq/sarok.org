<?php declare(strict_types=1);

namespace Sarok\Models;

use Sarok\Util;
use DateTime;

/**
 * Represents a single user (author and/or commenter), their many properties that are
 * either displayed on the profile page, and their settings control various aspects 
 * of their experience on the site.
 * 
 * Table structure for `users` and `userdata`:
 *
 * ```sql
 * `ID`             int(6) unsigned  NOT NULL AUTO_INCREMENT,
 * `login`          char(30)         NOT NULL DEFAULT '',
 * `pass`           char(42)         NOT NULL DEFAULT 'this-is-not-a-valid-password-hash',
 * `createDate`     datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `loginDate`      datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `activationDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `isTerminated`   enum('Y','N')    NOT NULL DEFAULT 'N',
 * 
 * -----8<-------8<-------8<----------------
 * 
 * `userID`         int(10) unsigned NOT NULL DEFAULT '0',
 * `name`           varchar(60)      NOT NULL DEFAULT '',
 * `value`          longtext         NOT NULL DEFAULT '',
 * ```
 */
class User
{
    // "users" fields
    const FIELD_ID              = 'ID';
    const FIELD_LOGIN           = 'login';
    const FIELD_PASS            = 'pass';
    const FIELD_CREATE_DATE     = 'createDate';
    const FIELD_LOGIN_DATE      = 'loginDate';
    const FIELD_ACTIVATION_DATE = 'activationDate';
    const FIELD_IS_TERMINATED   = 'isTerminated';
    
    // "userdata" fields
    const FIELD_USER_ID = 'userID';
    const FIELD_NAME    = 'name';
    const FIELD_VALUE   = 'value';

    // "userdata" keys for the user profile page
    const KEY_BIRTH_YEAR  = 'birthYear';
    const KEY_CITY        = 'city';
    const KEY_COUNTRY     = 'country';
    const KEY_DISTRICT    = 'district';
    const KEY_EMAIL       = 'email';
    const KEY_EYE_COLOR   = 'eyeColor';
    const KEY_HAIR_COLOR  = 'hairColor';
    const KEY_ICQ         = 'ICQ';
    const KEY_MSN         = 'MSN';
    const KEY_SKYPE       = 'skype';
    const KEY_NAME        = 'name';
    const KEY_KEYWORDS    = 'keywords';
    const KEY_OCCUPATION  = 'occupation';
    const KEY_PHONE       = 'phone';
    const KEY_PUBLIC_INFO = 'publicInfo';
    const KEY_SEX         = 'sex';
    const KEY_STATE       = 'state';
    const KEY_WIW         = 'WIW';
    const KEY_BIRTHDATE   = 'birthDate'; // format is "MM/DD"
    const KEY_DESCRIPTION = 'description';

    // "userdata" keys for blog settings
    const KEY_BLOG_ACCESS      = 'blogAccess';
    const KEY_BLOG_NAME        = 'blogName';
    const KEY_COMMENT_ACCESS   = 'commentAccess';
    const KEY_COPYRIGHT        = 'copyright';
    const KEY_GOOGLE           = 'google';
    const KEY_MESSAGE_ACCESS   = 'messageAccess';
    const KEY_STATISTICS       = 'statistics';
    const KEY_ENTRIES_PER_PAGE = 'entriesPerPage';
    const KEY_BLOG_TEXT        = 'blogText';
    const KEY_COPYRIGHT_TEXT   = 'copyrightText';
    const KEY_BACKUP           = 'backup';

    // "userdata" keys for dashboard status
    const KEY_COMMENTS_LOADED            = 'commentsLoaded';
    const KEY_ENTRIES_LOADED             = 'entriesLoaded';
    const KEY_COMMENTS_OF_ENTRIES_LOADED = 'commentsOfEntriesLoaded';
    const KEY_MY_COMMENTS_LOADED         = 'myCommentsLoaded';
    const KEY_NEW_MAIL                   = 'newMail';

    // "userdata" keys not yet categorized (can be any of the above or miscellaneous settings)
    const KEY_TO_MAIN_PAGE     = 'toMainPage';
    const KEY_FRIEND_LIST_ONLY = 'friendListOnly';
    const KEY_TRACK_ME         = 'trackMe';
    const KEY_WYSIWYG          = 'wysiwyg';
    const KEY_RSS              = 'rss';
    const KEY_RSS_SECRET       = 'rssSecret';
    const KEY_CSS              = 'css';
    const KEY_SKIN_NAME        = 'skinName';
    const KEY_BIND_TO_MAP      = 'bindToMap';
    const KEY_POS_X            = 'posX';
    const KEY_POS_Y            = 'posY';

    // Special user IDs
    const ID_ANONYMOUS = 1;

    // Special user logins
    const LOGIN_ALL = 'all';

    private int      $ID               = -1;
    private string   $login            = '';
    private string   $pass             = 'this-is-not-a-valid-password-hash';
    private DateTime $_createDate;
    private DateTime $_loginDate;
    private DateTime $_activationDate;
    private bool     $_isTerminated    = false;
    
    /** 
     * User properties stored as an associative array
     * @var array<string, string>
     */
    private array $userData = array();

    /**
     * Keys that will need updating on next save
     * @var array<string>
     */
    private array $unsavedKeys = array();
    
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
    }
    
    public function __set(string $name, mixed $value) : void
    {
        // Support conversion from string for fetch_object()
        if (self::FIELD_CREATE_DATE === $name && is_string($value)) {
            $this->setCreateDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_LOGIN_DATE === $name && is_string($value)) {
            $this->setLoginDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_ACTIVATION_DATE === $name && is_string($value)) {
            $this->setActivationDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_IS_TERMINATED === $name && is_string($value)) {
            $this->setTerminated(Util::yesNoToBool($value));
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
    
    public function getLogin() : string
    {
        return $this->login;
    }
    
    public function setLogin(string $login) : void
    {
        $this->login = $login;
    }
    
    public function getPass() : string
    {
        return $this->pass;
    }
    
    public function setPass(string $pass) : void
    {
        $this->pass = $pass;
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

    public function isTerminated() : bool
    {
        return $this->_isTerminated;
    }

    public function setTerminated(bool $isTerminated) : void
    {
        $this->_isTerminated = $isTerminated;
    }
    
    /**
     * Resets unsaved keys to an empty array and sets all user data to match the given associative array.
     * 
     * @param array<string, string> $userData
     */
    public function putAllUserData(array $userData = array()) : void
    {
        $this->userData = $userData;
        $this->unsavedKeys = array();
    }
    
    /**
     * Sets a single user data property to the specified value, and marks the property as unsaved if the
     * new value differs from the earlier one.
     * 
     * @param string $key the user property key
     * @param string $value the user property value
     * @return string the previously set value, or an empty string if no value was set earlier
     */
    public function putUserData(string $key, string $value) : string
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
    
    /**
     * Retrieves a user data property by key, or uses the provided default value if the property
     * is not set.
     * 
     * @param string $key the key of the user property to retrieve
     * @param string $defaultValue the default value to use
     * @return string the property value, or the provided default value if no value was set earlier
     */
    public function getUserData(string $key, string $defaultValue = '') : string
    {
        if (!isset($this->userData[$key])) {
            return $defaultValue;
        } else {
            return $this->userData[$key];
        }
    }
    
    /**
     * Returns user property keys with the currently set values for keys that were marked as unsaved,
     * then clears unsaved keys.
     * 
     * @return array<string, string> the property key-value pairs that should be updated
     */
    public function flushUserData() : array
    {
        $unsavedData = array_intersect_key($this->unsavedKeys, $this->userData);
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
