<?php namespace Sarok\Repository;

use Sarok\Models\Friend;
use Sarok\Models\FriendType;
use Sarok\Models\User;
use Sarok\Service\DB;

class FriendRepository extends AbstractRepository
{
    const TABLE_NAME = 'friends';
    
    private const COLUMN_NAMES = array(
        Friend::FIELD_FRIEND_OF,
        Friend::FIELD_USER_ID,
        Friend::FIELD_FRIEND_TYPE,
    );
    
    public function __construct(DB $db)
    {
        parent::__construct($db);
    }
    
    protected function getTableName() : string
    {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array
    {
        return self::COLUMN_NAMES;
    }
    
    public function getSourceUserIdsQuery()
    {
        $friends = $this->getTableName();
        $friendOf = Friend::FIELD_FRIEND_OF;
        $userID = Friend::FIELD_USER_ID;
        $friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return the ID of users who chose "userID" as their friend/enemy/reader
        return "SELECT `$friendOf` FROM `$friends` AS `f` " .
            "WHERE `f`.`$userID` = ? AND `f`.`$friendType` = ?";
    }
    
    public function getDestinationUserIdsQuery()
    {
        $friends = $this->getTableName();
        $userID = Friend::FIELD_USER_ID;
        $friendOf = Friend::FIELD_FRIEND_OF;
        $friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return the ID of users who were chosen by "friendOf" as their friend/enemy/reader
        return "SELECT `$userID` FROM `$friends` AS `f` " .
            "WHERE `f`.`$friendOf` = ? AND `f`.`$friendType` = ?";
    }
    
    public function getAssociationExistsQuery()
    {
        $friends = $this->getTableName();
        $userID = Friend::FIELD_USER_ID;
        $friendOf = Friend::FIELD_FRIEND_OF;
        $friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return 1 if user "friendOf" chose "userID" as their friend/enemy/reader
        return "SELECT 1 FROM `$friends` AS `f` " .
            "WHERE `f`.`$friendOf` = ? AND `f`.`$userID` = ? AND `f`.`$friendType` = ? LIMIT 1";
    }
    
    public function getDestinationLoginsQuery()
    {
        $login = User::FIELD_LOGIN;
        $friends = $this->getTableName();
        $users = UserRepository::TABLE_NAME;
        $userID = Friend::FIELD_USER_ID;
        $ID = User::FIELD_ID;
        $friendOf = Friend::FIELD_FRIEND_OF;
        $friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return the login name of users who where chosen by "friendOf" as their friend/enemy/reader
        return "SELECT `u`.`$login` FROM `$friends` AS `f` LEFT JOIN `$users` AS `u` " .
            "ON `f`.`$userID` = `u`.`$ID` " .
            "WHERE `$friendOf` = ? AND `$friendType` = ?";
    }
    
    private function getUserIds(string $q, int $userID, string $friendType) : array
    {
        $result = $this->db->query($q, 'is', $friendOf, $friendType);
        $userIds = array();
        while ($entryID = $result->fetch_row()) {
            $userIds[] = $entryID[0];
        }
        
        return $userIds;
    }

    public function getSourceUserIds(int $userID, string $friendType) : array
    {
        return $this->getUserIds($this->getSourceUserIdsQuery(), $userID, $friendType);
    }
    
    public function getDestinationUserIds(int $friendOf, string $friendType) : array
    {
        return $this->getUserIds($this->getDestinationUserIdsQuery(), $userID, $friendType);
    }
    
    private function deleteById(string $IDColumn, int $userID, string $friendType) : int
    {
        $friends = $this->getTableName();
        $friendTypeColumn = Friend::FIELD_FRIEND_TYPE;
        
        $q = "DELETE FROM `$friends` WHERE `$IDColumn` = ? AND `$friendTypeColumn` = ?";
        return $this->db->execute($q, 'is', $userID, $friendType);
    }

    public function deleteByDestinationUserId(int $userID, string $friendType) : int
    {
        return $this->deleteById(Friend::FIELD_USER_ID, $userID, $friendType);
    }
    
    public function deleteBySourceUserId(int $friendOf, string $friendType) : int
    {
        return $this->deleteById(Friend::FIELD_FRIEND_OF, $userID, $friendType);
    }
    
    public function insert(Friend $data) : int
    {
        $friends = $this->getTableName();
        $friendArray = $data->toArray();
        $insertColumns = array_keys($friendArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$friends`(`$columnList`) VALUES ($placeholderList)";
        $values = array_values($friendArray);
        return $this->db->execute($q, 'iis', ...$values);
    }
}
