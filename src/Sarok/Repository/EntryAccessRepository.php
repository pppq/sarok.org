<?php namespace Sarok\Repository;

use DateTime;
use Sarok\Util;
use Sarok\Service\DB;
use Sarok\Models\EntryAccess;
use Sarok\Repository\SessionRepository;
use Sarok\Repository\AbstractRepository;

class EntryAccessRepository extends AbstractRepository
{
    const TABLE_NAME = 'entryaccess';
    
    private const COLUMN_NAMES = array(
        EntryAccess::FIELD_ENTRY_ID,
        EntryAccess::FIELD_USER_ID,
    );
    
    /* @var SessionRepository */
    private SessionRepository $sessionRepository;
    
    public function __construct(DB $db, SessionRepository $sessionRepository)
    {
        parent::__construct($db);
        $this->sessionRepository = $sessionRepository;
    }
    
    protected function getTableName() : string
    {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array
    {
        return self::COLUMN_NAMES;
    }
    
    public function getActiveUsersWithAccess(string $entryID, DateTime $lastActivityAfter) : array
    {
        $c_userID = EntryAccess::FIELD_USER_ID;
        $t_entryaccess = $this->getTableName();
        $c_entryID = EntryAccess::FIELD_ENTRY_ID;
        
        $activeUserIdsQuery = $this->sessionRepository->getActiveUserIdsQuery();
        $q = "SELECT DISTINCT `$c_userID` FROM `$t_entryaccess` WHERE `$c_entryID` = ? AND `$c_userID` IN ($activeUserIdsQuery)";

        return $this->db->queryArray($q, 'is', 
            $entryID,
            Util::dateTimeToString($lastActivityAfter));
    }

    public function getExistsQuery(string $entryAlias = 'e') : string
    {
        $t_entryaccess = $this->getTableName();
        $c_userID = EntryAccess::FIELD_USER_ID;
        $c_entryID = EntryAccess::FIELD_ENTRY_ID;
        
        // FIXME: Use Entry::FIELD_ID for the field name
        return "SELECT 1 FROM `$t_entryaccess` AS `ea` WHERE `ea`.`$c_userID` = ? AND `ea`.`$c_entryID` = `$entryAlias`.`ID` LIMIT 1";
    }
    
    public function deleteByEntryIDs(array $entryIDs) : int
    {
        $t_entryaccess = $this->getTableName();
        $c_entryID = EntryAccess::FIELD_ENTRY_ID;
        $placeholderList = $this->toPlaceholderList($entryIDs);
        
        $q = "DELETE FROM `$t_entryaccess` WHERE `$c_entryID` IN ($placeholderList)";
        return $this->db->executeWithParams($q, $entryIDs);
    }
    
    public function save(EntryAccess $entryAccess) : int
    {
        $t_entryaccess = $this->getTableName();
        $entryAccessArray = $entryAccess->toArray();
        $insertColumns = array_keys($entryAccessArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$t_entryaccess` (`$columnList`) VALUES ($placeholderList)";
        $values = array_values($entryAccessArray);
        return $this->db->execute($q, 'ii', ...$values);
    }
}
