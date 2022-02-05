<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Models\EntryDigest;
use Sarok\Service\DB;
use DateTime;

class EntryDigestRepository extends AbstractRepository {

    const TABLE_NAME = 'cache_entrylist';
    
    public function __construct(DB $db) {
        parent::__construct($db);
    }
    
    public function getTableName() : string {
        return self::TABLE_NAME;
    }
    
    public function getColumnNames() : array {
        return array(
            EntryDigest::FIELD_ID,
            EntryDigest::FIELD_OWNER_ID,
            EntryDigest::FIELD_USER_ID,
            EntryDigest::FIELD_DIARY_ID,
            EntryDigest::FIELD_CREATE_DATE,
            EntryDigest::FIELD_ACCESS,
            EntryDigest::FIELD_BODY,
            EntryDigest::FIELD_LAST_USED,
        );
    }
    
    private function deleteByColumn(string $cache_entrylist, string $column, int $value) : int {
        $q = "DELETE FROM `$cache_entrylist` WHERE `$column` = ?";
        return $this->db->execute($q, 'i', $value);
    }
    
    public function deleteById(int $ID) : int {
        return $this->deleteByColumn(self::TABLE_NAME, EntryDigest::FIELD_ID, $ID);
    }
    
    public function deleteByOwnerId(int $ownerID) : int {
        return $this->deleteByColumn(self::TABLE_NAME, EntryDigest::FIELD_OWNER_ID, $ownerID);
    }
    
    public function deleteLastUsedBefore(DateTime $lastUsed) : int {
        $cache_entrylist = self::TABLE_NAME;
        $lastUsedColumn = EntryDigest::FIELD_LAST_USED;
        
        $q = "DELETE FROM `$cache_entrylist` WHERE `$lastUsedColumn` < ?";
        $lastUsedString = Util::dateTimeToString($lastUsed);
        return $this->db->execute($q, 's', $lastUsedString);
    }
    
    public function updateLastUsed(DateTime $lastUsed, array $IDs) : int {
        $cache_entrylist = self::TABLE_NAME;
        $lastUsedColumn = EntryDigest::FIELD_LAST_USED;
        $ID = EntryDigest::FIELD_ID;

        // Introduce an alias after saving placeholders based on the original list
        $placeholderList = $this->toPlaceholderList($IDs);
        $values = &$IDs;
        
        // First value in the UPDATE statement is the timestamp
        array_unshift($values, Util::dateTimeToString($lastUsed));

        $q = "UPDATE `$cache_entrylist` SET `$lastUsedColumn` = ? WHERE `$ID` IN ($placeholderList)";
        return $this->db->executeWithParams($q, $values);
    }
    
    public function updateAccess(string $access, string $diaryID, array $IDs) : int {
        $cache_entrylist = self::TABLE_NAME;
        $accessColumn = EntryDigest::FIELD_ACCESS;
        $diaryIDColumn = EntryDigest::FIELD_DIARY_ID;
        $IDColumn = EntryDigest::FIELD_ID;
        
        $placeholderList = $this->toPlaceholderList($IDs);
        $q = "UPDATE `$cache_entrylist` SET `$accessColumn` = ? WHERE `$diaryIDColumn` = ? AND `$IDColumn` IN ($placeholderList)";
        
        // Introduce an alias, we don't want to copy the array by assignment here
        $values = &$IDs;
        
        // Prepend parameters: access type and diaryID
        array_unshift($values, $access, $diaryID);
        return $this->db->executeWithParams($q, $values);
    }
    
    public function getMostRecent(
        int $ownerID,
        bool $friendsOnly, 
        array $bannedIDs = array(),
        int $limit = 30) : array {
        
        return $this->getMostRecentBefore(
            $ownerID,
            $friendsOnly,
            Util::utcDateTimeFromString(), 
            $bannedIDs,
            $limit);
    }
    
    public function getMostRecentBefore(
        int $ownerID,
        bool $friendsOnly, 
        DateTime $createDate, 
        array $bannedIDs = array(),
        int $limit = 30) : array {
        
        // XXX: not all fields are populated
        $selectColumns = array(
            EntryDigest::FIELD_ID,
            EntryDigest::FIELD_USER_ID,
            EntryDigest::FIELD_DIARY_ID,
            EntryDigest::FIELD_CREATE_DATE,
            EntryDigest::FIELD_ACCESS,
            EntryDigest::FIELD_BODY,
        );
        
        $columnList = $this->toColumnList($selectColumns);
        $cache_entrylist = self::TABLE_NAME;
        $createDateColumn = EntryDigest::FIELD_CREATE_DATE;
        $diaryID = EntryDigest::FIELD_DIARY_ID;
        $userID = EntryDigest::FIELD_USER_ID;
        $ownerIDColumn = EntryDigest::FIELD_OWNER_ID;
        
        $values = array($ownerID, Util::dateTimeToString($createDate));
        
        // If "friendsOnly" is set, the section is restricted to entries made by friends of the user
        if ($friendsOnly === true) {
            // FIXME: replace table and field names with constants from Friend and User model repositories
            // FIXME: all lists (friends, bans, reads) are consulted here
            $friendsSubQuery = "SELECT `login` FROM `friends` LEFT JOIN `users` ON `friends`.`userID` = `users`.`ID` WHERE `friendOf` = ?";
            $friendsOnlyClause = "AND `$diaryID` IN ($friendsSubQuery) ";
            
            // Parameter 3 (index 2) should be the ownerID again
            $values[] = $ownerID;
        } else {
            $friendsOnlyClause = '';
        }
        
        // Remove banned people from the output
        if (count($bannedIDs) > 0) {
            $placeholderList = $this->toPlaceholderList($bannedIDs);
            $bannedClause = "AND `$userID` NOT IN ($placeholderList) ";
            
            // Parameters 4 and up (index 3) should be the banned ID list
            array_push($values, ...$bannedIDs);
        } else {
            $bannedClause = '';
        }
        
        $q = "SELECT `$columnList` FROM `$cache_entrylist` ".
             "WHERE `$ownerIDColumn` IN (0, ?) AND `$createDateColumn` <= ? {$friendsOnlyClause}{$bannedClause}" .
             "ORDER BY `$createDateColumn` DESC LIMIT ?";
        
        // Last parameter is the limit
        $values[] = $limit;
        return $this->db->queryObjectsWithParams($q, EntryDigest::class, $values);
    }
    
    public function upsert(EntryDigest $data) : int {
        $cache_entrylist = self::TABLE_NAME;
        $lastUsed = EntryDigest::FIELD_LAST_USED;
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $values = $data->toArray();
        // Value for the ON DUPLICATE KEY part is repeated
        $values[] = Util::dateTimeToString($data->getLastUsed());
        
        // FIXME: body is not updated when refreshing the entries cache, however it is updated for comments. Why?
        $q = "INSERT INTO `$cache_entrylist`(`$columnList`) VALUES ($placeholderList) ON DUPLICATE KEY UPDATE `$lastUsed` = ?";
        // Last 's' stands for lastUsed, see above!
        return $this->db->execute($q, 'iisssssss', ...$values);
    }
}
