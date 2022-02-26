<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Service\DB;
use Sarok\Models\User;
use Sarok\Models\FriendType;
use Sarok\Models\Friend;

class FriendRepository extends AbstractRepository
{
    const TABLE_NAME = 'friends';
    
    private const COLUMN_NAMES = array(
        Friend::FIELD_FRIEND_OF, // source
        Friend::FIELD_USER_ID, // destination
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
    
    ////////////////////////////////////////////////
    // Queries (also used by other repositories)
    ////////////////////////////////////////////////

    /**
     * Returns a query that can be used to collect the ID of users who chose `userID` 
     * as their friend/enemy/reader. Uses the table alias `'f'`.
     * 
     * Requires adding `'is'` parameters to the statement (for `userID` and `friendType`).
     */ 
    public function getSourceUserIdsQuery()
    {
        $c_friendOf = Friend::FIELD_FRIEND_OF;
        $t_friends = $this->getTableName();
        $c_userID = Friend::FIELD_USER_ID;
        $c_friendType = Friend::FIELD_FRIEND_TYPE;
        
        
        return "SELECT `$c_friendOf` FROM `$t_friends` AS `f` " .
            "WHERE `f`.`$c_userID` = ? AND `f`.`$c_friendType` = ?";
    }
    
    /**
     * Returns a query that can be used to collect the ID of users who were chosen 
     * by `friendOf` as their friend/enemy/reader. Uses the table alias `'f'`.
     * 
     * Requires adding `'is'` parameters to the statement (for `friendOf` and `friendType`).
     */ 
    public function getDestinationUserIdsQuery()
    {
        $c_userID = Friend::FIELD_USER_ID;
        $t_friends = $this->getTableName();
        $c_friendOf = Friend::FIELD_FRIEND_OF;
        $c_friendType = Friend::FIELD_FRIEND_TYPE;
        
        return "SELECT `$c_userID` FROM `$t_friends` AS `f` " .
            "WHERE `f`.`$c_friendOf` = ? AND `f`.`$c_friendType` = ?";
    }
    
    /**
     * Returns a query that can be used to check the existence of an association between
     * `friendOf` and `userID`, with type `friendType`. Uses the table alias `'f'`.
     * 
     * Requires adding `'iis'` parameters to the statement (for `friendOf`, `userID` and `friendType`).
     */
    public function getAssociationExistsQuery()
    {
        $t_friends = $this->getTableName();
        $c_friendOf = Friend::FIELD_FRIEND_OF;
        $c_userID = Friend::FIELD_USER_ID;
        $c_friendType = Friend::FIELD_FRIEND_TYPE;
        
        return "SELECT 1 FROM `$t_friends` AS `f` " .
            "WHERE `f`.`$c_friendOf` = ? AND `f`.`$c_userID` = ? AND `f`.`$c_friendType` = ? LIMIT 1";
    }
    
    /**
     * Returns a query that can be used to collect the **login name** of users who were chosen 
     * by `friendOf` as their friend/enemy/reader. If no login name can be retrieved with the ID, 
     * falls back to returning the userID. Uses the table alias `'f'` and `'u'`.
     * 
     * Requires adding `'is'` parameters to the statement (for `friendOf` and `friendType`).
     */ 
    public function getDestinationLoginsQuery()
    {
        $c_login = User::FIELD_LOGIN;
        $t_friends = $this->getTableName();
        $t_users = UserRepository::USER_TABLE_NAME;
        $c_userID = Friend::FIELD_USER_ID;
        $c_ID = User::FIELD_ID;
        $c_friendOf = Friend::FIELD_FRIEND_OF;
        $c_friendType = Friend::FIELD_FRIEND_TYPE;
        
        return "SELECT IFNULL(`u`.`$c_login`, `f`.`$c_userID`) FROM `$t_friends` AS `f` " .
            "LEFT JOIN `$t_users` AS `u` ON `f`.`$c_userID` = `u`.`$c_ID` " .
            "WHERE `f`.`$c_friendOf` = ? AND `f`.`$c_friendType` = ?";
    }
    
    ///////////////////////////
    // Selects
    ///////////////////////////
    
    private function getUserIds(string $q, int $sourceOrDestinationID, FriendType $friendType) : array
    {
        return $this->db->queryArray($q, 'is', $sourceOrDestinationID, $friendType->value);
    }

    public function getSourceUserIds(int $userID, FriendType $friendType) : array
    {
        return $this->getUserIds($this->getSourceUserIdsQuery(), $userID, $friendType);
    }
    
    public function getDestinationUserIds(int $friendOf, FriendType $friendType) : array
    {
        return $this->getUserIds($this->getDestinationUserIdsQuery(), $friendOf, $friendType);
    }
    
    ///////////////////////////
    // Deletes
    ///////////////////////////

    private function deleteById(string $column, int $sourceOrDestinationID, FriendType $friendType) : int
    {
        $t_friends = $this->getTableName();
        $t_friendType = Friend::FIELD_FRIEND_TYPE;
        
        $q = "DELETE FROM `$t_friends` WHERE `$column` = ? AND `$t_friendType` = ?";
        return $this->db->execute($q, 'is', $sourceOrDestinationID, $friendType->value);
    }

    public function deleteBySourceUserId(int $friendOf, FriendType $friendType) : int
    {
        return $this->deleteById(Friend::FIELD_FRIEND_OF, $friendOf, $friendType);
    }

    public function deleteByDestinationUserId(int $userID, FriendType $friendType) : int
    {
        return $this->deleteById(Friend::FIELD_USER_ID, $userID, $friendType);
    }
    
    ///////////////////////////
    // Insert
    ///////////////////////////
    
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
