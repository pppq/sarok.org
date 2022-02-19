<?php namespace Sarok\Repository;

use Sarok\Models\EntryAccess;
use Sarok\Models\Favourite;
use Sarok\Service\DB;
use Sarok\Util;
use DateTime;

class FavouriteRepository extends AbstractRepository
{
    const TABLE_NAME = 'favourites';
    
    private const COLUMN_NAMES = array(
        Favourite::FIELD_ENTRY_ID,
        Favourite::FIELD_USER_ID,
        Favourite::FIELD_LAST_VISITED,
        Favourite::FIELD_NEW_COMMENTS,
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
        return self::COLUMN_NAMES;
    }
    
    public function getFavouritedEntries(string $userID, bool $commentedAfterLastVisit) : array
    {
        $favourites = $this->getTableName();
        $entryIDColumn = Favourite::FIELD_ENTRY_ID;
        $userIDColumn = Favourite::FIELD_USER_ID;
        $lastVisited = Favourite::FIELD_LAST_VISITED;
        
        if ($commentedAfterLastVisit) {
            $op = '>=';
        } else {
            $op = '<';
        }
        
        // FIXME: Move query to EntryRepository?
        $commentedSubquery = "SELECT `ID` from `entries` WHERE `ID` = `f`.`$entryIDColumn` AND `isTerminated` = 'N' AND `lastComment` $op `f`.`$lastVisited`";
        $q="SELECT `$entryIDColumn` FROM `$favourites` AS `f` WHERE `$userIDColumn` = ? AND EXISTS ($commentedSubquery)";
        $result = $this->db->execute($q, 'i', $userID);
        
        $entryIDList = array();
        while ($entryID = $result->fetch_row()) {
            $entryIDList[] = $entryID[0];
        }
        
        return $entryIDList;
    }

    public function updateLastVisited(int $userID, int $entryID, DateTime $lastVisited) : int
    {
        $favourites = $this->getTableName();
        $lastVisitedColumn = Favourite::FIELD_LAST_VISITED;
        $userIDColumn = Favourite::FIELD_USER_ID;
        $entryIDColumn = Favourite::FIELD_ENTRY_ID;
        
        $q = "UPDATE `$favourites` SET `$lastVisitedColumn` = ? WHERE `$userIDColumn` = ? AND `$entryIDColumn` = ? LIMIT 1";
        $lastVisitedString = Util::dateTimeToString($lastVisited);
        return $this->db->execute($q, 'sii', $lastVisitedString, $userID, $entryID);
    }
    
    public function delete(int $userID, int $entryID) : int
    {
        $favourites = $this->getTableName();
        $userIDColumn = Favourite::FIELD_USER_ID;
        $entryIDColumn = Favourite::FIELD_ENTRY_ID;
        
        $q = "DELETE FROM `$favourites` WHERE `$userIDColumn` = ? AND `$entryIDColumn` = ? LIMIT 1";
        return $this->db->execute($q, 'ii', $userID, $entryID);
    }
    
    public function insert(Favourite $data) : int
    {
        $favourites = $this->getTableName();
        $favouriteArray = $data->toArray();
        $insertColumns = array_keys($favouriteArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $lastVisited = Favourite::FIELD_LAST_VISITED;
        
        $q = "INSERT INTO `$favourites`(`$columnList`) VALUES ($placeholderList) ON DUPLICATE KEY UPDATE `$lastVisited` = ?";
        $values = array_values($favouriteArray);
        // Add extra datetime value and 's' parameter type for ON DUPLICATE KEY UPDATE
        $values[] = Util::dateTimeToString($data->getLastVisited());
        return $this->db->execute($q, 'iisis', ...$values);
    }
}
