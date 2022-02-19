<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Models\Session;
use Sarok\Service\DB;
use DateTime;
use Sarok\Models\Friend;

class SessionRepository extends AbstractRepository
{
    const TABLE_NAME = 'sessions';
    
    private const COLUMN_NAMES = array(
        Session::FIELD_ID,
        Session::FIELD_USER_ID,
        Session::FIELD_CREATE_DATE,
        Session::FIELD_LOGIN_DATE,
        Session::FIELD_ACTIVATION_DATE,
        Session::FIELD_IP,
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
        $userID = Session::FIELD_USER_ID;
        $sessions = $this->getTableName();
        $activationDate = Session::FIELD_ACTIVATION_DATE;
        
        return "SELECT DISTINCT `$userID` from `$sessions` WHERE `$activationDate` > ?";
    }

    public function getActiveRelatedUserIds(int $userID, DateTime $lastActivityAfter, string $friendType) : array
    {
        $userIDColumn = Session::FIELD_USER_ID;
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
    
    public function getFriendsActivity(int $userID, DateTime $lastActivityAfter) : array
    {
        $userIDColumn = Session::FIELD_USER_ID;
        $activationDate = Session::FIELD_ACTIVATION_DATE;
        $sessions = $this->getTableName();
        $destinationIdsQuery = $this->friendRepository->getDestinationUserIdsQuery();
        
        $q = "SELECT DISTINCT `$userIDColumn`, `$activationDate` FROM `$sessions` " .
            "WHERE `$activationDate` > ? AND `$userIDColumn` IN ($destinationIdsQuery) " .
            "ORDER BY `$activationDate` DESC";
        
        $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        $friendType = Friend::TYPE_FRIEND;
        $result = $this->db->query($q, 'sis', $lastActivityString, $userID, $friendType);
        
        $friendsActivity = array();
        while ($row = $result->fetch_row()) {
            $friendsActivity[] = array(
                'userID' => $row[0],
                'lastActivity' => Util::utcDateTimeFromString($row[1])
            );
        }
        
        return $friendsActivity;
    }
    
    public function getActiveSessions(int $userID, DateTime $lastActivityAfter) : int
    {
        $ID = Session::FIELD_ID;
        $sessions = $this->getTableName();
        $userIDColumn = Session::FIELD_USER_ID;
        $activationDate = Session::FIELD_ACTIVATION_DATE;
        
        $q = "SELECT COUNT(`$ID`) FROM `$sessions` WHERE `$userIDColumn` = ? AND `$activationDate` > ?";
        
        $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        $result = $this->db->execute($q, 'is', $userID, $lastActivityString);
        if ($sessionCount = $result->fetch_row()) {
            return $sessionCount[0];
        }
        
        return 0;
    }
    
    public function getUserIdIfActive(string $ID, DateTime $lastActivityAfter) : int
    {
        $userID = Session::FIELD_USER_ID;
        $sessions = $this->getTableName();
        $IDColumn = Session::FIELD_ID;
        $activationDate = Session::FIELD_ACTIVATION_DATE;
        
        $q = "SELECT `$userID` FROM `$sessions` WHERE `$IDColumn` = ? AND `$activationDate` > ? LIMIT 1";
        $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        $result = $this->db->execute($q, 'ss', $ID, $lastActivityString);
        if ($row = $result->fetch_row()) {
            return (int) $row[0];
        }
        
        return 0;
    }

    public function updateActivity(string $ID, DateTime $lastActivity) : int
    {
        $sessions = $this->getTableName();
        $activationDate = Session::FIELD_ACTIVATION_DATE;
        $IDColumn = Session::FIELD_ID;
        
        $q = "UPDATE `$sessions` SET `$activationDate` = ? WHERE `$IDColumn` = ? LIMIT 1";
        $lastActivityString = Util::dateTimeToString($lastActivity);
        return $this->db->execute($q, "ss", $lastActivityString, $ID);
    }

    public function updateUserID(string $ID, int $userID, DateTime $loginDate) : int
    {
        $sessions = $this->getTableName();
        $userIDColumn = Session::FIELD_USER_ID;
        $loginDateColumn = Session::FIELD_LOGIN_DATE;
        $activationDate = Session::FIELD_ACTIVATION_DATE;
        $IDColumn = Session::FIELD_ID;
        
        $q = "UPDATE `$sessions` SET `$userIDColumn` = ?, `$loginDateColumn` = ?, `$activationDate` = ? " .
            "WHERE `$IDColumn` = ? LIMIT 1";
        
        $loginDateString = Util::dateTimeToString($loginDate);
        return $this->db->execute($q, "isss", $userID, $loginDateString, $loginDateString, $ID);
    }
    
    public function deleteInactive(DateTime $lastActivityBefore) : int
    {
        $sessions = $this->getTableName();
        $activationDate = Session::FIELD_ACTIVATION_DATE;
        
        $q = "DELETE FROM `$sessions` WHERE `$activationDate` <= ?";
        $lastActivityString = Util::dateTimeToString($lastActivityBefore);
        return $this->db->execute($q, 's', $lastActivityString);
    }
    
    public function deleteByIP(string $IP) : int
    {
        $sessions = $this->getTableName();
        $IPColumn = Session::FIELD_IP;
        
        $q = "DELETE FROM `$sessions` WHERE `$IPColumn` = ?";
        return $this->db->execute($q, 's', $IP);
    }
    
    public function insert(Session $data) : int
    {
        $sessions = $this->getTableName();
        $sessionArray = $data->toArray();
        $insertColumns = array_keys($sessionArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$sessions`(`$columnList`) VALUES ($placeholderList)";
        $values = array_values($sessionArray);
        return $this->db->execute($q, 'sissss', ...$values);
    }
}
