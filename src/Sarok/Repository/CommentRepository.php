<?php declare(strict_types=1);

namespace Sarok\Repository;

use mysqli_result;
use Sarok\Util;
use Sarok\DB;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\EntryRepository;
use Sarok\Repository\EntryAccessRepository;
use Sarok\Repository\Repository;
use Sarok\Models\FriendType;
use Sarok\Models\Entry;
use Sarok\Models\CommentListType;
use Sarok\Models\Comment;
use Sarok\Models\AccessType;
use InvalidArgumentException;
use DateTime;

final class CommentRepository extends Repository
{
    public const TABLE_NAME = 'comments';
    
    public const COLUMN_NAMES = array(
        Comment::FIELD_ID,
        Comment::FIELD_IS_TERMINATED,
        Comment::FIELD_PARENT_ID,
        Comment::FIELD_ENTRY_ID,
        Comment::FIELD_USER_ID,
        Comment::FIELD_CREATE_DATE,
        Comment::FIELD_BODY,
        Comment::FIELD_IP,
        Comment::FIELD_DAY_DATE,
        Comment::FIELD_RATE,
    );
    
    private FriendRepository $friendRepository;
    private EntryAccessRepository $entryAccessRepository;
    private EntryRepository $entryRepository;

    public function __construct(
        DB $db, 
        FriendRepository $friendRepository,
        EntryAccessRepository $entryAccessRepository,
        EntryRepository $entryRepository
    ) {
        parent::__construct($db);
        $this->friendRepository = $friendRepository;
        $this->entryAccessRepository = $entryAccessRepository;
        $this->entryRepository = $entryRepository;
    }
    
    private function updateTerminated(string $column, int $value, bool $terminated) : int
    {
        $t_comments = self::TABLE_NAME;
        $c_isTerminated = Comment::FIELD_IS_TERMINATED;
        
        $q = "UPDATE `${t_comments}` SET `${c_isTerminated}` = ? WHERE `${column}` = ?";

        return $this->db->execute($q, 'si', 
            Util::boolToYesNo($terminated), $value);
    }
    
    public function updateTerminatedByID(int $ID, bool $terminated = true) : int
    {
        return $this->updateTerminated(Comment::FIELD_ID, $ID, $terminated);
    }
    
    public function updateTerminatedByEntryID(int $entryID, bool $terminated = true) : int
    {
        return $this->updateTerminated(Comment::FIELD_ENTRY_ID, $entryID, $terminated);
    }
    
    public function updateRate(int $ID, int $rate) : int
    {
        $t_comments = self::TABLE_NAME;
        $c_rate = Comment::FIELD_RATE;
        $c_ID = Comment::FIELD_ID;
        
        $q = "UPDATE `${t_comments}` SET `${c_rate}` = ? WHERE `${c_ID}` = ? LIMIT 1";
        
        return $this->db->execute($q, 'ii', 
            $rate, $ID);
    }
    
    public function getByID(int $ID) : ?Comment
    {
        $t_comments = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Comment::FIELD_ID;

        $q = "SELECT `${columnList}` FROM `${t_comments}` WHERE `${c_ID}` = ? LIMIT 1";

        return $this->db->querySingleObject($q, Comment::class, 'i', 
            $ID);
    }
    
    public function getNonTerminatedByID(int $ID) : ?Comment
    {
        $t_comments = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Comment::FIELD_ID;
        $c_isTerminated = Comment::FIELD_IS_TERMINATED;

        $q = "SELECT `${columnList}` FROM `${t_comments}` " .
            "WHERE `${c_ID}` = ? AND `${c_isTerminated}` = 'N' LIMIT 1";

        return $this->db->querySingleObject($q, Comment::class, 'i', 
            $ID);
    }
    
    public function getByEntryID(int $entryID) : array
    {
        $t_comments = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_entryID = Comment::FIELD_ENTRY_ID;

        $q = "SELECT `${columnList}` FROM `${t_comments}` WHERE `${c_entryID}` = ?";

        return $this->db->queryObjects($q, Comment::class, 'i', 
            $entryID);
    }
    
    public function getNonTerminatedByIDAndEntryID(int $ID, int $entryID) : ?Comment
    {
        $t_comments = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Comment::FIELD_ID;
        $c_entryID = Comment::FIELD_ENTRY_ID;
        $c_isTerminated = Comment::FIELD_IS_TERMINATED;

        $q = "SELECT `${columnList}` FROM `${t_comments}` " .
            "WHERE `${c_ID}` = ? AND `${c_entryID}` = ? AND `${c_isTerminated}` = 'N' " . 
            "LIMIT 1";

        return $this->db->querySingleObject($q, Comment::class, 'ii', 
            $ID, $entryID);
    }
    
    public function getLeastRecentNonTerminatedByEntryID(int $entryID, int $limit = 5000) : array
    {
        $t_comments = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_entryID = Comment::FIELD_ENTRY_ID;
        $c_isTerminated = Comment::FIELD_IS_TERMINATED;
        $c_createDate = Comment::FIELD_CREATE_DATE;

        $q = "SELECT `${columnList}` FROM `${t_comments}` " .
            "WHERE `${c_entryID}` = ? AND `${c_isTerminated}` = 'N' " . 
            "ORDER BY `${c_createDate}` ASC LIMIT ?";

        return $this->db->queryObjects($q, Comment::class, 'ii', 
            $entryID, $limit);
    }
    
    public function getNonTerminatedByEntryID(int $entryID) : array
    {
        $t_comments = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_entryID = Comment::FIELD_ENTRY_ID;
        $c_isTerminated = Comment::FIELD_IS_TERMINATED;
        $c_createDate = Comment::FIELD_CREATE_DATE;

        $q = "SELECT `${columnList}` FROM `${t_comments}` " .
            "WHERE `${c_entryID}` = ? AND `${c_isTerminated}` = 'N' " . 
            "ORDER BY `${c_createDate}` ASC";

        return $this->db->queryObjects($q, Comment::class, 'i', 
            $entryID);
    }
    
    public function getEntryID(int $ID) : int
    {
        $c_entryID = Comment::FIELD_ID;
        $t_comments = self::TABLE_NAME;
        $c_ID = Comment::FIELD_ID;

        $q = "SELECT `${c_entryID}` FROM `${t_comments}` WHERE `${c_ID}` = ?";

        return $this->db->queryInt($q, -1, 'i', 
            $ID);
    }

    public function getNumCommentsByEntryID(int $entryID) : int
    {
        $c_ID = Comment::FIELD_ID;
        $t_comments = self::TABLE_NAME;
        $c_isTerminated = Comment::FIELD_IS_TERMINATED;
        $c_entryID = Comment::FIELD_ID;

        $q = "SELECT COUNT(`${c_ID}`) FROM `${t_comments}` " . 
            "WHERE `${c_isTerminated}` = 'N' AND `${c_entryID}` = ?";

        return $this->db->queryInt($q, 0, 'i', 
            $entryID);
    }

    public function getLastCommentDateByEntryID(int $entryID) : DateTime
    {
        $c_createDate = Comment::FIELD_CREATE_DATE;
        $t_comments = self::TABLE_NAME;
        $c_isTerminated = Comment::FIELD_IS_TERMINATED;
        $c_entryID = Comment::FIELD_ID;

        $q = "SELECT MAX(`${c_createDate}`) FROM `${t_comments}` " . 
            "WHERE `${c_isTerminated}` = 'N' AND `${c_entryID}` = ?";

        $dateValue = $this->db->queryString($q, Util::ZERO_DATE_TIME_VALUE, 'i', $entryID);
        return Util::utcDateTimeFromString($dateValue);
    }

    public function getUserCount() : int
    {
        $c_userID = Comment::FIELD_USER_ID;
        $t_comments = self::TABLE_NAME;

        $q = "SELECT COUNT(DISTINCT `${c_userID}`) FROM `${t_comments}`";

        return $this->db->queryInt($q, 0);
    }

    public function getCommentCount() : int
    {
        $c_ID = Comment::FIELD_ID;
        $t_comments = self::TABLE_NAME;

        $q = "SELECT COUNT(`{$c_ID}`) FROM `${t_comments}`";

        return $this->db->queryInt($q, 0);
    }

    public function getCommentCountByUserID(int $userID) : int
    {
        $c_ID = Comment::FIELD_ID;
        $t_comments = self::TABLE_NAME;
        $c_userID = Comment::FIELD_USER_ID;

        $q = "SELECT COUNT(`${c_ID}`) FROM `${t_comments}` " . 
            "WHERE `${c_userID}` = ?";

        return $this->db->queryInt($q, 0, 'i', $userID);
    }

    private function toCommentCount(mysqli_result $result) : array
    {
        $commentCount = array();
        while ($row = $result->fetch_row()) {
            $commentCount[$row[0]] = $row[1];
        }

        return $commentCount;
    }

    public function getDailyCommentCountByUserID(int $userID, DateTime $from, DateTime $to) : array
    {
        $c_dayDate = Comment::FIELD_DAY_DATE;
        $c_ID = Comment::FIELD_ID;
        $t_comments = self::TABLE_NAME;
        $c_userID = Comment::FIELD_USER_ID;

        $q = "SELECT `${c_dayDate}`, COUNT(`${c_ID}`) FROM `${t_comments}` " .
            "WHERE `${c_userID}` = ? " .
            "AND `${c_dayDate}` BETWEEN ? AND ? " . 
            "GROUP BY `${c_dayDate}`";
        
        $result = $this->db->query($q, 'iss',
            $userID, Util::dateToString($from), Util::dateToString($to));

        return $this->toCommentCount($result);
    }

    public function getDailyCommentCountByEntryAuthorOrOwnerID(int $authorOrOwnerID, DateTime $from, DateTime $to) : array
    {
        /*
         * Returns number of comments for each day in range where the given user is the author of the entry
         * or the owner of the diary the entry was added to.
         */
        $c_comment_dayDate = Comment::FIELD_DAY_DATE;
        $c_comment_ID = Comment::FIELD_ID;
        $t_comments = self::TABLE_NAME;
        $t_entries = EntryRepository::TABLE_NAME;
        $c_entry_ID = Entry::FIELD_ID;
        $c_comment_entryID = Comment::FIELD_ENTRY_ID;
        $c_entry_userID = Entry::FIELD_USER_ID;
        $c_entry_diaryID = Entry::FIELD_DIARY_ID;

        $q = "SELECT `c`.`${c_comment_dayDate}`, COUNT(`c`.`${c_comment_ID}`) FROM `${t_comments}` AS `c` " .
            "LEFT JOIN `${t_entries}` AS `e` ON `c`.`${c_comment_entryID}` = `e`.`${c_entry_ID}` " .
            "WHERE (`e`.`${c_entry_userID}` = ? OR `e`.`${c_entry_diaryID}` = ?) " .
            "AND `${c_comment_dayDate}` BETWEEN ? AND ? " . 
            "GROUP BY `${c_comment_dayDate}`";
        
        // User ID is used twice in WHERE condition above
        $result = $this->db->query($q, 'iiss',
            $authorOrOwnerID, $authorOrOwnerID, Util::dateToString($from), Util::dateToString($to));

        return $this->toCommentCount($result);
    }

    private function getMyCommentsClause(int $userID, /*out*/ array &$values) : string
    {
        $c_comment_userID = Comment::FIELD_USER_ID;

        $ownClause = "`c`.`${c_comment_userID}` = ?";

        // Append parameter used in this clause
        $values[] = $userID;

        return $ownClause;
    }

    public function getComments(
        int $userID, 
        int $listType, 
        DateTime $beforeDate, 
        DateTime $afterDate, 
        array $bannedIDs = array(), 
        int $limit = 30) : array
    {
        $c_comment_ID = Comment::FIELD_ID;
        $c_comment_entryID = Comment::FIELD_ENTRY_ID;
        $c_comment_userID = Comment::FIELD_USER_ID;
        $c_comment_createDate = Comment::FIELD_CREATE_DATE;
        $c_comment_body = Comment::FIELD_BODY;
        $c_entry_diaryID = Entry::FIELD_DIARY_ID;
        $c_entry_access = Entry::FIELD_ACCESS;
        $t_comments = self::TABLE_NAME;
        $t_entries = EntryRepository::TABLE_NAME;
        $c_entry_ID = Entry::FIELD_ID;
        $c_comment_isTerminated = Comment::FIELD_IS_TERMINATED;
        $c_entry_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_entry_userID = Entry::FIELD_USER_ID;

        $values = array();

        $selectColumns = "`c`.`${c_comment_ID}`, " . 
            "`c`.`${c_comment_entryID}`, " . 
            "`c`.`${c_comment_userID}`, " . 
            "`c`.`${c_comment_createDate}`, " .
            "`c`.`${c_comment_body}`, " . 
            "`e`.`${c_entry_diaryID}`, " . 
            "`e`.`${c_entry_access}` " . 
            "FROM `${t_comments}` AS `c` LEFT JOIN `${t_entries}` AS `e` " . 
            "ON `c`.`${c_comment_entryID}` = `e`.`${c_entry_ID}` ";

        $activeClause = " `c`.`${c_comment_isTerminated}` = 'N' AND `e`.`${c_entry_isTerminated}` = 'N'";

        $dateClause = " AND `c`.`${c_comment_createDate}` BETWEEN ? AND ?";
        $values[] = Util::dateTimeToString($beforeDate);
        $values[] = Util::dateTimeToString($afterDate);

        if (count($bannedIDs) > 0) {
            $bannedPlaceholders = $this->toPlaceholderList($bannedIDs);
            // FIXME: also remove comments where the entry author is banned?
            $bannedClause = 
                " AND `c`.`${c_comment_userID}` NOT IN (${bannedPlaceholders})" .
                " AND `e`.`${c_entry_diaryID}` NOT IN (${bannedPlaceholders})";

            // banned IDs are appearing twice in "NOT IN" filters above
            array_push($values, ...$bannedIDs, ...$bannedIDs);
        } else {
            $bannedClause = '';
        }

        $q = "SELECT ${selectColumns} WHERE " . 
            $activeClause .
            $dateClause .
            $bannedClause;

        switch ($listType) {
            case CommentListType::ALL_COMMENTS:
                $ownEntryClause = $this->entryRepository->getOwnEntriesClause($userID, $values);
                $allOrRegisteredClause = $this->entryRepository->getAllOrRegisteredEntriesClause();
                $friendClause = $this->entryRepository->getFriendEntriesClause($userID, $values);
                $listClause = $this->entryRepository->getListEntriesClause($userID, $values);
                $q .= " AND (($ownEntryClause) OR ($allOrRegisteredClause) OR ($friendClause) OR ($listClause))";
                break;
            
            case CommentListType::FRIENDS_COMMENTS:
                $friendClause = $this->entryRepository->getFriendEntriesClause($userID, $values);
                $q .= " AND $friendClause";
                break;
            
            case CommentListType::OWN_ENTRY_COMMENTS:
                $ownEntryClause = $this->entryRepository->getOwnEntriesClause($userID, $values);
                $q .= " AND $ownEntryClause";
                break;

            case CommentListType::MY_COMMENTS:
                $myCommentsClause = $this->getMyCommentsClause($userID, $values);
                $q .= " AND $myCommentsClause";
                break;
            
            default:
                throw new InvalidArgumentException("Unsupported comment list type '${listType}'.");
        }

        $q .= " ORDER BY `c`.`${c_comment_createDate}` DESC LIMIT ?";
        $values[] = $limit;

        return $this->db->queryObjectsWithParams($q, Comment::class, $values);
    }

    public function save(Comment $comment) : int
    {
        $t_comments = self::TABLE_NAME;
        $commentArray = $comment->toArray();

        $insertColumns = array_keys($commentArray);
        $values = array_values($commentArray);
        if ($comment->getID() < 0) {
            array_shift($insertColumns);
            array_shift($values);
        }

        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `${t_comments}` (`${columnList}`) VALUES (${placeholderList})";

        if ($comment->getID() < 0) {
            $affectedRows = $this->db->execute($q, 'siiissssi', ...$values);
            if ($affectedRows > 0) {
                $comment->setID($this->db->getLastInsertID());
            }
        } else {
            $affectedRows = $this->db->execute($q, 'isiiissssi', ...$values);
        }

        return $affectedRows;
    }
}
