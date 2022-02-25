<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Service\DB;
use Sarok\Repository\AbstractRepository;
use Sarok\Models\AccessLog;
use DateTime;

class AccessLogRepository extends AbstractRepository
{
    const TABLE_NAME = 'accesslog';
    
    private const COLUMN_NAMES = array(
        AccessLog::FIELD_DATUM,
        AccessLog::FIELD_MICROS,
        AccessLog::FIELD_SESSID,
        AccessLog::FIELD_ACTION,
        AccessLog::FIELD_REFERRER,
        AccessLog::FIELD_IP,
        AccessLog::FIELD_USER_CODE,
        AccessLog::FIELD_RUNTIME,
        AccessLog::FIELD_NUM_QUERIES,
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
        return self::$COLUMN_NAMES;
    }
    
    public function getIpAddressesOfUser(int $userCode) : array
    {
        $c_ip = AccessLog::FIELD_IP;
        $t_accesslog = $this->getTableName();
        $c_userCode = AccessLog::FIELD_USER_CODE;
        
        $q = "SELECT DISTINCT `$c_ip` FROM `$t_accesslog` WHERE `$c_userCode` = ?";
        return $this->db->queryArray($q, 'i', $userCode);
    }
    
    public function getUserActionsFromDate(DateTime $datum, int $limit = 2500) : array
    {
        // XXX: not all fields are populated
        $selectColumns = array(
            AccessLog::FIELD_DATUM,
            AccessLog::FIELD_SESSID,
            AccessLog::FIELD_ACTION,
            AccessLog::FIELD_REFERRER,
            AccessLog::FIELD_IP,
            AccessLog::FIELD_USER_CODE,
        );
        
        $columnList = $this->toColumnList($selectColumns);
        $t_accesslog = $this->getTableName();
        $c_datum = AccessLog::FIELD_DATUM;
        $c_action = AccessLog::FIELD_ACTION;
        
        $q = "SELECT `$columnList` FROM `$t_accesslog` " .
            "WHERE `$c_datum` >= ? AND `$c_action` LIKE 'users/_%' " .
            "ORDER BY `$c_datum` LIMIT ?";
        
        return $this->db->queryObjects($q, AccessLog::class, 'si', 
            Util::dateTimeToString($datum), 
            $limit);
    }
    
    public function save(AccessLog $accessLog) : int
    {
        $t_accesslog = $this->getTableName();
        $accessLogArray = $accessLog->toArray();
        $insertColumns = array_keys($accessLogArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$t_accesslog` (`$columnList`) VALUES ($placeholderList)";
        $values = array_values($accessLogArray);
        return $this->db->execute($q, 'siisssiii', ...$values);
    }
}
