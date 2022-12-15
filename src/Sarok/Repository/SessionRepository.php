<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\DB;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\Repository;
use Sarok\Models\Session;
use Sarok\Models\FriendType;
use Sarok\Models\FriendActivity;
use DateTime;

final class SessionRepository extends Repository
{
    public const TABLE_NAME = 'sessions';
    
    public const COLUMN_NAMES = array(
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
    
    public function getActiveUserIdsQuery() : string
    {
        $c_userID = Session::FIELD_USER_ID;
        $t_sessions = self::TABLE_NAME;
        $c_activationDate = Session::FIELD_ACTIVATION_DATE;
        
        return "SELECT DISTINCT `${c_userID}` from `${t_sessions}` WHERE `${c_activationDate}` > ?";
    }

    public function getActiveRelatedUserIds(int $userID, DateTime $lastActivityAfter, FriendType $friendType) : array
    {
        $activeUserIdsQuery = $this->getActiveUserIdsQuery();
        $c_userID = Session::FIELD_USER_ID;
        $destinationIdsQuery = $this->friendRepository->getDestinationUserIdsQuery();
        
        $q = "${activeUserIdsQuery} AND `${c_userID}` IN (${destinationIdsQuery})";

        return $this->db->queryArray($q, 'sis', 
            Util::dateTimeToString($lastActivityAfter), $userID, $friendType->value);
    }
    
    public function getFriendsActivity(int $userID, DateTime $lastActivityAfter) : array
    {
        $c_userID = Session::FIELD_USER_ID;
        $c_activationDate = Session::FIELD_ACTIVATION_DATE;
        $t_sessions = self::TABLE_NAME;
        $destinationIdsQuery = $this->friendRepository->getDestinationUserIdsQuery();
        
        $q = "SELECT DISTINCT `${c_userID}`, `${c_activationDate}` FROM `${t_sessions}` " .
            "WHERE `${c_activationDate}` > ? AND `${c_userID}` IN (${destinationIdsQuery}) " .
            "ORDER BY `${c_activationDate}` DESC";
        
        return $this->db->queryObjects($q, FriendActivity::class, 'sis', 
            Util::dateTimeToString($lastActivityAfter), $userID, FriendType::FRIEND->value);
    }
    
    public function getActiveSessions(int $userID, DateTime $lastActivityAfter) : int
    {
        $c_ID = Session::FIELD_ID;
        $t_sessions = self::TABLE_NAME;
        $c_userID = Session::FIELD_USER_ID;
        $c_activationDate = Session::FIELD_ACTIVATION_DATE;
        
        $q = "SELECT COUNT(`${c_ID}`) FROM `${t_sessions}` " . 
            "WHERE `${c_userID}` = ? AND `${c_activationDate}` > ?";

        return $this->db->queryInt($q, 0, 'is', 
            $userID, Util::dateTimeToString($lastActivityAfter));
    }

    public function getUserIdIfActive(string $ID, DateTime $lastActivityAfter) : int
    {
        $c_userID = Session::FIELD_USER_ID;
        $t_sessions = self::TABLE_NAME;
        $c_ID = Session::FIELD_ID;
        $c_activationDate = Session::FIELD_ACTIVATION_DATE;
        
        $q = "SELECT `${c_userID}` FROM `${t_sessions}` " . 
            "WHERE `${c_ID}` = ? AND `${c_activationDate}` > ? " . 
            "LIMIT 1";

        return $this->db->queryInt($q, 0, 'ss', 
            $ID, Util::dateTimeToString($lastActivityAfter));
    }

    public function updateActivity(string $ID, DateTime $lastActivity) : int
    {
        $t_sessions = self::TABLE_NAME;
        $c_activationDate = Session::FIELD_ACTIVATION_DATE;
        $c_ID = Session::FIELD_ID;
        
        $q = "UPDATE `${t_sessions}` SET `${c_activationDate}` = ? WHERE `${c_ID}` = ? LIMIT 1";

        return $this->db->execute($q, "ss", 
            Util::dateTimeToString($lastActivity), $ID);
    }

    public function updateUserIDAndLoginDate(string $ID, int $userID, DateTime $loginDate) : int
    {
        $t_sessions = self::TABLE_NAME;
        $c_userID = Session::FIELD_USER_ID;
        $c_loginDate = Session::FIELD_LOGIN_DATE;
        $c_activationDate = Session::FIELD_ACTIVATION_DATE;
        $c_ID = Session::FIELD_ID;
        
        $q = "UPDATE `${t_sessions}` " . 
            "SET `${c_userID}` = ?, `${c_loginDate}` = ?, `${c_activationDate}` = ? " .
            "WHERE `${c_ID}` = ? LIMIT 1";
        
        $loginDateString = Util::dateTimeToString($loginDate);
        return $this->db->execute($q, "isss", 
            $userID, $loginDateString, $loginDateString, $ID);
    }
    
    public function deleteInactive(DateTime $lastActivityBefore) : int
    {
        $t_sessions = self::TABLE_NAME;
        $c_activationDate = Session::FIELD_ACTIVATION_DATE;
        
        $q = "DELETE FROM `${t_sessions}` WHERE `${c_activationDate}` <= ?";

        return $this->db->execute($q, 's', 
            Util::dateTimeToString($lastActivityBefore));
    }
    
    public function deleteByIP(string $IP) : int
    {
        $t_sessions = self::TABLE_NAME;
        $c_IP = Session::FIELD_IP;
        
        $q = "DELETE FROM `${t_sessions}` WHERE `${c_IP}` = ?";

        return $this->db->execute($q, 's', 
            $IP);
    }
    
    public function save(Session $session) : int
    {
        $t_sessions = self::TABLE_NAME;
        $sessionArray = $session->toArray();
        $insertColumns = array_keys($sessionArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `${t_sessions}` (`${columnList}`) VALUES (${placeholderList})";
        $values = array_values($sessionArray);
        return $this->db->execute($q, 'sissss', ...$values);
    }
}
