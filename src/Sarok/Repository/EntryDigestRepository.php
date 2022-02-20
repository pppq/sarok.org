<?php namespace Sarok\Repository;

use DateTime;
use Sarok\Util;
use Sarok\Service\DB;
use Sarok\Models\Friend;
use Sarok\Models\FriendType;
use Sarok\Models\EntryDigest;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\AbstractRepository;

class EntryDigestRepository extends AbstractRepository
{
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
    
    /** @var FriendRepository */
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
        $t_cache_entrylist = $this->getTableName();
        
        $q = "DELETE FROM `$t_cache_entrylist` WHERE `$column` = ?";
        return $this->db->execute($q, 'i', $value);
    }
    
    public function deleteById(int $ID) : int
    {
        return $this->deleteByColumn(EntryDigest::FIELD_ID, $ID);
    }
    
    public function deleteByOwnerId(int $ownerID) : int
    {
        return $this->deleteByColumn(EntryDigest::FIELD_OWNER_ID, $ownerID);
    }
    
    public function deleteLastUsedBefore(DateTime $lastUsed) : int
    {
        $t_cache_entrylist = $this->getTableName();
        $c_lastUsed = EntryDigest::FIELD_LAST_USED;
        
        $q = "DELETE FROM `$t_cache_entrylist` WHERE `$c_lastUsed` < ?";
        return $this->db->execute($q, 's', Util::dateTimeToString($lastUsed));
    }
    
    public function updateLastUsed(DateTime $lastUsed, array $IDs) : int
    {
        $t_cache_entrylist = $this->getTableName();
        $c_lastUsed = EntryDigest::FIELD_LAST_USED;
        $c_ID = EntryDigest::FIELD_ID;

        // Introduce an alias after saving placeholders based on the original list
        $placeholderList = $this->toPlaceholderList($IDs);
        $values = &$IDs;
        
        // First value in the UPDATE statement is the timestamp
        array_unshift($values, Util::dateTimeToString($lastUsed));
        
        $q = "UPDATE `$t_cache_entrylist` SET `$c_lastUsed` = ? WHERE `$c_ID` IN ($placeholderList)";
        return $this->db->executeWithParams($q, $values);
    }
    
    public function updateAccess(string $access, string $diaryID, array $IDs) : int
    {
        $t_cache_entrylist = $this->getTableName();
        $c_access = EntryDigest::FIELD_ACCESS;
        $c_diaryID = EntryDigest::FIELD_DIARY_ID;
        $c_ID = EntryDigest::FIELD_ID;
        
        // Introduce an alias, we don't want to copy the array by assignment here
        $placeholderList = $this->toPlaceholderList($IDs);
        $values = &$IDs;

        // Prepend parameters: access type and diaryID
        array_unshift($values, $access, $diaryID);
        
        $q = "UPDATE `$t_cache_entrylist` SET `$c_access` = ? WHERE `$c_diaryID` = ? AND `$c_ID` IN ($placeholderList)";
        return $this->db->executeWithParams($q, $values);
    }
    
    public function getMostRecent(
        int $ownerID,
        bool $friendsOnly,
        array $bannedIDs = array(),
        int $limit = 30) : array
    {
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
        int $limit = 30) : array
    {
        $values = array(
            $ownerID, 
            Util::dateTimeToString($createDate)
        );
        
        // If "friendsOnly" is set, the section is restricted to entries made by friends of the user
        $c_diaryID = EntryDigest::FIELD_DIARY_ID;
        if ($friendsOnly === true) {
            $friendsSubQuery = $this->friendRepository->getDestinationLoginsQuery();
            $friendsOnlyClause = "AND `$c_diaryID` IN ($friendsSubQuery) ";
            
            // Parameter 3 (index 2) should be the ownerID again, followed by the association type
            $values[] = $ownerID;
            $values[] = FriendType::FRIEND;
        } else {
            $friendsOnlyClause = '';
        }
        
        // Remove banned people from the output
        $c_userID = EntryDigest::FIELD_USER_ID;
        if (count($bannedIDs) > 0) {
            $placeholderList = $this->toPlaceholderList($bannedIDs);
            $bannedClause = "AND `$c_userID` NOT IN ($placeholderList) ";
            
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
        $t_cache_entrylist = $this->getTableName();
        $c_ownerID = EntryDigest::FIELD_OWNER_ID;
        $c_createDate = EntryDigest::FIELD_CREATE_DATE;
        
        $q = "SELECT `$columnList` FROM `$t_cache_entrylist` ".
             "WHERE `$c_ownerID` IN (0, ?) AND `$c_createDate` <= ? {$friendsOnlyClause}{$bannedClause}" .
             "ORDER BY `$c_createDate` DESC LIMIT ?";
        
        // Last parameter is the limit
        $values[] = $limit;
        return $this->db->queryObjectsWithParams($q, EntryDigest::class, $values);
    }
    
    public function save(EntryDigest $entryDigest) : int
    {
        $t_cache_entrylist = $this->getTableName();
        $entryDigestArray = $data->toArray();
        $insertColumns = array_keys($entryDigestArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $c_lastUsed = EntryDigest::FIELD_LAST_USED;
        
        // FIXME: body is not updated when refreshing the entries cache, however it is updated for comments. Why?
        $q = "INSERT INTO `$t_cache_entrylist` (`$columnList`) VALUES ($placeholderList) ON DUPLICATE KEY UPDATE `$c_lastUsed` = ?";
        // Value for the ON DUPLICATE KEY part is repeated
        $values = array_values($entryDigestArray);
        $values[] = Util::dateTimeToString($data->getLastUsed());
        return $this->db->execute($q, 'iisssssss', ...$values);
    }
}
