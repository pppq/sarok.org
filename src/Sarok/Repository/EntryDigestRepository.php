<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\DB;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\Repository;
use Sarok\Models\FriendType;
use Sarok\Models\EntryDigest;
use DateTime;
use Sarok\Models\AccessType;

final class EntryDigestRepository extends Repository
{
    public const TABLE_NAME = 'cache_entrylist';
    
    public const COLUMN_NAMES = array(
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
    
    public function __construct(DB $db, FriendRepository $friendRepository)
    {
        parent::__construct($db);
        $this->friendRepository = $friendRepository;
    }
    
    private function deleteByIDColumn(string $column, int $value) : int
    {
        $t_cache_entrylist = self::TABLE_NAME;
        $q = "DELETE FROM `${t_cache_entrylist}` WHERE `${column}` = ?";
        return $this->db->execute($q, 'i', $value);
    }
    
    public function deleteById(int $ID) : int
    {
        return $this->deleteByIDColumn(EntryDigest::FIELD_ID, $ID);
    }
    
    public function deleteByOwnerId(int $ownerID) : int
    {
        return $this->deleteByIDColumn(EntryDigest::FIELD_OWNER_ID, $ownerID);
    }
    
    public function deleteLastUsedBefore(DateTime $lastUsed) : int
    {
        $t_cache_entrylist = self::TABLE_NAME;
        $c_lastUsed = EntryDigest::FIELD_LAST_USED;
        
        $q = "DELETE FROM `${t_cache_entrylist}` WHERE `${c_lastUsed}` < ?";

        return $this->db->execute($q, 's', 
            Util::dateTimeToString($lastUsed));
    }
    
    public function updateLastUsed(DateTime $lastUsed, array $IDs) : int
    {
        $t_cache_entrylist = self::TABLE_NAME;
        $c_lastUsed = EntryDigest::FIELD_LAST_USED;
        $c_ID = EntryDigest::FIELD_ID;
        $placeholderList = $this->toPlaceholderList($IDs);
        
        $q = "UPDATE `${t_cache_entrylist}` SET `${c_lastUsed}` = ? WHERE `${c_ID}` IN (${placeholderList})";

        // Introduce an alias, we don't want to copy the array by assignment here
        $values = &$IDs;
        array_unshift($values, Util::dateTimeToString($lastUsed));
        return $this->db->executeWithParams($q, $values);
    }
    
    public function updateAccess(AccessType $access, string $diaryID, array $IDs) : int
    {
        $t_cache_entrylist = self::TABLE_NAME;
        $c_access = EntryDigest::FIELD_ACCESS;
        $c_diaryID = EntryDigest::FIELD_DIARY_ID;
        $c_ID = EntryDigest::FIELD_ID;
        $placeholderList = $this->toPlaceholderList($IDs);
        
        $q = "UPDATE `$t_cache_entrylist` SET `$c_access` = ? WHERE `$c_diaryID` = ? AND `$c_ID` IN ($placeholderList)";

        // Introduce an alias, we don't want to copy the array by assignment here
        $values = &$IDs;
        array_unshift($values, $access->value, $diaryID);
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
        $values = array($ownerID, Util::dateTimeToString($createDate));
        
        // If "friendsOnly" is set, the section is restricted to entries made by friends of the user
        if ($friendsOnly === true) {
            $c_diaryID = EntryDigest::FIELD_DIARY_ID;
            $friendsSubQuery = $this->friendRepository->getDestinationLoginsQuery();
            $friendsOnlyClause = "AND `${c_diaryID}` IN (${friendsSubQuery}) ";
            
            array_push($values, $ownerID, FriendType::FRIEND->value);
        } else {
            $friendsOnlyClause = '';
        }
        
        // Remove banned people from the output
        if (count($bannedIDs) > 0) {
            $c_userID = EntryDigest::FIELD_USER_ID;
            $placeholderList = $this->toPlaceholderList($bannedIDs);
            $bannedClause = "AND `${c_userID}` NOT IN (${placeholderList}) ";
            
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
        $t_cache_entrylist = self::TABLE_NAME;
        $c_ownerID = EntryDigest::FIELD_OWNER_ID;
        $c_createDate = EntryDigest::FIELD_CREATE_DATE;
        
        $q = "SELECT `${columnList}` FROM `${t_cache_entrylist}` ".
             "WHERE `${c_ownerID}` IN (0, ?) AND `${c_createDate}` <= ? ${friendsOnlyClause}${bannedClause}" .
             "ORDER BY `${c_createDate}` DESC LIMIT ?";
        
        // Last parameter is the limit
        $values[] = $limit;
        return $this->db->queryObjectsWithParams($q, EntryDigest::class, $values);
    }
    
    public function save(EntryDigest $entryDigest) : int
    {
        $t_cache_entrylist = self::TABLE_NAME;
        $entryDigestArray = $entryDigest->toArray();
        $insertColumns = array_keys($entryDigestArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $c_lastUsed = EntryDigest::FIELD_LAST_USED;
        
        // FIXME: body is not updated when refreshing the entries cache, however it is updated for comments. Why?
        $q = "INSERT INTO `${t_cache_entrylist}` (`${columnList}`) VALUES (${placeholderList}) ON DUPLICATE KEY UPDATE `${c_lastUsed}` = ?";
        
        // "Last used" value for the ON DUPLICATE KEY part is added to the end
        $values = array(...$entryDigestArray, Util::dateTimeToString($entryDigest->getLastUsed()));
        return $this->db->execute($q, 'iisssssss', ...$values);
    }
}
