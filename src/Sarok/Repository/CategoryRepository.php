<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\DB;
use Sarok\Repository\EntryRepository;
use Sarok\Repository\Repository;
use Sarok\Models\Entry;
use Sarok\Models\Category;
use Sarok\Models\AccessType;

final class CategoryRepository extends Repository
{
    public const TABLE_NAME = 'categories';
    
    public const COLUMN_NAMES = array(
        Category::FIELD_ENTRY_ID,
        Category::FIELD_NAME,
    );

    const MAX_TAGS_PER_ENTRY = 50;
    const MAX_SUGGESTIONS = 10;

    public function __construct(DB $db)
    {
        parent::__construct($db);
    }
    
    public function getNamesByPrefix(string $namePrefix, int $limit = self::MAX_SUGGESTIONS) : array
    {
        $c_Name = Category::FIELD_NAME;
        $t_categories = self::TABLE_NAME;
        
        $q = "SELECT DISTINCT `${c_Name}` FROM `${t_categories}` " .
            "WHERE `${c_Name}` LIKE ? " . 
            "ORDER BY `${c_Name}` LIMIT ?";

        return $this->db->queryArray($q, "si", 
            $namePrefix . '%', $limit);
    }

    public function getNamesByEntryID(int $entryID, int $limit = self::MAX_TAGS_PER_ENTRY) : array
    {
        $c_Name = Category::FIELD_NAME;
        $t_categories = self::TABLE_NAME;
        $c_entryID = Category::FIELD_ENTRY_ID;

        $q = "SELECT `${c_Name}` FROM `${t_categories}` " . 
            "WHERE `${c_entryID}` = ? " . 
            "ORDER BY `${c_Name}` LIMIT ?";

        return $this->db->queryArray($q, 'ii', 
            $entryID, $limit);
    }

    public function getCategoriesByEntryIDs(array $entryIDs) : array
    {
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        $c_entryID = Category::FIELD_ENTRY_ID;
        $t_categories = self::TABLE_NAME;
        $placeholderList = $this->toPlaceholderList($entryIDs);

        $q = "SELECT `${columnList}` FROM `${t_categories}` " . 
            "WHERE `${c_entryID}` IN (${placeholderList})";

        /** @var array<Category> */
        $categories = $this->db->queryObjectsWithParams($q, Category::class, $entryIDs);
        
        // Index category name by entry ID
        $categoriesByEntryIDs = array();

        foreach ($categories as $c) {
            $categoryEntryID = $c->getEntryID();
            $categoryName = $c->getName();

            if (!isset($categoriesByEntryIDs[$categoryEntryID])) {
                $categoriesByEntryIDs[$categoryEntryID] = array($categoryName);
            } else {
                $categoriesByEntryIDs[$categoryEntryID][] = $categoryName;
            }
        }

        // Set empty arrays for requested IDs that do not have corresponding categories
        foreach ($entryIDs as $entryID) {
            if (!isset($categoriesByEntryIDs[$entryID])) {
                $categoriesByEntryIDs[$entryID] = array();
            }
        }

        return $categoriesByEntryIDs;
    }

    public function getTagCloud(int $diaryID = 0) : array
    {
        /* 
         * This subquery should be in EntryRepository, but moving it there would introduce a 
         * circular dependency.
         */
        $c_ID = Entry::FIELD_ID;
        $t_entries = EntryRepository::TABLE_NAME;
        $c_isTerminated = Entry::FIELD_IS_TERMINATED;
        $c_access = Entry::FIELD_ACCESS;
        $authorOnly = AccessType::AUTHOR_ONLY->value;
        $c_diaryID = Entry::FIELD_DIARY_ID;

        $entrySubquery = "SELECT `${c_ID}` FROM `${t_entries}` " . 
            "WHERE `${c_isTerminated}` = 'N' AND `${c_access}` <> '{$authorOnly}'";

        if ($diaryID > 0) {
            $entrySubquery .= " AND `${c_diaryID}` = ?";
        }

        $c_Name = Category::FIELD_NAME;
        $c_entryID = Category::FIELD_ENTRY_ID;
        $t_categories = self::TABLE_NAME;
        
        $q = "SELECT `${c_Name}`, COUNT(`{$c_entryID}`) FROM `${t_categories}` " . 
            "WHERE `${c_entryID}` IN (${entrySubquery}) " . 
            "GROUP BY `${c_Name}`";
        
        if ($diaryID > 0) {
            $result = $this->db->query($q, 'i', $diaryID);
        } else {
            $result = $this->db->query($q);
        }

        $tagCloud = array();

        while ($row = $result->fetch_row()) {
            $tagCloud[$row[0]] = $row[1];
        }

        return $tagCloud;
    }

    public function getEntryIDSubquery(array $names) : string
    {
        $placeholderList = $this->toPlaceholderList($names);
        $c_entryID = Category::FIELD_ENTRY_ID;
        $t_categories = self::TABLE_NAME;
        $c_Name = Category::FIELD_NAME;

        return "SELECT `${c_entryID}` FROM `${t_categories}` " . 
            "WHERE `${c_Name}` IN (${placeholderList})";
    }

    public function deleteByEntryID(int $entryID, int $limit = self::MAX_TAGS_PER_ENTRY) : int
    {
        $t_categories = self::TABLE_NAME;
        $c_entryID = Category::FIELD_ENTRY_ID;
        
        $q = "DELETE FROM `${t_categories}` " . 
            "WHERE `${c_entryID}` = ? " . 
            "LIMIT ?";

        return $this->db->execute($q, 'ii', 
            $entryID, $limit);
    }

    public function delete(int $entryID, string $name) : int
    {
        $t_categories = self::TABLE_NAME;
        $c_entryID = Category::FIELD_ENTRY_ID;
        $c_Name = Category::FIELD_NAME;
        
        $q = "DELETE FROM `${t_categories}` " . 
            "WHERE `${c_entryID}` = ? AND `${c_Name}` = ? " .
            "LIMIT 1";

        return $this->db->execute($q, 'is', 
            $entryID, $name);
    }
    
    public function save(Category $category) : int
    {
        $t_categories = self::TABLE_NAME;
        $categoryArray = $category->toArray();
        $insertColumns = array_keys($categoryArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT IGNORE INTO `${t_categories}` (`${columnList}`) VALUES (${placeholderList})";
        
        $values = array_values($categoryArray);
        return $this->db->execute($q, 'is', ...$values);
    }
}
