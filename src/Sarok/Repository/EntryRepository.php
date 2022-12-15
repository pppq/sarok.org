<?php declare(strict_types=1);

namespace Sarok\Repository;

use mysqli_result;
use Sarok\Util;
use Sarok\DB;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\EntryAccessRepository;
use Sarok\Repository\CategoryRepository;
use Sarok\Repository\Repository;
use Sarok\Models\FriendType;
use Sarok\Models\Entry;
use Sarok\Models\AccessType;
use DateTime;

final class EntryRepository extends Repository
{
    public const TABLE_NAME = 'entries';
    
    public const COLUMN_NAMES = array(
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
    
    private FriendRepository $friendRepository;
    private EntryAccessRepository $entryAccessRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(
        DB $db, 
        FriendRepository $friendRepository,
        EntryAccessRepository $entryAccessRepository,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($db);
        $this->friendRepository = $friendRepository;
        $this->entryAccessRepository = $entryAccessRepository;
        $this->categoryRepository = $categoryRepository;
    }

    ///////////////////////////
    // Selects
    ///////////////////////////
    
    public function getEntryByID(int $entryID) : ?Entry
    {
        $t_entries = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Entry::FIELD_ID;

        $q = "SELECT `${columnList}` FROM `${t_entries}` WHERE `${c_ID}` = ? LIMIT 1";

        return $this->db->querySingleObject($q, Entry::class, 'i', $entryID);
    }

    /*
     * XXX: The clauses belows might need to be copied to CommentRepository
     * again if the container can not create instances due to circular
     * dependencies!
     */

    public function getFriendEntriesClause(int $userID, /*out*/ array &$values) : string
    {
        $c_access = Entry::FIELD_ACCESS;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $friends = AccessType::FRIENDS->value;

        $destinationIdsQuery = $this->friendRepository->getDestinationUserIdsQuery();
        $friendClause = "`e`.`${c_access}` = '${friends}' AND `e`.`${c_diaryID}` IN (${destinationIdsQuery})";

        // Append parameters used in "destination user IDs" subquery
        array_push($values, $userID, FriendType::FRIEND->value);

        return $friendClause;
    }

    public function getOwnEntriesClause(int $userID, /*out*/ array &$values) : string
    {
        $c_userID = Entry::FIELD_USER_ID;
        $c_diaryID = Entry::FIELD_DIARY_ID;

        $ownClause = "`e`.`${c_userID}` = ? OR `e`.`${c_diaryID}` = ?";

        // Append parameters used in this clause
        array_push($values, $userID, $userID);

        return $ownClause;
    }

    public function getAllOrRegisteredEntriesClause() : string
    {
        $c_access = Entry::FIELD_ACCESS;
        $all = AccessType::ALL->value;
        $registered = AccessType::REGISTERED->value;
            
        $allOrRegisteredClause = "`e`.`${c_access}` IN ('${all}', '${registered}')";
        return $allOrRegisteredClause;
    }
    
    public function getListEntriesClause(int $userID, /*out*/ array &$values) : string
    {
        $c_entry_access = Entry::FIELD_ACCESS;
        $list = AccessType::LIST->value;

        $entryAccessQuery = $this->entryAccessRepository->getExistsQuery();
        $listClause = "`e`.`${c_entry_access}` = '${list}' AND EXISTS (${entryAccessQuery})";

        // Append parameter used in list access subquery
        $values[] = $userID;

        return $listClause;
    }

    private function findEntries(
        array $selectColumns, 
        array $diaryIDs, 
        int $readerID, 
        array $filters, 
        array $bannedIDs = array(), 
        int $offset = 0, 
        int $limit = 50) : array
    {
        $columnList = $this->toColumnList($selectColumns);
        $t_entries = self::TABLE_NAME;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_userID = Entry::FIELD_USER_ID;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_dayDate = Entry::FIELD_DAY_DATE;
        $c_ID = Entry::FIELD_ID;
        $c_title = Entry::FIELD_TITLE;
        $c_body_1 = Entry::FIELD_BODY_1;
        $c_body_2 = Entry::FIELD_BODY_2;
        $c_createDate = Entry::FIELD_CREATE_DATE;
        $c_posX = Entry::FIELD_POS_X;
        $c_posY = Entry::FIELD_POS_Y;
        $c_access = Entry::FIELD_ACCESS;
        
        $q = "SELECT `${columnList}` FROM `${t_entries}` AS `e` WHERE `e`.`${c_isTerminated}` = 'N'";

        // Extract query parameters to local variables
        list(
            'year'        => $year,
            'month'       => $month,
            'day'         => $day,
            'tags'        => $tags,
            'keyword'     => $keyword,
            'beforeDate'  => $beforeDate,
            'afterDate'   => $afterDate,
            'geotagged'   => $geotagged,
            'accessTypes' => $accessTypes,
        ) = $filters;
        
        $placeholderList = $this->toPlaceholderList($diaryIDs);
        $q .= " AND `e`.`$c_diaryID` IN ($placeholderList)";
        
        $params = &$diaryIDs;
        
        if (is_numeric($year) && is_numeric($month)) {
            if (is_numeric($day)) {
                $q .= " AND `e`.`${c_dayDate}` = ?";
                $params[] = "${year}-${month}-${day}";
            } else {
                $q .= " AND DATE_FORMAT(`e`.`${c_dayDate}`, '%Y-%c') = ?";
                $params[] = "${year}-${month}";
            }
        }

        if (isset($tags) && count($tags) > 0) {
            $tagsSubquery = $this->categoryRepository->getEntryIDSubquery($tags);
            $q .= " AND `e`.`${c_ID}` IN ($tagsSubquery)";
            array_push($params, ...$tags);
        }

        if (isset($geotagged) && $geotagged == true) {
            $q .= " AND `e`.`${c_posX}` IS NOT NULL AND `e`.`${c_posY}` IS NOT NULL";
        }

        if (isset($keyword) && strlen($keyword) > 0) {
            $q .= " AND (`e`.`${c_title}` LIKE ? OR `e`.`${c_body_1}` LIKE ? OR `e`.`${c_body_2}` LIKE ?)";
            $keywordWithWildcard = "%${keyword}%";
            array_push($params, $keywordWithWildcard, $keywordWithWildcard, $keywordWithWildcard);
        }
        
        if (isset($accessTypes)) {
            $accessPlaceholders = $this->toPlaceholderList($accessTypes);
            $q .= " AND `e`.`${c_access}` IN (${accessPlaceholders})";
            array_push($params, ...$accessTypes);
        } else {
            $ownEntryClause = $this->getOwnEntriesClause($readerID, $params);
            $allOrRegisteredClause = $this->getAllOrRegisteredEntriesClause();
            $friendClause = $this->getFriendEntriesClause($readerID, $params);
            $listClause = $this->getListEntriesClause($readerID, $params);
            $q .= " AND ((${ownEntryClause}) OR ({$allOrRegisteredClause}) OR (${friendClause}) OR (${listClause}))";
        }

        if (count($bannedIDs) > 0) {
            $bannedPlaceholders = $this->toPlaceholderList($bannedIDs);
            $q .= 
                " AND `e`.`${c_userID}` NOT IN (${bannedPlaceholders})" .
                " AND `e`.`${c_diaryID}` NOT IN (${bannedPlaceholders})";
            array_push($params, ...$bannedIDs, ...$bannedIDs);
        }

        if (isset($afterDate) && strlen($afterDate) > 0) {
            $q .= " AND `${c_createDate}` >= ?";
            $params[] = Util::dateTimeToString($afterDate);
        }

        if (isset($beforeDate) && strlen($beforeDate) > 0) {
            $q .= " AND `${c_createDate}` <= ?";
            $params[] = Util::dateTimeToString($beforeDate);
        }

        $q .= " ORDER BY `${c_createDate}` DESC LIMIT ?, ?";
        array_push($params, $offset, $limit);
        return $this->db->queryObjectsWithParams($q, Entry::class, $params);
    }

    public function getEntries(
        array $diaryIDs, 
        int $readerID, 
        array $filters, 
        array $bannedIDs = array(), 
        int $offset = 0, 
        int $limit = 50) : array
    {
        $selectColumns = array(
            Entry::FIELD_ID, 
            Entry::FIELD_DIARY_ID, 
            Entry::FIELD_USER_ID, 
            Entry::FIELD_CREATE_DATE, 
            Entry::FIELD_MODIFY_DATE, 
            Entry::FIELD_ACCESS, 
            Entry::FIELD_TITLE, 
            Entry::FIELD_BODY_1, 
            Entry::FIELD_BODY_2, 
            Entry::FIELD_NUM_COMMENTS, 
            Entry::FIELD_MODERATOR_COMMENT, 
            Entry::FIELD_CATEGORY, 
            Entry::FIELD_POS_X,
            Entry::FIELD_POS_Y, 
            Entry::FIELD_RSS_URL
        );
        
        return $this->findEntries($selectColumns, $diaryIDs, $readerID, $filters, $bannedIDs, $offset, $limit);
    }

    public function getEntryPOIs(
        array $diaryIDs, 
        int $readerID, 
        array $filters, 
        array $bannedIDs = array(), 
        int $offset = 0, 
        int $limit = 50) : array
    {
        // Useful for rendering pushpins on a map
        $selectColumns = array(
            Entry::FIELD_ID, 
            Entry::FIELD_TITLE, 
            Entry::FIELD_BODY_1, 
            Entry::FIELD_CREATE_DATE, 
            Entry::FIELD_POS_X,
            Entry::FIELD_POS_Y, 
        );
        
        return $this->findEntries($selectColumns, $diaryIDs, $readerID, $filters, $bannedIDs, $offset, $limit);
    }

    public function getEntriesForDashboard(
        array $diaryIDs, 
        int $readerID, 
        array $filters, 
        array $bannedIDs = array(), 
        int $offset = 0, 
        int $limit = 50) : array
    {
        $selectColumns = array(
            Entry::FIELD_ID, 
            Entry::FIELD_USER_ID, 
            Entry::FIELD_DIARY_ID, 
            Entry::FIELD_CREATE_DATE, 
            Entry::FIELD_TITLE, 
            Entry::FIELD_BODY_1, 
            Entry::FIELD_ACCESS, 
        );
        
        return $this->findEntries($selectColumns, $diaryIDs, $readerID, $filters, $bannedIDs, $offset, $limit);
    }

    private function toEntryMap(mysqli_result $result) : array
    {
        $entryMap = array();
        while ($row = $result->fetch_row()) {
            $entryMap[$row[0]] = $row[1];
        }

        return $entryMap;
    }

    public function getCommentCountByEntryIds(array $entryIDs) : array
    {
        $t_entries = self::TABLE_NAME;
        $c_numComments = Entry::FIELD_NUM_COMMENTS;
        $c_ID = Entry::FIELD_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        
        $q = "SELECT `${c_ID}`, `${c_numComments}` FROM `${t_entries}` WHERE `${c_ID}` IN (${placeholderList})";

        $result = $this->db->queryWithParams($q, $entryIDs);
        return $this->toEntryMap($result);
    }

    public function getNonTerminatedByID(int $entryID) : ?Entry
    {
        $t_entries = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Entry::FIELD_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;

        $q = "SELECT `${columnList}` FROM `${t_entries}` " . 
            "WHERE `${c_ID}` = ? AND `${c_isTerminated}` = 'N' " . 
            "LIMIT 1";

        return $this->db->querySingleObject($q, Entry::class, 'i', 
            $entryID);
    }
    
    public function getEntryByIDAndDiaryID(int $entryID, int $diaryID) : ?Entry
    {
        $t_entries = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Entry::FIELD_ID;
        $c_diaryID = Entry::FIELD_DIARY_ID;

        $q = "SELECT `${columnList}` FROM `${t_entries}` " . 
            "WHERE `${c_ID}` = ? AND `${c_diaryID}` = ? " . 
            "LIMIT 1";

        return $this->db->querySingleObject($q, Entry::class, 'ii', 
            $entryID, $diaryID);
    }
    
    public function getUserID(int $entryID) : int
    {
        $t_entries = self::TABLE_NAME;
        $c_userID = Entry::FIELD_USER_ID;
        $c_ID = Entry::FIELD_ID;

        $q = "SELECT `${c_userID}` FROM `${t_entries}` WHERE `${c_ID}` = ? LIMIT 1";

        return $this->db->queryInt($q, -1, 'i', 
            $entryID);
    }

    public function getNonPrivateByID(int $entryID) : ?Entry
    {
        $t_entries = self::TABLE_NAME;
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_ID = Entry::FIELD_ID;
        $c_access = Entry::FIELD_ACCESS;
        $access = AccessType::AUTHOR_ONLY->value;

        $q = "SELECT `${columnList}` FROM `${t_entries}` " . 
            "WHERE `${c_ID}` = ? AND `${c_access}` != '$access' " . 
            "LIMIT 1";

        return $this->db->querySingleObject($q, Entry::class, 'i', 
            $entryID);
    }

    public function filterEntryIDsByDiaryID(array $entryIDs, int $diaryID) : array
    {
        $c_ID = Entry::FIELD_ID;
        $t_entries = self::TABLE_NAME;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        
        // Get the element count before we re-purpose the array as the query input
        $limit = count($entryIDs);

        $q = "SELECT `${c_ID}` FROM `${t_entries}` " . 
            "WHERE `${c_ID}` IN (${placeholderList}) AND `${c_diaryID}` = ? AND `${c_isTerminated}` = 'N' " . 
            "LIMIT ?";

        $params = &$entryIDs;
        array_push($params, $diaryID, $limit);
        return $this->db->queryArrayWithParams($q, $params);
    }

    public function entryExists(int $entryID) : bool
    {
        $c_ID = Entry::FIELD_ID;
        $t_entries = self::TABLE_NAME;

        $q = "SELECT COUNT(`${c_ID}`) FROM `${t_entries}` WHERE `${c_ID}` = ?";

        return $this->db->queryInt($q, 0, 'i', $entryID) > 0;
    }

    public function entryExistsInDiary(int $entryID, int $diaryID, bool $isTerminated = false) : bool
    {
        $c_ID = Entry::FIELD_ID;
        $t_entries = self::TABLE_NAME;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;

        $q = "SELECT COUNT(`${c_ID}`) FROM `${t_entries}` " . 
            "WHERE `${c_ID}` = ? AND `${c_diaryID}` = ? AND `${c_isTerminated}` = ?";
        
        return $this->db->queryInt($q, 0, 'iis', 
            $entryID, $diaryID, Util::boolToYesNo($isTerminated)) > 0;
    }

    public function getDiaryID(int $entryID) : int
    {
        $t_entries = self::TABLE_NAME;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_ID = Entry::FIELD_ID;

        $q = "SELECT `${c_diaryID}` FROM `${t_entries}` WHERE `${c_ID}` = ? LIMIT 1";

        return $this->db->queryInt($q, -1, 'i', $entryID);
    }

    public function getDayDate(int $entryID) : ?DateTime
    {
        $t_entries = self::TABLE_NAME;
        $c_dayDate = Entry::FIELD_DAY_DATE;
        $c_ID = Entry::FIELD_ID;

        $q = "SELECT `${c_dayDate}` FROM `${t_entries}` WHERE `${c_ID}` = ? LIMIT 1";

        $value = $this->db->queryString($q, '', 'i', $entryID);
        if ($value !== '') {
            return Util::utcDateTimeFromString($value);
        } else {
            return null;
        }
    }
    
    public function getDayDatesForIDs(array $entryIDs) : array
    {
        $c_dayDate = Entry::FIELD_DAY_DATE;
        $t_entries = self::TABLE_NAME;
        $c_ID = Entry::FIELD_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;

        $q = "SELECT DISTINCT `${c_dayDate}` FROM `${t_entries}` " . 
            "WHERE `${c_ID}` IN (${placeholderList}) AND `${c_isTerminated}` = 'N'";

        $params = $this->db->queryArrayWithParams($q, $entryIDs);
        $toDateTime = function ($str) { return Util::utcDateTimeFromString($str); };
        return array_map($toDateTime, $params);
    }

    public function getCommentedSubquery(string $op, string $idExpression, string $lastCommentExpression) : string
    {
        $c_ID = Entry::FIELD_ID;
        $t_entries = self::TABLE_NAME;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_lastComment = Entry::FIELD_LAST_COMMENT;
        
        return "SELECT `e`.`${c_ID}` FROM `${t_entries}` AS `e` " . 
            "WHERE `e`.`${c_ID}` = ${idExpression} AND `e`.`${c_isTerminated}` = 'N' " . 
            "AND `e`.`${c_lastComment}` ${op} ${lastCommentExpression}";
    }

    // Used for displaying favourites
    public function getPartialEntryByIDs(array $entryIDs) : array
    {
        $selectColumns = array(
            Entry::FIELD_ID,
            Entry::FIELD_DIARY_ID,
            Entry::FIELD_USER_ID,
            Entry::FIELD_TITLE,
            Entry::FIELD_LAST_COMMENT
        );

        $columnList = $this->toColumnList($selectColumns);
        $t_entries = self::TABLE_NAME;
        $c_ID = Entry::FIELD_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        $c_lastComment = Entry::FIELD_LAST_COMMENT;

        // FIXME: terminated entries are not filtered out here
        $q = "SELECT `${columnList}` FROM `${t_entries}` " . 
            "WHERE `${c_ID}` IN (${placeholderList}) " . 
            "ORDER BY `${c_lastComment}` DESC";

        return $this->db->queryObjectsWithParams($q, Entry::class, $entryIDs);
    }

    public function getEntryCount() : int
    {
        $c_ID = Entry::FIELD_ID;
        $t_entries = self::TABLE_NAME;

        $q = "SELECT COUNT(`${c_ID}`) FROM `${t_entries}`";

        return $this->db->queryInt($q, 0);
    }

    public function getEntryIDsForDiary(int $diaryID) : array
    {
        $t_entries = self::TABLE_NAME;
        $c_ID = Entry::FIELD_ID;
        $c_diaryID = Entry::FIELD_DIARY_ID;

        $q = "SELECT `${c_ID}` FROM `${t_entries}` where `${c_diaryID}` = ?";

        return $this->db->queryArray($q, 'i', $diaryID);
    }

    public function getOwnEntryCountGroupByDate(int $userID, DateTime $from, DateTime $to) : array
    {
        $c_dayDate = Entry::FIELD_DAY_DATE;
        $c_ID = Entry::FIELD_ID;
        $t_entries = self::TABLE_NAME;
        $c_userID = Entry::FIELD_USER_ID;
        $c_diaryID = Entry::FIELD_DIARY_ID;

        // FIXME: terminated entries are not filtered out here
        $q = "SELECT `${c_dayDate}`, COUNT(`{$c_ID}`) FROM `${t_entries}` " .
            "WHERE (`${c_userID}` = ? OR `${c_diaryID}` = ?) " .
            "AND `${c_dayDate}` BETWEEN ? AND ? " . 
            "GROUP BY `${c_dayDate}`";
        
        $result = $this->db->query($q, 'iiss',
            $userID, $userID, Util::dateToString($from), Util::dateToString($to));

        return $this->toEntryMap($result);
    }

    public function getOwnEntryCountGroupByAccess(int $userID, DateTime $from, DateTime $to) : array
    {
        $c_access = Entry::FIELD_ACCESS;
        $c_ID = Entry::FIELD_ID;
        $t_entries = self::TABLE_NAME;
        $c_userID = Entry::FIELD_USER_ID;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_dayDate = Entry::FIELD_DAY_DATE;

        // FIXME: terminated entries are not filtered out here
        $q = "SELECT `${c_access}`, COUNT(`${c_ID}`) FROM `${t_entries}` " .
            "WHERE (`${c_userID}` = ? OR `${c_diaryID}` = ?) " .
            "AND `${c_dayDate}` BETWEEN ? AND ? " . 
            "GROUP BY `${c_access}`";

        $result = $this->db->query($q, 'iiss',
            $userID, $userID, Util::dateToString($from), Util::dateToString($to));

        return $this->toEntryMap($result);
    }

    public function getOwnEntryCount(int $userID) : int
    {
        $c_ID = Entry::FIELD_ID;
        $t_entries = self::TABLE_NAME;
        $c_userID = Entry::FIELD_USER_ID;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;

        $q = "SELECT COUNT(`${c_ID}`) FROM `${t_entries}` " . 
            "WHERE (`${c_userID}` = ? OR `${c_diaryID}` = ?) " . 
            "AND `${c_isTerminated}` = 'N'";

        return $this->db->queryInt($q, 0, 'ii', 
            $userID, $userID);
    }

    ///////////////////////////
    // Updates
    ///////////////////////////
    
    public function updateLastVisit(int $entryID, DateTime $lastVisit) : int
    {
        $t_entries = self::TABLE_NAME;
        $c_lastVisit = Entry::FIELD_LAST_VISIT;
        $c_ID = Entry::FIELD_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        
        $q = "UPDATE `${t_entries}` SET `${c_lastVisit}` = ? WHERE `${c_ID}` = ? AND `${c_isTerminated}` = 'N' LIMIT 1";
        
        return $this->db->execute($q, 'si', 
            Util::dateTimeToString($lastVisit), $entryID);
    }

    public function updateTerminated(int $entryID, bool $terminated) : int
    {
        $t_entries = self::TABLE_NAME;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_ID = Entry::FIELD_ID;
        
        $q = "UPDATE `${t_entries}` SET `${c_isTerminated}` = ? WHERE `${c_ID}` = ? LIMIT 1";

        return $this->db->execute($q, 'si', 
            Util::boolToYesNo($terminated), $entryID);
    }
    
    public function updateCommentStats(int $entryID, int $numComments, DateTime $lastComment) : int
    {
        $t_entries = self::TABLE_NAME;
        $c_numComments = Entry::FIELD_NUM_COMMENTS;
        $c_lastComment = Entry::FIELD_LAST_COMMENT;
        $c_ID = Entry::FIELD_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;

        $q = "UPDATE `${t_entries}` " . 
            "SET `${c_numComments}` = ?, `${c_lastComment}` = ? " . 
            "WHERE `${c_ID}` = ? AND `${c_isTerminated}` = 'N' " . 
            "LIMIT 1";

        return $this->db->execute($q, 'isi', 
            $numComments, Util::dateTimeToString($lastComment), $entryID);
    }

    private function updateAccess(
        string $column, 
        AccessType $access, 
        DateTime $modifyDate, 
        int $diaryID, 
        array &$entryIDs) : int
    {
        $t_entries = self::TABLE_NAME;
        $c_modifyDate = Entry::FIELD_MODIFY_DATE;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_ID = Entry::FIELD_ID;
        
        // Introduce an alias, we don't want to copy the array by assignment here
        $placeholderList = $this->toPlaceholderList($entryIDs);
        $params = &$entryIDs;

        // Prepend parameters: access type, modify date and diaryID
        array_unshift($params, $access->value, Util::dateTimeToString($modifyDate), $diaryID);
        
        $q = "UPDATE `${t_entries}` SET `${column}` = ?, `${c_modifyDate}` = ? " .
            "WHERE `${c_diaryID}` = ? AND `${c_isTerminated}` = 'N' AND `${c_ID}` IN (${placeholderList})";

        return $this->db->executeWithParams($q, $params);
    }

    public function updateEntryAccess(AccessType $access, DateTime $modifyDate, int $diaryID, array $entryIDs) : int
    {
        return $this->updateAccess(Entry::FIELD_ACCESS, $access, $modifyDate, $diaryID, $entryIDs);
    }

    public function updateCommentAccess(AccessType $access, DateTime $modifyDate, int $diaryID, array $entryIDs) : int
    {
        return $this->updateAccess(Entry::FIELD_COMMENTS, $access, $modifyDate, $diaryID, $entryIDs);
    }

    public function update(Entry $entry, DateTime $modifyDate) : int
    {
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
        
        $toAssignment = function ($column) { return "`${column}` = ?"; };
        $assignments = array_map($toAssignment, $updateColumns);
        $assignmentList = implode(', ', $assignments);
        
        $t_entries = self::TABLE_NAME;
        $c_ID = Entry::FIELD_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        
        $q = "UPDATE `${t_entries}` SET ${assignmentList} " . 
            "WHERE `${c_ID}` = ? AND `${c_isTerminated}` = 'N' " . 
            "LIMIT 1";

        // Set the modification date before we convert the object to an assoc. array
        $entry->setModifyDate($modifyDate);
        
        // Keep values for columns which will be updated
        $params = $entry->toArray();
        $filteredValues = array_intersect_key($params, array_flip($updateColumns));
        return $this->db->executeWithParams($q, $filteredValues);
    }

    ///////////////////////////
    // Insert
    ///////////////////////////

    public function save(Entry $entry) : int
    {
        $t_entries = self::TABLE_NAME;
        $entryArray = $entry->toArray();

        $insertColumns = array_keys($entryArray);
        $values = array_values($entryArray);
        if ($entry->getID() < 0) {
            array_shift($insertColumns);
            array_shift($values);
        }

        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `${t_entries}` (`${columnList}`) VALUES (${placeholderList})";

        if ($entry->getID() < 0) {
            $affectedRows = $this->db->execute($q, 'iisssssssissssissss', ...$values);
            if ($affectedRows > 0) {
                $entry->setID($this->db->getLastInsertID());
            }
        } else {
            $affectedRows = $this->db->execute($q, 'iiisssssssissssissss', ...$values);
        }

        return $affectedRows;
    }
}
