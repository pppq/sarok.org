<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\DB;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\Repository;
use Sarok\Models\User;
use Sarok\Models\FriendType;
use Sarok\Models\FriendActivity;
use Sarok\Models\AccessType;
use DateTime;

final class UserRepository extends Repository
{
    public const USER_TABLE_NAME = 'users';
    public const DATA_TABLE_NAME = 'userdata';
    
    public const COLUMN_NAMES = array(
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
    
    public function getActiveUserIdsQuery() : string
    {
        $c_userID = User::FIELD_USER_ID;
        $t_users = self::USER_TABLE_NAME;
        $c_activationDate = User::FIELD_ACTIVATION_DATE;
        
        return "SELECT DISTINCT `${c_userID}` from `${t_users}` WHERE `${c_activationDate}` > ?";
    }

    public function getActiveRelatedUserIds(int $userID, DateTime $lastActivityAfter, FriendType $friendType) : array
    {
        $c_userID = User::FIELD_USER_ID;
        $activeUserIdsQuery = $this->getActiveUserIdsQuery();
        $destinationIdsQuery = $this->friendRepository->getDestinationUserIdsQuery();
        
        $q = "${activeUserIdsQuery} AND `${c_userID}` IN (${destinationIdsQuery})";

        return $this->db->queryArray($q, 'sis', 
            Util::dateTimeToString($lastActivityAfter), $userID, $friendType->value);
    }
    
    public function getFriendsActivity(int $userID, DateTime $lastActivityAfter = null) : array
    {
        $c_ID = User::FIELD_ID;
        $c_login = User::FIELD_LOGIN;
        $c_activationDate = User::FIELD_ACTIVATION_DATE;
        $t_users = self::USER_TABLE_NAME;
        $destinationIdsQuery = $this->friendRepository->getDestinationUserIdsQuery();
        
        $q = "SELECT DISTINCT `${c_ID}`, `${c_login}`, `${c_activationDate}` FROM `${t_users}` " .
            "WHERE `${c_activationDate}` > ? AND `${c_ID}` IN (${destinationIdsQuery}) " .
            "ORDER BY `${c_activationDate}` DESC";
        
        if ($lastActivityAfter === null) {
            $lastActivityString = Util::zeroDateTime();
        } else {
            $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        }

        return $this->db->queryObjects($q, FriendActivity::class, 'sis', 
            $lastActivityString, $userID, FriendType::FRIEND->value);
    }
    
    // FIXME: this query makes no sense! "userId" is a userdata field.
    public function getActiveUsers(int $userID, DateTime $lastActivityAfter = null) : int
    {
        $c_ID = User::FIELD_ID;
        $t_users = self::USER_TABLE_NAME;
        $c_userID = User::FIELD_USER_ID;
        $c_activationDate = User::FIELD_ACTIVATION_DATE;
        
        $q = "SELECT COUNT(`${c_ID}`) FROM `${t_users}` " . 
            "WHERE `${c_userID}` = ? AND `${c_activationDate}` > ?";
        
        if ($lastActivityAfter === null) {
            $lastActivityString = Util::zeroDateTime();
        } else {
            $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        }
        
        return $this->db->queryInt($q, 0, 'is', 
            $userID, $lastActivityString);
    }
    
    // FIXME: ...and this query makes no sense either.
    public function getUserIdIfActive(string $ID, DateTime $lastActivityAfter) : int
    {
        $c_userID = User::FIELD_USER_ID;
        $t_users = self::USER_TABLE_NAME;
        $c_ID = User::FIELD_ID;
        $c_activationDate = User::FIELD_ACTIVATION_DATE;
        
        $q = "SELECT `${c_userID}` FROM `${t_users}` " . 
            "WHERE `${c_ID}` = ? AND `${c_activationDate}` > ? LIMIT 1";
        
        return $this->db->queryInt($q, 0, 'ss', 
            $ID, Util::dateTimeToString($lastActivityAfter));
    }
    
    public function getLoginsByPrefix(string $loginPrefix, int $limit = 10) : array
    {
        $c_login = User::FIELD_LOGIN;
        $t_users = self::USER_TABLE_NAME;
        
        $q = "SELECT `${c_login}` FROM `${t_users}` " . 
            "WHERE `${c_login}` LIKE ? " . 
            "ORDER BY `${c_login}` LIMIT ?";

        return $this->db->queryArray($q, "si", 
            $loginPrefix . '%', $limit);
    }

    public function userExists(string $login) : bool
    {
        return $this->getUserID($login) > 0;
    }

    public function getUserID(string $login) : int
    {
        $c_id = User::FIELD_ID;
        $c_login = User::FIELD_LOGIN;
        $t_users = self::USER_TABLE_NAME;
        
        $q = "SELECT `${c_id}` FROM `${t_users}` WHERE `{$c_login}` = ? LIMIT 1";

        return $this->db->queryInt($q, -1, "s", $login);
    }

    private function updateStringField(string $column, int $userID, string $value) : int
    {
        $t_users = self::USER_TABLE_NAME;
        $c_ID = User::FIELD_ID;
        
        $q = "UPDATE `${t_users}` SET `${column}` = ? WHERE `${c_ID}` = ? LIMIT 1";

        return $this->db->execute($q, "si", $value, $userID);
    }

    private function updateDateField(string $column, int $userID, DateTime $dateTime) : int
    {
        return $this->updateStringField($column, $userID, Util::dateTimeToString($dateTime));
    }
    
    public function updateLoginDate(int $userID, DateTime $loginDate) : int
    {
        return $this->updateDateField(User::FIELD_LOGIN_DATE, $userID, $loginDate);
    }

    public function updateActivity(int $userID, DateTime $lastActivity) : int
    {
        return $this->updateDateField(User::FIELD_ACTIVATION_DATE, $userID, $lastActivity);
    }

    public function updateTerminated(int $userID, bool $isTerminated) : int
    {
        return $this->updateStringField(User::FIELD_IS_TERMINATED, $userID, Util::boolToYesNo($isTerminated));
    }
    
    public function updatePassword(int $userID, string $password) : int
    {
        return $this->updateStringField(User::FIELD_PASS, $userID, $password);
    }
    
    public function getUserData(string $userID) : array
    {
        $t_userdata = self::DATA_TABLE_NAME;
        $selectColumns = array(
            User::FIELD_NAME,
            User::FIELD_VALUE,
        );

        $columnList = $this->toColumnList($selectColumns);
        $c_userID = User::FIELD_USER_ID;
        $c_name = User::FIELD_NAME;

        $q = "SELECT (`${columnList}`) FROM `${t_userdata}` WHERE `${c_userID}` = ? ORDER BY `{$c_name}`";

        $result = $this->db->query($q, 'i', $userID);

        $properties = array();
        while ($row = $result->fetch_row()) {
            $properties[$row[0]] = $row[1];
        }

        return $properties;
    }

    public function updateUserData(int $userID, array $changedValues) : int
    {
        $t_userdata = self::DATA_TABLE_NAME;
        $insertColumns = array(
            User::FIELD_USER_ID,
            User::FIELD_NAME,
            User::FIELD_VALUE,
        );
        
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $c_value = User::FIELD_VALUE;
        
        $q = "INSERT INTO `${t_userdata}` (`${columnList}`) VALUES (${placeholderList}) " .
            "ON DUPLICATE KEY UPDATE `${c_value}` = ?";
        
        $affectedRows = 0;
        foreach ($changedValues as $key => $value) {
            // "value" appears twice due to the ON DUPLICATE KEY UPDATE
            $affectedRows += $this->db->execute($q, 'isss', 
                $userID, $key, $value, $value);
        }

        return $affectedRows;
    }

    public function getCitiesByPrefix(string $cityPrefix, int $limit = 10) : array
    {
        $c_value = User::FIELD_VALUE;
        $t_userdata = self::DATA_TABLE_NAME;
        $c_nameColumn = User::FIELD_NAME;

        $q = "SELECT DISTINCT `${c_value}` FROM `${t_userdata}` " . 
            "WHERE `${c_value}` LIKE ? AND `${c_nameColumn}` = ? " . 
            "ORDER BY `${c_value}` LIMIT ?";

        return $this->db->queryArray($q, "ssi", 
            $cityPrefix . '%', User::KEY_CITY, $limit);
    }

    public function getDiariesByPrefix(int $userID, string $userLogin, string $diaryPrefix, int $limit = 10) : array
    {
        // Returns the login names of users who allow the current user to create entries
        $c_login = User::FIELD_LOGIN;
        $t_users = self::USER_TABLE_NAME;
        $c_ID = User::FIELD_ID;
        $c_userID = User::FIELD_USER_ID;
        $t_userdata = self::DATA_TABLE_NAME;
        $c_name = User::FIELD_NAME;
        $c_value = User::FIELD_VALUE;

        // "The user themselves" (duh)
        $ownAccessClause = "`${c_login}` = ?";

        // "People who allow any registered user to write to their diary"
        $blogAccess = User::KEY_BLOG_ACCESS;
        $userAllowsAccess = "SELECT `${c_userID}` FROM `${t_userdata}` WHERE `${c_name}` = '${blogAccess}' AND `${c_value}` = ?";
        $registeredAccessClause = "`${c_ID}` IN ($userAllowsAccess)";

        // "People who made <current user> their friends and allow friends to write to their diary"
        $sourceUserIds = $this->friendRepository->getSourceUserIdsQuery();
        $friendAccessClause = "`${c_ID}` IN (${userAllowsAccess} AND `${c_userID}` IN (${sourceUserIds}))";

        $q = "SELECT `${c_login}` FROM `${t_users}` " .
            "WHERE `${c_login}` LIKE ? AND (${ownAccessClause} OR ${registeredAccessClause} OR ${friendAccessClause}) " .
            "ORDER BY `$c_login` LIMIT ?";

        return $this->db->queryArray($q, 'ssssssisi',
            $diaryPrefix . '%',
            $userLogin,
            AccessType::REGISTERED->value, 
            AccessType::FRIENDS->value,
            $userID,
            FriendType::FRIEND,
            $limit);
    }
    
    public function save(User $user) : int
    {
        $t_users = self::USER_TABLE_NAME;
        $userArray = $user->toArray();
        
        $insertColumns = array_keys($userArray);
        $values = array_values($userArray);
        if ($user->getID() < 0) {
            array_shift($insertColumns);
            array_shift($values);
        }

        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `${t_users}` (`${columnList}`) VALUES (${placeholderList})";
        
        if ($user->getID() < 0) {
            $affectedRows = $this->db->execute($q, 'ssssss', ...$values);
            if ($affectedRows > 0) {
                $user->setID($this->db->getLastInsertID());
            }
        } else {
            $affectedRows = $this->db->execute($q, 'issssss', ...$values);
        }

        $affectedRows += $this->updateUserData($user->getID(), $user->flushUserData());
        return $affectedRows;
    }
}
