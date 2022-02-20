<?php namespace Sarok\Repository;

use Sarok\Service\DB;
use Sarok\Models\User;
use Sarok\Models\FriendType;
use Sarok\Models\Friend;

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
        $t_friends = $this->getTableName();
        $c_friendOf = Friend::FIELD_FRIEND_OF;
        $c_userID = Friend::FIELD_USER_ID;
        $c_friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return the ID of users who chose "userID" as their friend/enemy/reader
        return "SELECT `$c_friendOf` FROM `$t_friends` AS `f` " .
            "WHERE `f`.`$c_userID` = ? AND `f`.`$c_friendType` = ?";
    }
    
    public function getDestinationUserIdsQuery()
    {
        $t_friends = $this->getTableName();
        $c_userID = Friend::FIELD_USER_ID;
        $c_friendOf = Friend::FIELD_FRIEND_OF;
        $c_friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return the ID of users who were chosen by "friendOf" as their friend/enemy/reader
        return "SELECT `$c_userID` FROM `$t_friends` AS `f` " .
            "WHERE `f`.`$c_friendOf` = ? AND `f`.`$c_friendType` = ?";
    }
    
    public function getAssociationExistsQuery()
    {
        $t_friends = $this->getTableName();
        $c_userID = Friend::FIELD_USER_ID;
        $c_friendOf = Friend::FIELD_FRIEND_OF;
        $c_friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return 1 if user "friendOf" chose "userID" as their friend/enemy/reader
        return "SELECT 1 FROM `$t_friends` AS `f` " .
            "WHERE `f`.`$c_friendOf` = ? AND `f`.`$c_userID` = ? AND `f`.`$c_friendType` = ? LIMIT 1";
    }
    
    public function getDestinationLoginsQuery()
    {
        $c_login = User::FIELD_LOGIN;
        $t_friends = $this->getTableName();
        $t_users = UserRepository::USER_TABLE_NAME;
        $c_userID = Friend::FIELD_USER_ID;
        $c_ID = User::FIELD_ID;
        $c_friendOf = Friend::FIELD_FRIEND_OF;
        $c_friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return the login name of users who where chosen by "friendOf" as their friend/enemy/reader
        return "SELECT `u`.`$c_login` FROM `$t_friends` AS `f` LEFT JOIN `$t_users` AS `u` " .
            "ON `f`.`$c_userID` = `u`.`$c_ID` " .
            "WHERE `f`.`$c_friendOf` = ? AND `f`.`$c_friendType` = ?";
    }
    
    private function getUserIds(string $q, int $sourceOrDestinationID, string $friendType) : array
    {
        return $this->db->queryArray($q, 'is', $sourceOrDestinationID, $friendType);
    }

    public function getSourceUserIds(int $userID, string $friendType) : array
    {
        return $this->getUserIds($this->getSourceUserIdsQuery(), $userID, $friendType);
    }
    
    public function getDestinationUserIds(int $friendOf, string $friendType) : array
    {
        return $this->getUserIds($this->getDestinationUserIdsQuery(), $friendOf, $friendType);
    }
    
    private function deleteById(string $column, int $sourceOrDestinationID, string $friendType) : int
    {
        $t_friends = $this->getTableName();
        $t_friendType = Friend::FIELD_FRIEND_TYPE;
        
        $q = "DELETE FROM `$t_friends` WHERE `$column` = ? AND `$t_friendType` = ?";
        return $this->db->execute($q, 'is', $sourceOrDestinationID, $friendType);
    }

    public function deleteByDestinationUserId(int $userID, string $friendType) : int
    {
        return $this->deleteById(Friend::FIELD_USER_ID, $userID, $friendType);
    }
    
    public function deleteBySourceUserId(int $friendOf, string $friendType) : int
    {
        return $this->deleteById(Friend::FIELD_FRIEND_OF, $friendOf, $friendType);
    }
    
    public function save(Friend $friend) : int
    {
        $t_friends = $this->getTableName();
        $friendArray = $friend->toArray();
        $insertColumns = array_keys($friendArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT IGNORE INTO `$t_friends` (`$columnList`) VALUES ($placeholderList)";
        $values = array_values($friendArray);
        return $this->db->execute($q, 'iis', ...$values);
    }
}
