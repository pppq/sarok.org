<?php namespace Sarok\Repository;

use mysqli_result;
use Sarok\Util;
use Sarok\Service\DB;
use Sarok\Repository\EntryRepository;
use Sarok\Repository\AbstractRepository;
use Sarok\Models\Entry;
use Sarok\Models\Category;
use Sarok\Models\AccessType;
use DateTime;

class CategoryRepository extends AbstractRepository
{
    const TABLE_NAME = 'categories';
    
    private const COLUMN_NAMES = array(
        Category::FIELD_ENTRY_ID,
        Category::FIELD_NAME,
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
    
    public function getNamesByPrefix(string $namePrefix, int $limit = 10) : array
    {
        $c_Name = Category::FIELD_NAME;
        $t_categories = $this->getTableName();
        
        $q = "SELECT DISTINCT `$c_Name` FROM `$t_categories` WHERE `$c_Name` LIKE ? ORDER BY `$c_Name` LIMIT ?";
        $namePrefix .= '%';
        return $this->db->queryArray($q, "si", $namePrefix, $limit);
    }

    public function getNamesByEntryID(int $entryID, int $limit = 50) : array
    {
        $c_Name = Category::FIELD_NAME;
        $t_categories = $this->getTableName();
        $c_entryID = Category::FIELD_ENTRY_ID;

        $q = "SELECT `$c_Name` FROM `$t_categories` WHERE `$c_entryID` = ? ORDER BY `$c_Name` LIMIT ?";
        return $this->db->queryArray($q, 'ii', $entryID, $limit);
    }

    public function getCategoriesByEntryIDs(array $entryIDs) : array
    {
        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        $c_entryID = Category::FIELD_ENTRY_ID;
        $t_categories = $this->getTableName();
        $placeholderList = $this->toPlaceholderList($entryIDs);

        $q = "SELECT `$columnList` `$t_categories` WHERE `$c_entryID` IN ($placeholderList)";
        return $this->db->queryObjectsWithParams($q, Category::class, $entryIDs);
    }

    private function toTagCloud(mysqli_result $result) : array
    {
        $tagCloud = array();
        while ($row = $result->fetch_row()) {
            $tagCloud[$row[0]] = $row[1];
        }

        return $tagCloud;
    }

    public function getGlobalTagCloud() : array
    {
        $c_Name = Category::FIELD_NAME;
        $c_entryID = Category::FIELD_ENTRY_ID;
        $t_categories = $this->getTableName();

        $c_ID = Entry::FIELD_ID;
        $t_entries = EntryRepository::TABLE_NAME;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_access = Entry::FIELD_ACCESS;
        $access = AccessType::AUTHOR_ONLY;

        $entrySubquery = "SELECT `$c_ID` FROM `$t_entries` WHERE `$c_isTerminated` = 'N' AND `$c_access` != '$access'";
        $q = "SELECT `$c_Name`, COUNT(`$c_entryID`) FROM `$t_categories` WHERE `$c_entryID` IN ($entrySubquery) GROUP BY `$c_Name`";
        return $this->toTagCloud($this->db->queryArray($q));
    }

    public function getDiaryTagCloud(int $diaryID) : array
    {
        $c_Name = Category::FIELD_NAME;
        $c_entryID = Category::FIELD_ENTRY_ID;
        $t_categories = $this->getTableName();

        $c_ID = Entry::FIELD_ID;
        $t_entries = EntryRepository::TABLE_NAME;
        $c_diaryID = Entry::FIELD_DIARY_ID;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_access = Entry::FIELD_ACCESS;
        $access = AccessType::AUTHOR_ONLY;

        $entrySubquery = "SELECT `$c_ID` FROM `$t_entries` WHERE `$c_diaryID` = ? AND `$c_isTerminated` = 'N' AND `$c_access` != '$access'";
        $q = "SELECT `$c_Name`, COUNT(`$c_entryID`) FROM `$t_categories` WHERE `$c_entryID` IN ($entrySubquery) GROUP BY `$c_Name`";
        return $this->toTagCloud($this->db->queryArray($q, 'i', $diaryID));
    }

    public function getEntryIDSubquery(array $names) : string
    {
        $placeholderList = $this->toPlaceholderList($names);
        $c_entryID = Category::FIELD_ENTRY_ID;
        $t_categories = $this->getTableName();
        $c_Name = Category::FIELD_NAME;

        return "SELECT `$c_entryID` FROM `$t_categories` WHERE `$c_Name` IN ($placeholderList)";
    }

    public function deleteByEntryID(int $entryID, int $limit = 50) : int
    {
        $t_categories = $this->getTableName();
        $c_entryID = Category::FIELD_ENTRY_ID;
        
        $q = "DELETE FROM `$t_categories` WHERE `$c_entryID` = ? LIMIT ?";
        return $this->db->execute($q, 'ii', $entryID, $limit);
    }

    public function delete(int $entryID, string $name) : int
    {
        $t_categories = $this->getTableName();
        $c_entryID = Category::FIELD_ENTRY_ID;
        $c_Name = Category::FIELD_NAME;
        
        $q = "DELETE FROM `$t_categories` WHERE `$c_entryID` = ? AND `$c_Name` = ? LIMIT 1";
        return $this->db->execute($q, 'is', $entryID, $name);
    }
    
    public function save(Category $category) : int
    {
        $t_categories = $this->getTableName();
        $categoryArray = $category->toArray();
        $insertColumns = array_keys($categoryArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$t_categories` (`$columnList`) VALUES ($placeholderList)";
        $values = array_values($categoryArray);
        return $this->db->execute($q, 'is', ...$values);
    }
}
