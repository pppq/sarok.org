<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Service\DB;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\EntryRepository;
use Sarok\Repository\AbstractRepository;
use Sarok\Models\FriendType;
use Sarok\Models\Entry;
use Sarok\Models\Calendar;
use DateTime;

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

        if ($publicOnly === true) {
            $c_numPublic = Calendar::FIELD_NUM_PUBLIC;
            $filterClause = "AND `$c_numPublic` > 0 ";
        } else {
            $filterClause = '';
        }
        
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
        $c_y = Calendar::FIELD_Y;
        $c_m = Calendar::FIELD_M;
        $c_d = Calendar::FIELD_D;
        $c_numAll = Calendar::FIELD_NUM_ALL;
        $t_calendar = $this->getTableName();
        $c_userID = Calendar::FIELD_USER_ID;
        
        $friendsSubQuery = $this->friendRepository->getDestinationUserIdsQuery();
        
        // XXX: numAll will contain the sum of the entry count of all friends
        $q = "SELECT `$c_y`, `$c_m`, `$c_d`, SUM(`$c_numAll`) AS `$c_numAll` FROM `$t_calendar` " .
            "WHERE `$c_y` = ? AND `$c_m` = ? AND `$c_userID` IN ($friendsSubQuery) " .
            "GROUP BY `$c_y`, `$c_m`, `$c_d`";
        
        return $this->db->queryObjects($q, Calendar::class, 'iiis', 
            $year, 
            $month, 
            $userID, 
            FriendType::FRIEND);
    }
    
    public function save(Calendar $calendar) : int
    {
        $t_calendar = $this->getTableName();
        $calendarArray = $calendar->toArray();
        $insertColumns = array_keys($calendarArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $updateColumns = array(
            Calendar::FIELD_NUM_ALL, 
            Calendar::FIELD_NUM_PUBLIC, 
            Calendar::FIELD_NUM_REGISTERED, 
            Calendar::FIELD_NUM_FRIENDS
        );

        // Final ' = ?' is added to the query
        $updateList = implode('` = ?, `', $updateColumns);
        $q = "INSERT INTO `$t_calendar` (`$insertColumns`) VALUES ($placeholderList) " .
            "ON DUPLICATE KEY UPDATE `$updateList` = ?";

        $values = array_values($accessLogArray);
        array_push($values, 
            $calendar->getNumAll(),
            $calendar->getNumPublic(),
            $calendar->getNumRegistered(),
            $calendar->getNumFriends());

        // 14 integers!
        $this->db->execute($q, 'iiiiiiiiiiiiii', ...$values);
    }
}
