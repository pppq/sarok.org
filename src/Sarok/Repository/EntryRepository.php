<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Service\DB;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\CommentRepository;
use Sarok\Repository\AbstractRepository;
use Sarok\Models\FriendType;
use Sarok\Models\Friend;
use Sarok\Models\Entry;
use DateTime;

class EntryRepository extends AbstractRepository
{
    const TABLE_NAME = 'entries';
    
    private const COLUMN_NAMES = array(
        Entry::FIELD_ID,
        Entry::FIELD_DIARY_ID,
        Entry::FIELD_USER_ID,
        Entry::FIELD_CREATE_DATE,
        Entry::FIELD_MODIFY_DATE,
        Entry::FIELD_ACCESS,
        Entry::FIELD_COMMENTS,
        Entry::FIELD_TITLE,
        Entry::FIELD_BODY_1,
        Entry::FIELD_BODY_2,
        Entry::FIELD_NUM_COMMENTS,
        Entry::FIELD_LAST_COMMENT,
        Entry::FIELD_LAST_VISIT,
        Entry::FIELD_IS_TERMINATED,
        Entry::FIELD_MODERATOR_COMMENT,
        Entry::FIELD_CATEGORY,
        Entry::FIELD_DAY_DATE,
        Entry::FIELD_RSS_URL,
        Entry::FIELD_POS_X,
        Entry::FIELD_POS_Y,
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
    
    public function getByID(int $entryID) : ?Entry
    {
        $t_entries = $this->getTableName();
        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Entry::FIELD_ID;

        $q = "SELECT `$columnList` FROM `$t_entries` WHERE `$c_ID` = ? LIMIT 1";
        return $this->db->querySingleObject($q, Entry::class, 'i', $entryID);
    }

    public function getByIDAndDiaryID(int $entryID, int $diaryID) : ?Entry
    {
        $t_entries = $this->getTableName();
        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Entry::FIELD_ID;
        $c_diaryID = Entry::FIELD_DIARY_ID;

        $q = "SELECT `$columnList` FROM `$t_entries` WHERE `$c_ID` = ? AND `$c_diaryID` = ? LIMIT 1";
        return $this->db->querySingleObject($q, Entry::class, 'ii', $entryID, $diaryID);
    }
    
    public function getNonTerminatedByID(int $entryID) : ?Entry
    {
        $t_entries = $this->getTableName();
        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Entry::FIELD_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;

        $q = "SELECT `$columnList` FROM `$t_entries` WHERE `$c_ID` = ? AND `$c_isTerminated` = 'N' LIMIT 1";
        return $this->db->querySingleObject($q, Entry::class, 'i', $entryID);
    }

    public function getCommentCountByEntryIds(array $entryIDs) : array
    {
        $t_entries = $this->getTableName();
        $c_numComments = Entry::FIELD_NUM_COMMENTS;
        $c_ID = Entry::FIELD_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        
        $q = "SELECT `$c_ID`, `$c_numComments` FROM `$t_entries` WHERE `$c_ID` IN ($placeholderList)";
        $result = $this->db->queryWithParams($q, $entryIDs);
        
        $commentCount = array();
        while ($row = $result->fetch_row()) {
            $commentCount[$row[0]] = (int) $row[1];
        }

        return $commentCount;
    }

    public function getUserID(int $entryID) : int
    {
        $t_entries = $this->getTableName();
        $c_userID = Entry::FIELD_USER_ID;
        $c_ID = Entry::FIELD_ID;

        $q = "SELECT `$c_userID` FROM `$t_entries` WHERE `$c_ID` = ? LIMIT 1";
        return $this->db->queryInt($q, -1, 'i', $entryID);
    }
    
    private function updateTerminated(int $entryID, bool $terminated) : int
    {
        $t_entries = $this->getTableName();
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_ID = Entry::FIELD_ID;
        
        $q = "UPDATE `$t_entries` SET `$c_isTerminated` = ? WHERE `$c_ID` = ?";
        return $this->db->execute($q, 'si', Util::boolToYesNo($terminated), $entryID);
    }
    
    public function updateLastVisit(int $entryID, DateTime $lastVisit) : int
    {
        $t_entries = $this->getTableName();
        $c_lastVisit = Entry::FIELD_LAST_VISIT;
        $c_ID = Entry::FIELD_ID;
        
        $q = "UPDATE `$t_entries` SET `$c_lastVisit` = ? WHERE `$c_ID` = ? LIMIT 1";
        return $this->db->execute($q, 'si', Util::dateTimeToString($lastVisit), $entryID);
    }
    
    public function updateCommentStats(int $entryID, int $numComments, DateTime $lastComment) : int
    {
        $t_entries = $this->getTableName();
        $c_numComments = Entry::FIELD_NUM_COMMENTS;
        $c_lastComment = Entry::FIELD_LAST_COMMENT;
        $c_ID = Entry::FIELD_ID;
        
        $q = "UPDATE `$t_entries` SET `$c_numComments` = ?, `$c_lastComment` = ? WHERE `$c_ID` = ? LIMIT 1";
        return $this->db->execute($q, 'isi', 
            $numComments, 
            Util::dateTimeToString($lastComment), 
            $entryID);
    }

    private function updateAccess(
        string $column, 
        string $access, 
        DateTime $modifyDate, 
        int $diaryID, 
        array &$entryIDs) : int
    {
        $t_entries = $this->getTableName();
        $c_modifyDate = Entry::FIELD_MODIFY_DATE;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_ID = Entry::FIELD_ID;
        
        // Introduce an alias, we don't want to copy the array by assignment here
        $placeholderList = $this->toPlaceholderList($entryIDs);
        $values = &$entryIDs;

        // Prepend parameters: access type, modify date and diaryID
        array_unshift($values, 
            $access, 
            Util::dateTimeToString($modifyDate), 
            $diaryID);
        
        $q = "UPDATE `$t_entries` SET `$column` = ?, `$c_modifyDate` = ? " .
            "WHERE `$c_diaryID` = ? AND `$c_isTerminated` = 'N' AND `$c_ID` IN ($placeholderList)";

        return $this->db->executeWithParams($q, $values);
    }

    public function updateEntryAccess(string $access, DateTime $modifyDate, int $diaryID, array $entryIDs) : int
    {
        return $this->updateAccess(Entry::FIELD_ACCESS, $access, $diaryID, $entryIDs);
    }

    public function updateCommentAccess(string $access, DateTime $modifyDate, int $diaryID, array $entryIDs) : int
    {
        return $this->updateAccess(Entry::FIELD_COMMENT_ACCESS, $access, $diaryID, $entryIDs);
    }

    public function update(Entry $entry, DateTime $modifyDate) : int
    {
        $t_entries = $this->getTableName();
        $updateColumns = array(
            Entry::FIELD_ACCESS,
            Entry::FIELD_COMMENTS,
            Entry::FIELD_TITLE,
            Entry::FIELD_BODY_1,
            Entry::FIELD_BODY_2,
            Entry::FIELD_DIARY_ID, // entries can be moved between diaries
            Entry::FIELD_POS_X,
            Entry::FIELD_POS_Y,
            Entry::FIELD_MODIFY_DATE,
        );

        $assignments = array_map(function ($column) { return "`$column` = ?"; }, $updateColumns);
        $assignmentList = implode(', ', $assignments);
        $q = "UPDATE `$t_entries` SET $assignmentList WHERE `$c_ID` = ? AND `$c_isTerminated` = 'N' LIMIT 1";

        $entry->setModifyDate($modifyDate);
        
        $values = $entry->toArray();
        // Keep values only for columns which will be updated
        $filteredValues = array_intersect_key($values, array_flip($updateColumns));
        return $this->db->executeWithParams($q, $filteredValues);
    }

    public function save(Entry $entry) : int
    {
        $t_entries = $this->getTableName();
        $entryArray = $data->toArray();
        $insertColumns = array_keys($entryArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$t_entries` (`$columnList`) VALUES ($placeholderList)";
        $values = array_values($entryArray);

        if ($comment->getID() < 0) {
            // Need auto-generated ID - don't send in the negative value
            array_shift($columnList);
            array_shift($placeholderList);
            array_shift($values);
            
            $modifiedRows = $this->db->execute($q, 'iisssssssissssissss', ...$values);
            if ($modifiedRows > 0) {
                $entry->setID($this->db->getLastInsertID());
            }
            
            return $modifiedRows;
        } else {
            return $this->db->execute($q, 'iiisssssssissssissss', ...$values);
        }
    }
}
