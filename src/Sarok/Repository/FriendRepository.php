<?php namespace Sarok\Repository;

use Sarok\Models\Friend;
use Sarok\Service\DB;

class FriendRepository extends AbstractRepository {

    const TABLE_NAME = 'friends';
    
    private const COLUMN_NAMES = array(
        Friend::FIELD_FRIEND_OF,
        Friend::FIELD_USER_ID,
        Friend::FIELD_FRIEND_TYPE,
    );
    
    public function __construct(DB $db) {
        parent::__construct($db);
    }
    
    protected function getTableName() : string {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array {
        return self::COLUMN_NAMES;
    }
    
    public function getSourceUserIdsQuery() {
        $friends = $this->getTableName();
        $friendOf = Friend::FIELD_FRIEND_OF;
        $userID = Friend::FIELD_USER_ID;
        $friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return the ID of users who chose "userID" as their friend/enemy/reader 
        return "SELECT `$friendOf` FROM `$friends` AS `f` " .
            "WHERE `f`.`$userID` = ? AND `f`.`$friendType` = ?";
    }
    
    public function getDestinationUserIdsQuery() {
        $friends = $this->getTableName();
        $userID = Friend::FIELD_USER_ID;
        $friendOf = Friend::FIELD_FRIEND_OF;
        $friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return the ID of users who were chosen by "friendOf" as their friend/enemy/reader 
        return "SELECT `$userID` FROM `$friends` AS `f` " .
            "WHERE `f`.`$friendOf` = ? AND `f`.`$friendType` = ?";
    }
    
    public function getAssociationExistsQuery() {
        $friends = $this->getTableName();
        $userID = Friend::FIELD_USER_ID;
        $friendOf = Friend::FIELD_FRIEND_OF;
        $friendType = Friend::FIELD_FRIEND_TYPE;
        
        // Return 1 if user "friendOf" chose "userID" as their friend/enemy/reader
        return "SELECT 1 FROM `$friends` AS `f` " .
            "WHERE `f`.`$friendOf` = ? AND `f`.`$userID` = ? AND `f`.`$friendType` = ? LIMIT 1";
    }
    
    public function getDestinationLoginsQuery() {
        $friends = $this->getTableName();
        $userID = Friend::FIELD_USER_ID;
        $friendOf = Friend::FIELD_FRIEND_OF;
        $friendType = Friend::FIELD_FRIEND_TYPE;
        
        // FIXME: Get field and table constants for "users"
        $login = 'login';
        $users = 'users';
        $ID = 'ID';
        
        // Return the login name of users who where chosen by "friendOf" as their friend/enemy/reader
        return "SELECT `u`.`$login` FROM `$friends` AS `f` LEFT JOIN `$users` AS `u` " .
            "ON `f`.`$userID` = `u`.`$ID` " .
            "WHERE `$friendOf` = ? AND `$friendType` = ?";
    }
    
    public function getSourceUserIds(int $userID, string $friendType) : array {
        $q = $this->getSourceUserIdsQuery();
        
        $result = $this->db->query($q, 'is', $userID, $friendType);
        $friendOfList = array();
        while ($entryID = $result->fetch_row()) {
            $friendOfList[] = $entryID[0];
        }
        
        return $friendOfList;
    }
    
    public function getDestinationUserIds(int $friendOf, string $friendType) : array {
        $q = $this->getDestinationUserIdsQuery();
        
        $result = $this->db->query($q, 'is', $friendOf, $friendType);
        $userIDList = array();
        while ($entryID = $result->fetch_row()) {
            $userIDList[] = $entryID[0];
        }
        
        return $userIDList;
    }
    
    public function deleteByDestinationId(int $userID, string $friendType) : int {
        $friends = $this->getTableName();
        $userIDColumn = Friend::FIELD_USER_ID;
        $friendTypeColumn = Friend::FIELD_FRIEND_TYPE;
        
        $q = "DELETE FROM `$friends` WHERE `$userIDColumn` = ? AND `$friendTypeColumn` = ?";
        return $this->db->execute($q, 'is', $userID, $friendType);
    }
    
    public function deleteBySourceId(int $friendOf, string $friendType) : int {
        $friends = $this->getTableName();
        $friendOfColumn = Friend::FIELD_FRIEND_OF;
        $friendTypeColumn = Friend::FIELD_FRIEND_TYPE;
        
        $q = "DELETE FROM `$friends` WHERE `$friendOfColumn` = ? AND `$friendTypeColumn` = ?";
        return $this->db->execute($q, 'is', $friendOf, $friendType);
    }
    
    public function insert(Friend $data) : int {
        $friends = $this->getTableName();
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$friends`(`$columnList`) VALUES ($placeholderList)";
        $values = $data->toArray();
        return $this->db->execute($q, 'iis', ...$values);
    }
}
