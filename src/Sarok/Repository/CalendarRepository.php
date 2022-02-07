<?php namespace Sarok\Repository;

use Sarok\Models\Calendar;
use Sarok\Service\DB;
use DateTime;

class CalendarRepository extends AbstractRepository {

    const TABLE_NAME = 'calendar';
    
    private const COLUMN_NAMES = array(
        Calendar::FIELD_USER_ID,
        Calendar::FIELD_Y,
        Calendar::FIELD_M,
        Calendar::FIELD_D,
        Calendar::FIELD_NUM_PUBLIC,
        Calendar::FIELD_NUM_REGISTERED,
        Calendar::FIELD_NUM_FRIENDS,
        Calendar::FIELD_NUM_ALL,
        Calendar::FIELD_NUM_MAILS_RECEIVED,
        Calendar::FIELD_NUM_MAILS_SENT,
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
    
    public function getBlogMonthsBefore(int $userID, DateTime $calendarYear, bool $publicOnly) : array {
        if ($publicOnly === true) {
            $numPublic = Calendar::FIELD_NUM_PUBLIC;
            $filterClause = "AND `$numPublic` > 0 ";
        } else {
            $filterClause = '';
        }

        $selectColumns = array(
            Calendar::FIELD_USER_ID,
            Calendar::FIELD_Y,
            Calendar::FIELD_M,
        );
        
        $columnList = $this->toColumnList($selectColumns);
        $calendar = $this->getTableName();
        $userIDColumn = Calendar::FIELD_USER_ID;
        $y = Calendar::FIELD_Y;
        $m = Calendar::FIELD_M;
        
        $q = "SELECT DISTINCT `$columnList` FROM `$calendar` " .
            "WHERE `$userIDColumn` = ? AND `$y` BETWEEN 1900 AND ? {$filterClause}" .
            "ORDER BY `$y` DESC, `$m` DESC";
        
        return $this->db->queryObjects($q, Calendar::class, 'ii', 
            $userID, 
            (int) $calendarYear->format('Y'));
    }

    public function getCalendarEntries(int $userID, DateTime $calendarMonth) : array {
        $columnList = $this->toColumnList($this->getColumnNames());
        $calendar = $this->getTableName();
        $userIDColumn = Calendar::FIELD_USER_ID;
        $y = Calendar::FIELD_Y;
        $m = Calendar::FIELD_M;
        
        $q = "SELECT `$columnList` FROM `$calendar` " .
            "WHERE `$userIDColumn` = ? AND `$y` = ? AND `$m` = ? " .
            "ORDER BY `$y` DESC, `$m` DESC";
        
        return $this->db->queryObjects($q, Calendar::class, 'iii',
            $userID,
            (int) $calendarMonth->format('Y'),
            (int) $calendarMonth->format('m'));
    }

    public function getCalendarEntriesOfFriends(int $userID, DateTime $calendarMonth) : array {
        $y = Calendar::FIELD_Y;
        $m = Calendar::FIELD_M;
        $d = Calendar::FIELD_D;
        $numAll = Calendar::FIELD_NUM_ALL;

        // numAll will contain the sum of the entry count of all friends
        $columnList = $this->toColumnList(array($y, $m, $d, "SUM($numAll)"));
        
        $calendar = $this->getTableName();
        $userIDColumn = Calendar::FIELD_USER_ID;
        
        // FIXME: replace table and field names with constants from the user list model/repository
        $friendsSubQuery = "SELECT `userID` FROM `friends` WHERE `friendOf` = ? AND `friendType` = 'friend'";
            
        $q = "SELECT `$columnList` FROM `$calendar` " .
            "WHERE `$y` = ? AND `$m` = ? AND `$userIDColumn` IN ($friendsSubQuery) " .
            "GROUP BY `$y`, `$m`, `$d`";
        
        return $this->db->queryObjects($q, Calendar::class, 'iii',
            (int) $calendarMonth->format('Y'),
            (int) $calendarMonth->format('m'),
            $userID);
    }
    
    public function updateCalendar(int $userID, DateTime $calendarDay) : int {
        $calendar = $this->getTableName();
        $userIDColumn = Calendar::FIELD_USER_ID;
        $y = Calendar::FIELD_Y;
        $m = Calendar::FIELD_M;
        $d = Calendar::FIELD_D;
        $insertColumns = array($userIDColumn, $y, $m, $d);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $insertQuery = "INSERT IGNORE INTO `$calendar` (`$insertColumns`) VALUES ($placeholderList)";
        $this->db->execute($insertQuery, 'iiii',
            $userID,
            (int) $calendarDay->format('Y'),
            (int) $calendarDay->format('m'),
            (int) $calendarDay->format('d'));

        // FIXME: replace table and field names with constants from Entry model and repository
        $allEntriesQuery = "SELECT COUNT(*) FROM `entries` AS `e` " .
            "WHERE date_format(`e`.`createDate`, '%Y-%c-%e') = concat(`c`.`$y`, '-', `c`.`$m`, '-', `c`.`$d`) " .
            "AND `c`.`$userIDColumn` = `e`.`diaryID` AND `isTerminated` = 'N'";
        
        $publicEntriesQuery = $allEntriesQuery . " AND `e`.`access` = 'ALL'";
        $registeredOnlyEntriesQuery = $allEntriesQuery . " AND `e`.`access` = 'REGISTERED'";
        $friendsOnlyEntriesQuery = $allEntriesQuery . " AND `e`.`access` = 'FRIENDS'";
        
        $numAll = Calendar::FIELD_NUM_ALL;
        $numPublic = Calendar::FIELD_NUM_PUBLIC;
        $numRegistered = Calendar::FIELD_NUM_REGISTERED;
        $numFriends = Calendar::FIELD_NUM_FRIENDS;
        
        $updateQuery = "UPDATE `$calendar` AS `c` SET " .
            "`$numAll` = ($allEntriesQuery), " .
            "`$numPublic` = ($publicEntriesQuery), " .
            "`$numRegistered` = ($registeredOnlyEntriesQuery), " .
            "`$numFriends` = ($friendsOnlyEntriesQuery) " .
            "WHERE `c`.`$userIDColumn` = ? AND `c`.`$y` = ? AND `c`.`$m` = ? AND `c`.`$d` = ?";
        
        return $this->db->execute($updateQuery, 'iiii', 
            $userID, 
            (int) $calendarDay->format('Y'),
            (int) $calendarDay->format('m'),
            (int) $calendarDay->format('d'));
    }
}
