<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Models\AccessLog;
use Sarok\Service\DB;
use DateTime;

class AccessLogRepository extends AbstractRepository {

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
    
    public function __construct(DB $db) {
        parent::__construct($db);
    }
    
    protected function getTableName() : string {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array {
        return self::$COLUMN_NAMES;
    }
    
    public function getIpAddressesOfUser(int $userCode) : array {
        $ip = AccessLog::FIELD_IP;
        $accesslog = $this->getTableName();
        $userCodeColumn = AccessLog::FIELD_USER_CODE;
        
        $q = "SELECT DISTINCT `$ip` FROM `$accesslog` WHERE `$userCodeColumn` = ?";
        $result = $this->db->query($q, 'i', $userCode);
        
        $ipAddressList = array();
        while ($ipAddress = $result->fetch_row()) {
            $ipAddressList[] = $ipAddress[0];
        }
        
        return $ipAddressList;
    }
    
    public function getUserActionsFromDate(DateTime $datum, int $limit = 2500) : array {
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
        $accesslog = $this->getTableName();
        $datumColumn = AccessLog::FIELD_DATUM;
        $action = AccessLog::FIELD_ACTION;
        
        $q = "SELECT `$columnList` FROM `$accesslog` WHERE `$datumColumn` >= ? AND `$action` LIKE 'users/_%' ORDER BY `$datumColumn` LIMIT ?";
        $datumString = Util::dateTimeToString($datum);
        return $this->db->queryObjects($q, AccessLog::class, 'si', $datumString, $limit);
    }
    
    public function insert(AccessLog $data) : int {
        $accesslog = $this->getTableName();
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$accesslog`(`$columnList`) VALUES ($placeholderList)";
        return $this->db->execute($q, 'siisssiii', ...$data->toArray());
    }
}
