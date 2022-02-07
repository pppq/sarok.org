<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Models\CommentDigest;
use Sarok\Service\DB;
use DateTime;

class CommentDigestRepository extends AbstractRepository {

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
    
    public function __construct(DB $db) {
        parent::__construct($db);
    }
    
    protected function getTableName() : string {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array {
        return self::COLUMN_NAMES;
    }
    
    private function deleteByColumn(string $column, int $value) : int {
        $cache_commentlist = $this->getTableName();
        
        $q = "DELETE FROM `$cache_commentlist` WHERE `$column` = ?";
        return $this->db->execute($q, 'i', $value);
    }
    
    public function deleteById(int $ID) : int {
        return $this->deleteByColumn(CommentDigest::FIELD_ID, $ID);
    }
    
    public function deleteByEntryId(int $entryID) : int {
        return $this->deleteByColumn(CommentDigest::FIELD_ENTRY_ID, $entryID);
    }
    
    public function deleteByOwnerId(int $ownerID) : int {
        return $this->deleteByColumn(CommentDigest::FIELD_OWNER_ID, $ownerID);
    }
    
    public function deleteByCategoryAndOwnerId(string $category, int $ownerID) : int {
        $cache_commentlist = $this->getTableName();
        $categoryColumn = CommentDigest::FIELD_CATEGORY;
        $ownerIDColumn = CommentDigest::FIELD_OWNER_ID;
        
        $q = "DELETE FROM `$cache_commentlist` WHERE `$categoryColumn` = ? AND `$ownerIDColumn` = ?";
        return $this->db->execute($q, 'si', $category, $ownerID);
    }
    
    public function deleteLastUsedBefore(DateTime $lastUsed) : int {
        $cache_commentlist = $this->getTableName();
        $lastUsedColumn = CommentDigest::FIELD_LAST_USED;
        
        $q = "DELETE FROM `$cache_commentlist` WHERE `$lastUsedColumn` < ?";
        $lastUsedString = Util::dateTimeToString($lastUsed);
        return $this->db->execute($q, 's', $lastUsedString);
    }
    
    public function updateLastUsed(DateTime $lastUsed, string $category, array $IDs, string $friendsOfId = '') : int {
        // Introduce an alias after saving placeholders based on the original list
        $placeholderList = $this->toPlaceholderList($IDs);
        $values = &$IDs;
        
        // First two values in the UPDATE statement is the timestamp and the category
        array_unshift($values, Util::dateTimeToString($lastUsed), $category);
        
        // If last parameter is set, the "all comments" section is restricted to comments made by friends of the user
        if ($category === CommentDigest::CATEGORY_ALL_COMMENTS && strlen($friendsOfId) > 0) {
            // FIXME: replace table and field names with constants from Friend and User models and repositories
            // FIXME: all lists (friends, bans, follows) were consulted here, shouldn't that be restricted to friends only?
            $friendsSubQuery = "SELECT `login` FROM `friends` LEFT JOIN `users` ON `friends`.`userID` = `users`.`ID` WHERE `friendOf` = ?";
            
            $diaryID = CommentDigest::FIELD_DIARY_ID;
            $friendsOnlyClause = "AND `$diaryID` IN ($friendsSubQuery)";
            
            // Optional third value (at index 2) is the user ID when given
            array_splice($values, 2, 0, $friendsOfId);
        } else {
            $friendsOnlyClause = '';
        }

        $cache_commentlist = $this->getTableName();
        $lastUsedColumn = CommentDigest::FIELD_LAST_USED;
        $categoryColumn = CommentDigest::FIELD_CATEGORY;
        $ID = CommentDigest::FIELD_ID;
        
        $q = "UPDATE `$cache_commentlist` SET `$lastUsedColumn` = ? WHERE `$categoryColumn` = ? $friendsOnlyClause AND `$ID` IN ($placeholderList)";
        return $this->db->executeWithParams($q, $values);
    }
    
    public function updateAccess(string $access, array $entryIDs) : int {
        $cache_commentlist = $this->getTableName();
        $accessColumn = CommentDigest::FIELD_ACCESS;
        $entryIDColumn = CommentDigest::FIELD_ENTRY_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        
        $q = "UPDATE `$cache_commentlist` SET `$accessColumn` = ? WHERE `$entryIDColumn` IN ($placeholderList)";
        
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
        int $limit = 30) : array {
        
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
        int $limit = 30) : array {
        
        // Get the shared comment digests as well for the "all comments" section (ownerID is 0 in that case)
        $ownerIDColumn = CommentDigest::FIELD_OWNER_ID;
        if ($category === CommentDigest::CATEGORY_ALL_COMMENTS) {
            $ownerClause = "`$ownerIDColumn` IN (0, ?)";
        } else {
            $ownerClause = "`$ownerIDColumn` = ?";
        }

        $values = array($ownerID, $category, Util::dateTimeToString($createDate));
        
        // If "friendsOnly" is set, the "all comments" section is restricted to comments made by friends of the user
        $diaryID = CommentDigest::FIELD_DIARY_ID;
        if ($category === CommentDigest::CATEGORY_ALL_COMMENTS && $friendsOnly === true) {
            // FIXME: replace table and field names with constants from Friend and User model repositories
            // FIXME: all lists (friends, bans, reads) are consulted here
            $friendsSubQuery = "SELECT `login` FROM `friends` LEFT JOIN `users` ON `friends`.`userID` = `users`.`ID` WHERE `friendOf` = ?";
            $friendsOnlyClause = "AND `$diaryID` IN ($friendsSubQuery) ";
            
            // Parameter 4 (index 3) should be the ownerID again
            $values[] = $ownerID;
        } else {
            $friendsOnlyClause = '';
        }
        
        /* 
         * Remove banned people from the output, unless the category is "my comments"; you should be able to see 
         * your own comments even if you banned or got banned by the blog owner.
         */
        $userID = CommentDigest::FIELD_USER_ID;
        if ($category !== CommentDigest::CATEGORY_MY_COMMENTS && count($bannedIDs) > 0) {
            $placeholderList = $this->toPlaceholderList($bannedIDs);
            $bannedClause = "AND `$userID` NOT IN ($placeholderList) AND `$diaryID` NOT IN ($placeholderList) ";
            
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
        $cache_commentlist = $this->getTableName();
        $categoryColumn = CommentDigest::FIELD_CATEGORY;
        $createDateColumn = CommentDigest::FIELD_CREATE_DATE;
        
        $q = "SELECT `$columnList` FROM `$cache_commentlist` ".
             "WHERE $ownerClause AND `$categoryColumn` = ? AND `$createDateColumn` <= ? {$friendsOnlyClause}{$bannedClause}" .
             "ORDER BY `$createDateColumn` DESC LIMIT ?";
        
        // Last parameter is the limit
        $values[] = $limit;
        return $this->db->queryObjectsWithParams($q, CommentDigest::class, $values);
    }
    
    public function upsert(CommentDigest $data) : int {
        $values = $data->toArray();
        // Values for the ON DUPLICATE KEY parts are repeated
        $values[] = $data->getBody();
        $values[] = Util::dateTimeToString($data->getLastUsed());

        $cache_commentlist = $this->getTableName();
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $body = CommentDigest::FIELD_BODY;
        $lastUsed = CommentDigest::FIELD_LAST_USED;
        
        $q = "INSERT INTO `$cache_commentlist`(`$columnList`) VALUES ($placeholderList) ON DUPLICATE KEY UPDATE `$body` = ?, `$lastUsed` = ?";
        // Last two 's' stand for body and lastUsed, see above!
        return $this->db->execute($q, 'siississssss', ...$values);
    }
}
