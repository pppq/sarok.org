<?php namespace Sarok\Repository;

use Sarok\Models\EntryAccess;
use Sarok\Service\DB;
use DateTime;
use Sarok\Util;

class EntryAccessRepository extends AbstractRepository {

    const TABLE_NAME = 'entryaccess';
    
    private const COLUMN_NAMES = array(
        EntryAccess::FIELD_ENTRY_ID,
        EntryAccess::FIELD_USER_ID,
    );
    
    private SessionRepository $sessionRepository;
    
    public function __construct(DB $db, SessionRepository $sessionRepository) {
        parent::__construct($db);
        $this->sessionRepository = $sessionRepository;
    }
    
    protected function getTableName() : string {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array {
        return self::COLUMN_NAMES;
    }
    
    public function getActiveUsersWithAccess(string $entryID, DateTime $lastActivityAfter) : array {
        $userIDColumn = EntryAccess::FIELD_USER_ID;
        $entryaccess = $this->getTableName();
        $entryIDColumn = EntryAccess::FIELD_ENTRY_ID;
        
        $activeUserIdsQuery = $this->sessionRepository->getActiveUserIdsQuery();
        $q = "SELECT DISTINCT `$userIDColumn` FROM `$entryaccess` WHERE `$entryIDColumn` = ? AND `$userIDColumn` IN ($activeUserIdsQuery)";

        $lastActivityString = Util::dateTimeToString($lastActivityAfter);
        $result = $this->db->execute($q, 'is', $entryID, $lastActivityString);
        
        $userIDList = array();
        while ($userID = $result->fetch_row()) {
            $userIDList[] = $userID[0];
        }
        
        return $userIDList;
    }

    public function getExistsQuery(string $entryAlias = 'e') : string {
        $entryaccess = $this->getTableName();
        $userID = EntryAccess::FIELD_USER_ID;
        $entryID = EntryAccess::FIELD_ENTRY_ID;
        
        // FIXME: Use Entry::FIELD_ID for the field name
        return "SELECT 1 FROM `$entryaccess` WHERE `$entryaccess`.`$userID` = ? AND `$entryaccess`.`$entryID` = `$entryAlias`.`ID` LIMIT 1";
    }
    
    public function deleteByEntryIDs(array $entryIDs) : int {
        $entryaccess = $this->getTableName();
        $entryID = EntryAccess::FIELD_ENTRY_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        
        $q = "DELETE FROM `$entryaccess` WHERE `$entryID` IN ($placeholderList)";
        return $this->db->executeWithParams($q, $entryIDs);
    }
    
    public function insert(EntryAccess $data) : int {
        $entryaccess = $this->getTableName();
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$entryaccess`(`$columnList`) VALUES ($placeholderList)";
        $values = $data->toArray();
        return $this->db->execute($q, 'ii', ...$values);
    }
}
