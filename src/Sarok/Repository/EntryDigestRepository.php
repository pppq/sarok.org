<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Models\EntryDigest;
use Sarok\Service\DB;
use DateTime;
use Sarok\Models\Friend;

class EntryDigestRepository extends AbstractRepository {

    const TABLE_NAME = 'cache_entrylist';
    
    private const COLUMN_NAMES = array(
        EntryDigest::FIELD_ID,
        EntryDigest::FIELD_OWNER_ID,
        EntryDigest::FIELD_USER_ID,
        EntryDigest::FIELD_DIARY_ID,
        EntryDigest::FIELD_CREATE_DATE,
        EntryDigest::FIELD_ACCESS,
        EntryDigest::FIELD_BODY,
        EntryDigest::FIELD_LAST_USED,
    );
    
    private FriendRepository $friendRepository;
    
    public function __construct(DB $db, FriendRepository $friendRepository) {
        parent::__construct($db);
        $this->friendRepository = $friendRepository;
    }
    
    protected function getTableName() : string {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array {
        return self::COLUMN_NAMES;
    }
    
    private function deleteByColumn(string $column, int $value) : int {
        $cache_entrylist = $this->getTableName();
        
        $q = "DELETE FROM `$cache_entrylist` WHERE `$column` = ?";
        return $this->db->execute($q, 'i', $value);
    }
    
    public function deleteById(int $ID) : int {
        return $this->deleteByColumn(EntryDigest::FIELD_ID, $ID);
    }
    
    public function deleteByOwnerId(int $ownerID) : int {
        return $this->deleteByColumn(EntryDigest::FIELD_OWNER_ID, $ownerID);
    }
    
    public function deleteLastUsedBefore(DateTime $lastUsed) : int {
        $cache_entrylist = $this->getTableName();
        $lastUsedColumn = EntryDigest::FIELD_LAST_USED;
        
        $q = "DELETE FROM `$cache_entrylist` WHERE `$lastUsedColumn` < ?";
        $lastUsedString = Util::dateTimeToString($lastUsed);
        return $this->db->execute($q, 's', $lastUsedString);
    }
    
    public function updateLastUsed(DateTime $lastUsed, array $IDs) : int {
        $cache_entrylist = $this->getTableName();
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
        $cache_entrylist = $this->getTableName();
        $accessColumn = EntryDigest::FIELD_ACCESS;
        $diaryIDColumn = EntryDigest::FIELD_DIARY_ID;
        $IDColumn = EntryDigest::FIELD_ID;
        
        // Introduce an alias, we don't want to copy the array by assignment here
        $placeholderList = $this->toPlaceholderList($IDs);
        $values = &$IDs;

        // Prepend parameters: access type and diaryID
        array_unshift($values, $access, $diaryID);
        
        $q = "UPDATE `$cache_entrylist` SET `$accessColumn` = ? WHERE `$diaryIDColumn` = ? AND `$IDColumn` IN ($placeholderList)";
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
        
        $values = array($ownerID, Util::dateTimeToString($createDate));
        
        // If "friendsOnly" is set, the section is restricted to entries made by friends of the user
        $diaryID = EntryDigest::FIELD_DIARY_ID;
        if ($friendsOnly === true) {
            $friendsSubQuery = $friendsSubQuery = $this->friendRepository->getDestinationLoginsQuery();
            $friendsOnlyClause = "AND `$diaryID` IN ($friendsSubQuery) ";
            
            // Parameter 3 (index 2) should be the ownerID again, followed by the association type
            $values[] = $ownerID;
            $values[] = Friend::TYPE_FRIEND;
        } else {
            $friendsOnlyClause = '';
        }
        
        // Remove banned people from the output
        $userID = EntryDigest::FIELD_USER_ID;
        if (count($bannedIDs) > 0) {
            $placeholderList = $this->toPlaceholderList($bannedIDs);
            $bannedClause = "AND `$userID` NOT IN ($placeholderList) ";
            
            // Parameters 4 and up (index 3) should be the banned ID list
            array_push($values, ...$bannedIDs);
        } else {
            $bannedClause = '';
        }
        
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
        $cache_entrylist = $this->getTableName();
        $ownerIDColumn = EntryDigest::FIELD_OWNER_ID;
        $createDateColumn = EntryDigest::FIELD_CREATE_DATE;
        
        $q = "SELECT `$columnList` FROM `$cache_entrylist` ".
             "WHERE `$ownerIDColumn` IN (0, ?) AND `$createDateColumn` <= ? {$friendsOnlyClause}{$bannedClause}" .
             "ORDER BY `$createDateColumn` DESC LIMIT ?";
        
        // Last parameter is the limit
        $values[] = $limit;
        return $this->db->queryObjectsWithParams($q, EntryDigest::class, $values);
    }
    
    public function upsert(EntryDigest $data) : int {
        $values = $data->toArray();
        // Value for the ON DUPLICATE KEY part is repeated
        $values[] = Util::dateTimeToString($data->getLastUsed());

        $cache_entrylist = $this->getTableName();
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $lastUsed = EntryDigest::FIELD_LAST_USED;
        
        // FIXME: body is not updated when refreshing the entries cache, however it is updated for comments. Why?
        $q = "INSERT INTO `$cache_entrylist`(`$columnList`) VALUES ($placeholderList) ON DUPLICATE KEY UPDATE `$lastUsed` = ?";
        // Last 's' stands for lastUsed, see above!
        return $this->db->execute($q, 'iisssssss', ...$values);
    }
}
