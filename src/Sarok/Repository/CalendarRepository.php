<?php namespace Sarok\Repository;

use DateTime;
use Sarok\Service\DB;
use Sarok\Models\Calendar;
use Sarok\Models\FriendType;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\AbstractRepository;

class CalendarRepository extends AbstractRepository
{
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
    
    public function getBlogMonthsBefore(int $userID, int $year, bool $publicOnly) : array
    {
        if ($publicOnly === true) {
            $c_numPublic = Calendar::FIELD_NUM_PUBLIC;
            $filterClause = "AND `$c_numPublic` > 0 ";
        } else {
            $filterClause = '';
        }

        $selectColumns = array(
            Calendar::FIELD_USER_ID,
            Calendar::FIELD_Y,
            Calendar::FIELD_M,
        );
        
        $columnList = $this->toColumnList($selectColumns);
        $t_calendar = $this->getTableName();
        $c_userID = Calendar::FIELD_USER_ID;
        $c_y = Calendar::FIELD_Y;
        $c_m = Calendar::FIELD_M;
        
        $q = "SELECT DISTINCT `$columnList` FROM `$t_calendar` " .
            "WHERE `$c_userID` = ? AND `$c_y` BETWEEN 1900 AND ? {$filterClause}" .
            "ORDER BY `$c_y` DESC, `$c_m` DESC";
        
        return $this->db->queryObjects($q, Calendar::class, 'ii', $userID, $year);
    }

    public function getCalendarEntries(int $userID, int $year, int $month) : array
    {
        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        $t_calendar = $this->getTableName();
        $c_userID = Calendar::FIELD_USER_ID;
        $c_y = Calendar::FIELD_Y;
        $c_m = Calendar::FIELD_M;
        
        $q = "SELECT `$columnList` FROM `$t_calendar` " .
            "WHERE `$c_userID` = ? AND `$c_y` = ? AND `$c_m` = ? " .
            "ORDER BY `$c_y` DESC, `$c_m` DESC";
        
        return $this->db->queryObjects($q, Calendar::class, 'iii', $userID, $year, $month);
    }

    public function getCalendarEntriesOfFriends(int $userID, int $year, int $month) : array
    {
        // numAll will contain the sum of the entry count of all friends
        $c_y = Calendar::FIELD_Y;
        $c_m = Calendar::FIELD_M;
        $c_d = Calendar::FIELD_D;
        $c_numAll = Calendar::FIELD_NUM_ALL;
        $t_calendar = $this->getTableName();
        $c_userID = Calendar::FIELD_USER_ID;
        
        $friendsSubQuery = $this->friendRepository->getDestinationUserIdsQuery();
            
        $q = "SELECT `$c_y`, `$c_m`, `$c_d`, SUM(`$c_numAll`) FROM `$t_calendar` " .
            "WHERE `$c_y` = ? AND `$c_m` = ? AND `$c_userID` IN ($friendsSubQuery) " .
            "GROUP BY `$c_y`, `$c_m`, `$c_d`";
        
        return $this->db->queryObjects($q, Calendar::class, 'iiis', $year, $month, $userID, FriendType::FRIEND);
    }
    
    public function update(int $userID, int $year, int $month, int $day) : int
    {
        $t_calendar = $this->getTableName();
        $c_userID = Calendar::FIELD_USER_ID;
        $c_y = Calendar::FIELD_Y;
        $c_m = Calendar::FIELD_M;
        $c_d = Calendar::FIELD_D;
        $insertColumns = array($c_userID, $c_y, $c_m, $c_d);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $insertQuery = "INSERT IGNORE INTO `$t_calendar` (`$insertColumns`) VALUES ($placeholderList)";
        $this->db->execute($insertQuery, 'iiii', $userID, $year, $month, $day);

        // FIXME: replace table and field names with constants from Entry model and repository
        $allEntriesQuery = "SELECT COUNT(*) FROM `entries` AS `e` " .
            "WHERE date_format(`e`.`createDate`, '%Y-%c-%e') = concat(`c`.`$y`, '-', `c`.`$m`, '-', `c`.`$d`) " .
            "AND `c`.`$userIDColumn` = `e`.`diaryID` AND `isTerminated` = 'N'";
        
        $publicEntriesQuery = $allEntriesQuery . " AND `e`.`access` = 'ALL'";
        $registeredOnlyEntriesQuery = $allEntriesQuery . " AND `e`.`access` = 'REGISTERED'";
        $friendsOnlyEntriesQuery = $allEntriesQuery . " AND `e`.`access` = 'FRIENDS'";
        
        $c_numAll = Calendar::FIELD_NUM_ALL;
        $c_numPublic = Calendar::FIELD_NUM_PUBLIC;
        $c_numRegistered = Calendar::FIELD_NUM_REGISTERED;
        $c_numFriends = Calendar::FIELD_NUM_FRIENDS;
        
        $updateQuery = "UPDATE `$t_calendar` AS `c` SET " .
            "`$c_numAll` = ($allEntriesQuery), " .
            "`$c_numPublic` = ($publicEntriesQuery), " .
            "`$c_numRegistered` = ($registeredOnlyEntriesQuery), " .
            "`$c_numFriends` = ($friendsOnlyEntriesQuery) " .
            "WHERE `c`.`$c_userID` = ? AND `c`.`$c_y` = ? AND `c`.`$c_m` = ? AND `c`.`$c_d` = ?";
        
        return $this->db->execute($updateQuery, 'iiii', $userID, $year, $month, $day);
    }
}
