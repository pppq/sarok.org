<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Models\Friend;
use Sarok\Models\User;
use Sarok\Service\DB;
use DateTime;

class UserRepository extends AbstractRepository
{
    const TABLE_NAME = 'users';
    const DATA_TABLE_NAME = 'userdata';
    
    private const COLUMN_NAMES = array(
        User::FIELD_ID,
        User::FIELD_LOGIN,
        User::FIELD_PASS,
        User::FIELD_CREATE_DATE,
        User::FIELD_LOGIN_DATE,
        User::FIELD_ACTIVATION_DATE,
        User::FIELD_IS_TERMINATED,
    );

    private FriendRepository $friendRepository;
    
    public function __construct(DB $db, FriendRepository $friendRepository)
    {
        parent::__construct($db);
        $this->friendRepository = $friendRepository;
    }
    
    protected function getTableName() : string
    {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array
    {
        return self::COLUMN_NAMES;
    }
    
    public function getActiveUserIdsQuery() : string
    {
        $userID = User::FIELD_USER_ID;
        $users = $this->getTableName();
        $activationDate = User::FIELD_ACTIVATION_DATE;
        
        return "SELECT DISTINCT `$userID` from `$users` WHERE `$activationDate` > ?";
    }

    public function getActiveRelatedUserIds(int $userID, DateTime $lastActivityAfter, string $friendType) : array
    {
        $userIDColumn = User::FIELD_USER_ID;
        $activeUserIdsQuery = $this->getActiveUserIdsQuery();
        $destinationIdsQuery = $this->friendRepository->getDestinationUserIdsQuery();
        
        $q = "$activeUserIdsQuery AND `$userIDColumn` IN ($destinationIdsQuery)";
        $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        $result = $this->db->query($q, 'sis', $lastActivityString, $userID, $friendType);
        
        $activeUserIDs = array();
        while ($row = $result->fetch_row()) {
            $activeUserIDs[] = $row[0];
        }
        
        return $activeUserIDs;
    }
    
    public function getFriendsActivity(int $userID, DateTime $lastActivityAfter = null) : array
    {
        $userIDColumn = User::FIELD_USER_ID;
        $login = User::FIELD_LOGIN;
        $activationDate = User::FIELD_ACTIVATION_DATE;
        $users = $this->getTableName();
        $destinationIdsQuery = $this->friendRepository->getDestinationUserIdsQuery();
        
        $q = "SELECT DISTINCT `$userIDColumn`, `$login`, `$activationDate` FROM `$users` " .
            "WHERE `$activationDate` > ? AND `$userIDColumn` IN ($destinationIdsQuery) " .
            "ORDER BY `$activationDate` DESC";
        
        if ($lastActivityAfter === null) {
            $lastAcitvityString = Util::zeroDateTime();
        } else {
            $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        }
        $friendType = Friend::TYPE_FRIEND;
        $result = $this->db->query($q, 'sis', $lastActivityString, $userID, $friendType);
        
        $friendsActivity = array();
        while ($row = $result->fetch_row()) {
            $friendsActivity[] = array(
                'userID' => $row[0],
                'login' => $row[1],
                'lastActivity' => Util::utcDateTimeFromString($row[2]),
            );
        }
        
        return $friendsActivity;
    }
    
    public function getActiveUsers(int $userID, DateTime $lastActivityAfter = null) : int
    {
        $ID = User::FIELD_ID;
        $users = $this->getTableName();
        $userIDColumn = User::FIELD_USER_ID;
        $activationDate = User::FIELD_ACTIVATION_DATE;
        
        $q = "SELECT COUNT(`$ID`) FROM `$users` WHERE `$userIDColumn` = ? AND `$activationDate` > ?";
        
        if ($lastActivityAfter === null) {
            $lastActivityString = Util::zeroDateTime();
        } else {
            $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        }
        
        $result = $this->db->execute($q, 'is', $userID, $lastActivityString);
        if ($userCount = $result->fetch_row()) {
            return $userCount[0];
        }
        
        return 0;
    }
    
    public function getUserIdIfActive(string $ID, DateTime $lastActivityAfter) : int
    {
        $userID = User::FIELD_USER_ID;
        $users = $this->getTableName();
        $IDColumn = User::FIELD_ID;
        $activationDate = User::FIELD_ACTIVATION_DATE;
        
        $q = "SELECT `$userID` FROM `$users` WHERE `$IDColumn` = ? AND `$activationDate` > ? LIMIT 1";
        $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        $result = $this->db->execute($q, 'ss', $ID, $lastActivityString);
        if ($row = $result->fetch_row()) {
            return (int) $row[0];
        }
        
        return 0;
    }
    
    public function getLoginsByPrefix(string $loginPrefix, int $limit = 10) : array
    {
        $login = User::FIELD_LOGIN;
        $users = $this->getTableName();
        
        $q = "SELECT `$login` FROM `$users` WHERE `$login` LIKE ? ORDER BY `$login` LIMIT ?";
        $loginPrefix .= '%';
        $result = $this->db->execute($q, "si", $loginPrefix, $limit);
        
        $logins = array();
        while ($row = $result->fetch_row()) {
            $logins[] = $row[0];
        }
        
        return $logins;
    }

    public function userExists(string $login) : bool
    {
        return $this->getUserID($login) > 0;
    }

    public function getUserID(string $login) : int
    {
        $id = User::FIELD_ID;
        $loginColumn = User::FIELD_LOGIN;
        $users = $this->getTableName();
        
        $q = "SELECT `$id` FROM `$users` WHERE `$loginColumn` = ? LIMIT 1";
        $result = $this->db->execute($q, "s", $login);
        if ($row = $result->fetch_row()) {
            return ((int) $row[0]);
        }
        
        return -1;
    }

    public function updateLoginDate(int $userID, DateTime $loginDate) : int
    {
        $users = $this->getTableName();
        $loginDate = User::FIELD_LOGIN_DATE;
        $ID = User::FIELD_ID;
        
        $q = "UPDATE `$users` SET `$loginDate` = ? WHERE `$ID` = ? LIMIT 1";
        $loginDateString = Util::dateTimeToString($loginDate);
        return $this->db->execute($q, "si", $loginDateString, $userID);
    }

    public function updateActivity(int $userID, DateTime $lastActivity) : int
    {
        $users = $this->getTableName();
        $activationDate = User::FIELD_ACTIVATION_DATE;
        $ID = User::FIELD_ID;
        
        $q = "UPDATE `$users` SET `$activationDate` = ? WHERE `$ID` = ? LIMIT 1";
        $lastActivityString = Util::dateTimeToString($lastActivity);
        return $this->db->execute($q, "si", $lastActivityString, $userID);
    }

    public function updateTerminated(int $userID, bool $isTerminated) : int
    {
        $users = $this->getTableName();
        $isTerminatedColumn = User::FIELD_IS_TERMINATED;
        $ID = User::FIELD_ID;
        
        $q = "UPDATE `$users` SET `$isTerminatedColumn` = ? WHERE `$ID` = ? LIMIT 1";
        $isTerminatedString = Util::boolToYesNo($isTerminated);
        return $this->db->execute($q, "si", $isTerminatedString, $userID);
    }
    
    public function updatePassword(int $userID, string $password) : int
    {
        $users = $this->getTableName();
        $passwordColumn = User::FIELD_PASS;
        $ID = User::FIELD_ID;
        
        $q = "UPDATE `$users` SET `$passwordColumn` = ? WHERE `$ID` = ?";
        return $this->db->execute($q, "ssssi", $password, $userID);
    }
    
    public function getUserData(string $userID) : array
    {
        $userdata = self::DATA_TABLE_NAME;
        $selectColumns = array(
            User::FIELD_NAME,
            User::FIELD_VALUE,
        );

        $columnList = $this->toColumnList($selectColumns);
        $userIDColumn = User::FIELD_USER_ID;
        $name = User::FIELD_NAME;

        $q = "SELECT (`$columnList`) FROM `$userdata` WHERE `$userIDColumn` = ? ORDER BY `$name`";
        $result = $this->db->execute($q, 'i', $userID);

        $properties = array();
        while ($row = $result->fetch_row()) {
            $properties[$row[0]] = $row[1];
        }

        return $properties;
    }

    public function updateUserData(int $userID, array $changedValues)
    {
        $userdata = self::DATA_TABLE_NAME;
        $insertColumns = array(
            User::FIELD_USER_ID,
            User::FIELD_NAME,
            User::FIELD_VALUE,
        );
        
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $valueColumn = User::FIELD_VALUE;
        
        $q = "INSERT INTO `$userdata` (`$columnList`) VALUES ($placeholderList) " .
            "ON DUPLICATE KEY UPDATE `$valueColumn` = ?";
        
        foreach ($changedValues as $key => $value) {
            // "value" appears twice due to the ON DUPLICATE KEY UPDATE
            $this->db->execute($q, 'isss', $userID, $key, $value, $value);
        }
    }

    public function getCitiesByPrefix(string $cityPrefix, int $limit = 10) : array
    {
        $value = User::FIELD_VALUE;
        $userdata = self::DATA_TABLE_NAME;
        $nameColumn = User::FIELD_NAME;

        $q = "SELECT DISTINCT `$value` FROM `$userdata` WHERE `$value` LIKE ? AND `$nameColumn` = ? ORDER BY `$value` LIMIT ?";
        $cityPrefix .= '%';
        $name = 'city';
        $result = $this->db->execute($q, "ssi", $cityPrefix, $name, $limit);

        $cities = array();
        while ($row = $result->fetch_row()) {
            $cities[] = $row[0];
        }

        return $cities;
    }

    public function getDiariesByPrefix(int $userID, string $userLogin, string $diaryPrefix, int $limit = 10) : array
    {
        // Returns the login names of users who allow the current user to create entries
        $loginColumn = User::FIELD_LOGIN;
        $users = self::TABLE_NAME;
        $ID = User::FIELD_ID;
        $userIDColumn = User::FIELD_USER_ID;
        $userdata = self::DATA_TABLE_NAME;
        $name = User::FIELD_NAME;
        $value = User::FIELD_VALUE;

        // "The user themselves" (duh)
        $ownAccessClause = "`$loginColumn` = ?";

        // "People who allow any registered user to write to their diary"
        $userAllowsAccess = "SELECT `$userIDColumn` FROM `$userdata` WHERE `$name` = ? AND `$value` = ?";
        $registeredAccessClause = "`$ID` IN ($userAllowsAccess)";

        // "People who made <current user> their friends and allow friends to write to their diary"
        $sourceUserIds = $this->friendRepository->getSourceUserIdsQuery();
        $friendAccessClause = "`$ID` IN ($userAllowsAccess AND `$userIDColumn` IN ($sourceUserIds))";

        $q = "SELECT `$loginColumn` FROM `$users` " .
            "WHERE `$loginColumn` LIKE ? AND ($ownAccessClause OR $registeredAccessClause OR $friendAccessClause) " .
            "ORDER BY `$loginColumn` LIMIT ?";

        $diaryPrefix .= '%';
        $blogAccess = 'blogAccess';
        $registered = 'registered';
        $friends = 'friends';
        $friendType = Friend::TYPE_FRIEND;

        $result = $this->db->execute($q, 'ssssssisi',
            $diaryPrefix,
            $userLogin,
            $blogAccess,
            $registered,
            $blogAccess,
            $friends,
            $userID,
            Friend::TYPE_FRIEND,
            $limit);

        $diaries = array();
        while ($row = $result->fetch_row()) {
            $diaries[] = $row[0];
        }

        return $diaries;
    }
    
    public function insert(User $user) : int
    {
        $users = $this->getTableName();
        $userArray = $user->toArray();
        $keys = array_keys($userArray);
        $columnList = $this->toColumnList($keys);
        $placeholderList = $this->toPlaceholderList($keys);
        
        $q = "INSERT INTO `$users`(`$columnList`) VALUES ($placeholderList)";
        
        $values = array_values($userArray);
        if ($user->getID() < 0) {
            // Need auto-generated ID - don't send in the negative value
            array_shift($columnList);
            array_shift($placeholderList);
            array_shift($values);
            
            $this->db->execute($q, 'ssssss', ...$values);
            $user->setID($this->db->getLastInsertID());
        } else {
            $this->db->execute($q, 'issssss', ...$values);
        }

        $this->updateUserData($user->flushUserData());
    }
}
