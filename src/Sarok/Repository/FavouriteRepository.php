<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Service\DB;
use Sarok\Repository\EntryRepository;
use Sarok\Repository\AbstractRepository;
use Sarok\Models\Favourite;
use Sarok\Models\EntryAccess;
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
    
    /** @var EntryRepository */
    private EntryRepository $entryRepository;

    public function __construct(DB $db, EntryRepository $entryRepository)
    {
        parent::__construct($db);
        $this->entryRepository = $entryRepository;
    }
    
    protected function getTableName() : string
    {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array
    {
        return self::COLUMN_NAMES;
    }
    
    public function getFavouritedEntries(int $userID, bool $commentedAfterLastVisit) : array
    {
        $t_favourites = $this->getTableName();
        $c_entryID = Favourite::FIELD_ENTRY_ID;
        $c_userID = Favourite::FIELD_USER_ID;
        $c_lastVisited = Favourite::FIELD_LAST_VISITED;
        
        if ($commentedAfterLastVisit) {
            $op = '>=';
        } else {
            $op = '<';
        }
        
        $commentedSubquery = $this->entryRepository->getCommentedSubquery($op, "`f`.`$c_entryID`", "`f`.`$c_lastVisited`");
        $q = "SELECT `$c_entryID` FROM `$t_favourites` AS `f` WHERE `$c_userID` = ? AND EXISTS ($commentedSubquery)";
        return $this->db->queryArray($q, 'i', $userID);
    }

    public function updateLastVisited(int $userID, int $entryID, DateTime $lastVisited) : int
    {
        $t_favourites = $this->getTableName();
        $c_lastVisited = Favourite::FIELD_LAST_VISITED;
        $c_userID = Favourite::FIELD_USER_ID;
        $c_entryID = Favourite::FIELD_ENTRY_ID;
        
        $q = "UPDATE `$t_favourites` SET `$c_lastVisited` = ? WHERE `$c_userID` = ? AND `$c_entryID` = ? LIMIT 1";
        return $this->db->execute($q, 'sii', Util::dateTimeToString($lastVisited), $userID, $entryID);
    }
    
    public function delete(int $userID, int $entryID) : int
    {
        $t_favourites = $this->getTableName();
        $c_userID = Favourite::FIELD_USER_ID;
        $c_entryID = Favourite::FIELD_ENTRY_ID;
        
        $q = "DELETE FROM `$t_favourites` WHERE `$c_userID` = ? AND `$c_entryID` = ? LIMIT 1";
        return $this->db->execute($q, 'ii', $userID, $entryID);
    }
    
    public function save(Favourite $favourite) : int
    {
        $t_favourites = $this->getTableName();
        $favouriteArray = $favourite->toArray();
        $insertColumns = array_keys($favouriteArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        $c_lastVisited = Favourite::FIELD_LAST_VISITED;
        
        $q = "INSERT INTO `$t_favourites` (`$columnList`) VALUES ($placeholderList) ON DUPLICATE KEY UPDATE `$c_lastVisited` = ?";
        $values = array_values($favouriteArray);
        // Add extra datetime value and 's' parameter type for ON DUPLICATE KEY UPDATE
        $values[] = Util::dateTimeToString($favourite->getLastVisited());
        return $this->db->execute($q, 'iisis', ...$values);
    }
}
