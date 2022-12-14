<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\DB;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\AbstractRepository;
use Sarok\Models\FriendType;
use Sarok\Models\CommentDigestCategory;
use Sarok\Models\CommentDigest;
use Sarok\Models\AccessType;
use DateTime;

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
    
    public function deleteByCategoryAndOwnerId(CommentDigestCategory $category, int $ownerID) : int
    {
        $t_cache_commentlist = $this->getTableName();
        $c_category = CommentDigest::FIELD_CATEGORY;
        $c_ownerID = CommentDigest::FIELD_OWNER_ID;
        
        $q = "DELETE FROM `$t_cache_commentlist` WHERE `$c_category` = ? AND `$c_ownerID` = ?";
        return $this->db->execute($q, 'si', $category->value, $ownerID);
    }
    
    public function deleteLastUsedBefore(DateTime $lastUsed) : int
    {
        $t_cache_commentlist = $this->getTableName();
        $c_lastUsed = CommentDigest::FIELD_LAST_USED;
        
        $q = "DELETE FROM `$t_cache_commentlist` WHERE `$c_lastUsed` < ?";
        return $this->db->execute($q, 's', Util::dateTimeToString($lastUsed));
    }
    
    public function updateLastUsed(DateTime $lastUsed, CommentDigestCategory $category, array $IDs, int $friendOf = 0) : int
    {
        // First two values in the UPDATE statement is the timestamp and the category
        $values = array(
            Util::dateTimeToString($lastUsed), 
            $category->value,
        );
        
        // If last parameter is set, the "all comments" section is restricted to comments made by friends of the user
        if ($category === CommentDigestCategory::COMMENTS && $friendOf > 0) {
            $friendsSubQuery = $this->friendRepository->getDestinationLoginsQuery();
            $c_diaryID = CommentDigest::FIELD_DIARY_ID;
            $friendsOnlyClause = "AND `$c_diaryID` IN ($friendsSubQuery) ";
            $friendType = FriendType::FRIEND;
            
            $values[] = $friendOf;
            $values[] = $friendType->value;
        } else {
            $friendsOnlyClause = '';
        }

        $t_cache_commentlist = $this->getTableName();
        $c_lastUsed = CommentDigest::FIELD_LAST_USED;
        $c_category = CommentDigest::FIELD_CATEGORY;
        $c_ID = CommentDigest::FIELD_ID;
        $placeholderList = $this->toPlaceholderList($IDs);
        
        $q = "UPDATE `$t_cache_commentlist` SET `$c_lastUsed` = ? WHERE `$c_category` = ? $friendsOnlyClause" .
            "AND `$c_ID` IN ($placeholderList)";
        
        array_push($values, ...$IDs);
        return $this->db->executeWithParams($q, $values);
    }
    
    public function updateAccess(AccessType $access, array $entryIDs) : int
    {
        $t_cache_commentlist = $this->getTableName();
        $c_access = CommentDigest::FIELD_ACCESS;
        $c_entryID = CommentDigest::FIELD_ENTRY_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        
        $q = "UPDATE `$t_cache_commentlist` SET `$c_access` = ? WHERE `$c_entryID` IN ($placeholderList)";
        
        // Prepend first parameter (access type)
        $values = array($access->value, ...$entryIDs);
        return $this->db->executeWithParams($q, $values);
    }
    
    public function getMostRecent(
        CommentDigestCategory $category, 
        int $ownerID, 
        bool $friendsOnly, 
        array $bannedLogins = array(), 
        int $limit = 30) : array
    {
        return $this->getMostRecentBefore(
            $category,
            $ownerID,
            $friendsOnly,
            Util::utcDateTimeFromString(),
            $bannedLogins,
            $limit);
    }
    
    public function getMostRecentBefore(
        CommentDigestCategory $category,
        int $ownerID,
        bool $friendsOnly,
        DateTime $createDate,
        array $bannedLogins = array(),
        int $limit = 30) : array
    {
        // Get the shared comment digests as well for the "all comments" section (ownerID is 0 in that case)
        $c_ownerID = CommentDigest::FIELD_OWNER_ID;
        if ($category === CommentDigestCategory::COMMENTS) {
            $ownerClause = "`$c_ownerID` IN (0, ?)";
        } else {
            $ownerClause = "`$c_ownerID` = ?";
        }

        $values = array(
            $ownerID,
            $category->value,
            Util::dateTimeToString($createDate)
        );
        
        // If "friendsOnly" is set, the "all comments" section is restricted to comments made by friends of the user
        $c_diaryID = CommentDigest::FIELD_DIARY_ID;
        if ($category === CommentDigestCategory::COMMENTS && $friendsOnly === true) {
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
        if ($category !== CommentDigestCategory::MY_COMMENTS && count($bannedLogins) > 0) {
            $placeholderList = $this->toPlaceholderList($bannedLogins);
            $bannedClause = "AND `$c_userID` NOT IN ($placeholderList) AND `$c_diaryID` NOT IN ($placeholderList) ";
            
            // Parameters 5 and up (index 4+) should be the banned login list, but twice!
            array_push($values, ...$bannedLogins, ...$bannedLogins);
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
        $values[] = $commentDigest->getBody();
        $values[] = Util::dateTimeToString($commentDigest->getLastUsed());
        return $this->db->execute($q, 'siississssss', ...$values);
    }
}
