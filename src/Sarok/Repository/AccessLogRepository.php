<?php namespace Sarok\Repository;

use Sarok\Models\AccessLog;
use Sarok\Service\DB;
use DateTime;

class AccessLogRepository extends AbstractRepository {

    const TABLE_NAME = 'accesslog';
    
    public function __construct(DB $db) {
        parent::__construct($db);
    }
    
    public function getTableName() : string {
        return self::TABLE_NAME;
    }
    
    public function getColumnNames() : array {
        return array(
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
    }
    
    public function getIpAddressesOfUser(int $userId) : array {
        $ip = AccessLog::FIELD_IP;
        $accesslog = self::TABLE_NAME;
        $userCode = AccessLog::FIELD_USER_CODE;
        
        $q = "SELECT DISTINCT `$ip` FROM `$accesslog` WHERE `$userCode` = ?";
        $result = $this->db->query($q, 'i', $userId);
        
        $ipAddressList = array();
        while ($ipAddress = $result->fetch_row()) {
            $ipAddressList[] = $ipAddress[0];
        }
        
        return $ipAddressList;
    }
    
    public function getUserActions(DateTime $fromDateTime, int $limit = 2500) : array {
        $selectColumns = array(
            AccessLog::FIELD_DATUM,
            AccessLog::FIELD_SESSID,
            AccessLog::FIELD_ACTION,
            AccessLog::FIELD_REFERRER,
            AccessLog::FIELD_IP,
            AccessLog::FIELD_USER_CODE,
        );
        
        $columnList = $this->toColumnList($selectColumns);
        $accesslog = self::TABLE_NAME;
        $datum = AccessLog::FIELD_DATUM;
        $action = AccessLog::FIELD_ACTION;
        
        $q = "SELECT `$columnList` FROM `$accesslog` WHERE `$datum` >= ? AND `$action` LIKE 'users/_%' ORDER BY `$datum` LIMIT ?";
        $fromDateTimeString = $fromDateTime->format('Y-m-d H:i:s');
        return $this->db->queryObjects($q, AccessLog::class, 'si', $fromDateTimeString, $limit);
    }
    
    public function save(AccessLog $data) : bool {
        $accesslog = self::TABLE_NAME;
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$accesslog`(`$columnList`) VALUES ($placeholderList)";
        return $this->db->execute($q, 'siisssiii', ...$data->toArray());
    }
}
