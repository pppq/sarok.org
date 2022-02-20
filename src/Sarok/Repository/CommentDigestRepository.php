<?php namespace Sarok\Repository;

use DateTime;
use Sarok\Util;
use Sarok\Service\DB;
use Sarok\Models\Friend;
use Sarok\Models\FriendType;
use Sarok\Models\CommentDigest;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\AbstractRepository;

class CommentDigestRepository extends AbstractRepository
{
    const TABLE_NAME = 'cache_commentlist';
    
    private const COLUMN_NAMES = array(
        CommentDigest::FIELD_CATEGORY,
        CommentDigest::FIELD_ID,
        CommentDigest::FIELD_OWNER_ID,
        CommentDigest::FIELD_USER_ID,
        CommentDigest::FIELD_DIARY_ID,
        CommentDigest::FIELD_ENTRY_ID,
        CommentDigest::FIELD_CREATE_DATE,
        CommentDigest::FIELD_ACCESS,
        CommentDigest::FIELD_BODY,
        CommentDigest::FIELD_LAST_USED,
    );
    
    /* @var FriendRepository */
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
    
    private function deleteByColumn(string $column, int $value) : int
    {
        $t_cache_commentlist = $this->getTableName();
        
        $q = "DELETE FROM `$t_cache_commentlist` WHERE `$column` = ?";
        return $this->db->execute($q, 'i', $value);
    }
    
    public function deleteById(int $ID) : int
    {
        return $this->deleteByColumn(CommentDigest::FIELD_ID, $ID);
    }
    
    public function deleteByEntryId(int $entryID) : int
    {
        return $this->deleteByColumn(CommentDigest::FIELD_ENTRY_ID, $entryID);
    }
    
    public function deleteByOwnerId(int $ownerID) : int
    {
        return $this->deleteByColumn(CommentDigest::FIELD_OWNER_ID, $ownerID);
    }
    
    public function deleteByCategoryAndOwnerId(string $category, int $ownerID) : int
    {
        $t_cache_commentlist = $this->getTableName();
        $c_category = CommentDigest::FIELD_CATEGORY;
        $c_ownerID = CommentDigest::FIELD_OWNER_ID;
        
        $q = "DELETE FROM `$t_cache_commentlist` WHERE `$c_category` = ? AND `$c_ownerID` = ?";
        return $this->db->execute($q, 'si', $category, $ownerID);
    }
    
    public function deleteLastUsedBefore(DateTime $lastUsed) : int
    {
        $t_cache_commentlist = $this->getTableName();
        $c_lastUsed = CommentDigest::FIELD_LAST_USED;
        
        $q = "DELETE FROM `$t_cache_commentlist` WHERE `$c_lastUsed` < ?";
        return $this->db->execute($q, 's', Util::dateTimeToString($lastUsed));
    }
    
    public function updateLastUsed(DateTime $lastUsed, string $category, array $IDs, string $friendOfId = '') : int
    {
        // Introduce an alias after saving placeholders based on the original list
        $placeholderList = $this->toPlaceholderList($IDs);
        $values = &$IDs;
        
        // First two values in the UPDATE statement is the timestamp and the category
        array_unshift($values, Util::dateTimeToString($lastUsed), $category);
        
        // If last parameter is set, the "all comments" section is restricted to comments made by friends of the user
        if ($category === CommentDigest::CATEGORY_ALL_COMMENTS && strlen($friendOfId) > 0) {
            $friendsSubQuery = $this->friendRepository->getDestinationLoginsQuery();
            $c_diaryID = CommentDigest::FIELD_DIARY_ID;
            $friendsOnlyClause = "AND `$c_diaryID` IN ($friendsSubQuery) ";
            
            // Optional third-fourth value (at index 2-3) is the user ID when given and the association type
            array_splice($values, 2, 0, array($friendOfId, FriendType::FRIEND));
        } else {
            $friendsOnlyClause = '';
        }

        $t_cache_commentlist = $this->getTableName();
        $c_lastUsed = CommentDigest::FIELD_LAST_USED;
        $c_category = CommentDigest::FIELD_CATEGORY;
        $c_ID = CommentDigest::FIELD_ID;
        
        $q = "UPDATE `$t_cache_commentlist` SET `$c_lastUsed` = ? WHERE `$c_category` = ? $friendsOnlyClause" .
            "AND `$c_ID` IN ($placeholderList)";
            
        return $this->db->executeWithParams($q, $values);
    }
    
    public function updateAccess(string $access, array $entryIDs) : int
    {
        $t_cache_commentlist = $this->getTableName();
        $c_access = CommentDigest::FIELD_ACCESS;
        $c_entryID = CommentDigest::FIELD_ENTRY_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        
        $q = "UPDATE `$t_cache_commentlist` SET `$c_access` = ? WHERE `$c_entryID` IN ($placeholderList)";
        
        // Introduce an alias, we don't want to copy the array by assignment here
        $values = &$entryIDs;
        
        // Prepend first parameter (access type)
        array_unshift($values, $access);
        return $this->db->executeWithParams($q, $values);
    }
    
    public function getMostRecent(
        string $category, 
        int $ownerID, 
        bool $friendsOnly, 
        array $bannedIDs = array(), 
        int $limit = 30) : array
    {
        return $this->getMostRecentBefore(
            $category,
            $ownerID,
            $friendsOnly,
            Util::utcDateTimeFromString(),
            $bannedIDs,
            $limit);
    }
    
    public function getMostRecentBefore(
        string $category,
        int $ownerID,
        bool $friendsOnly,
        DateTime $createDate,
        array $bannedIDs = array(),
        int $limit = 30) : array
    {
        // Get the shared comment digests as well for the "all comments" section (ownerID is 0 in that case)
        $c_ownerID = CommentDigest::FIELD_OWNER_ID;
        if ($category === CommentDigest::CATEGORY_ALL_COMMENTS) {
            $ownerClause = "`$c_ownerID` IN (0, ?)";
        } else {
            $ownerClause = "`$c_ownerID` = ?";
        }

        $values = array(
            $ownerID,
            $category,
            Util::dateTimeToString($createDate)
        );
        
        // If "friendsOnly" is set, the "all comments" section is restricted to comments made by friends of the user
        $c_diaryID = CommentDigest::FIELD_DIARY_ID;
        if ($category === CommentDigest::CATEGORY_ALL_COMMENTS && $friendsOnly === true) {
            $friendsSubQuery = $this->friendRepository->getDestinationLoginsQuery();
            $friendsOnlyClause = "AND `$c_diaryID` IN ($friendsSubQuery) ";
            
            // Parameter 4 (index 3) should be the ownerID again, followed by the association type
            $values[] = $ownerID;
            $values[] = FriendType::FRIEND;
        } else {
            $friendsOnlyClause = '';
        }
        
        /*
         * Remove banned people from the output, unless the category is "my comments"; you should be able to see
         * your own comments even if you banned or got banned by the blog owner.
         */
        $c_userID = CommentDigest::FIELD_USER_ID;
        if ($category !== CommentDigest::CATEGORY_MY_COMMENTS && count($bannedIDs) > 0) {
            $placeholderList = $this->toPlaceholderList($bannedIDs);
            $bannedClause = "AND `$c_userID` NOT IN ($placeholderList) AND `$c_diaryID` NOT IN ($placeholderList) ";
            
            // Parameters 5 and up (index 4+) should be the banned ID list, but twice!
            array_push($values, ...$bannedIDs, ...$bannedIDs);
        } else {
            $bannedClause = '';
        }
        
        // XXX: not all fields are populated
        $selectColumns = array(
            CommentDigest::FIELD_ID,
            CommentDigest::FIELD_USER_ID,
            CommentDigest::FIELD_DIARY_ID,
            CommentDigest::FIELD_ENTRY_ID,
            CommentDigest::FIELD_CREATE_DATE,
            CommentDigest::FIELD_ACCESS,
            CommentDigest::FIELD_BODY,
        );
        
        $columnList = $this->toColumnList($selectColumns);
        $t_cache_commentlist = $this->getTableName();
        $c_category = CommentDigest::FIELD_CATEGORY;
        $c_createDate = CommentDigest::FIELD_CREATE_DATE;
        
        $q = "SELECT `$columnList` FROM `$t_cache_commentlist` ".
             "WHERE $ownerClause AND `$c_category` = ? AND `$c_createDate` <= ? {$friendsOnlyClause}{$bannedClause}" .
             "ORDER BY `$c_createDate` DESC LIMIT ?";
        
        // Last parameter is the limit
        $values[] = $limit;
        return $this->db->queryObjectsWithParams($q, CommentDigest::class, $values);
    }
    
    public function save(CommentDigest $commentDigest) : int
    {
        $t_cache_commentlist = $this->getTableName();
        $commentDigestArray = $commentDigest->toArray();
        $insertColumns = array_keys($commentDigestArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $c_body = CommentDigest::FIELD_BODY;
        $c_lastUsed = CommentDigest::FIELD_LAST_USED;
        
        $q = "INSERT INTO `$t_cache_commentlist` (`$columnList`) VALUES ($placeholderList) ON DUPLICATE KEY UPDATE `$c_body` = ?, `$c_lastUsed` = ?";

        // Values for the ON DUPLICATE KEY parts are repeated
        $values = array_values($commentDigestArray);
        $values[] = $data->getBody();
        $values[] = Util::dateTimeToString($data->getLastUsed());
        return $this->db->execute($q, 'siississssss', ...$values);
    }
}
