<?php namespace Sarok\Repository;

use Sarok\Service\DB;

abstract class AbstractRepository {
    
    protected DB $db;
    
    protected function __construct(DB $db) {
        $this->db = $db;
    }
    
    protected function toColumnList(array $columns) : string {
        return implode('`, `', $columns);
    }
    
    protected function toPlaceholderList(array $values) : string {
        // Produce the same number of question marks as there are values
        $placeholders = array_fill(0, count($values), '?');
        return implode(', ', $placeholders);
    }

    public abstract function getTableName() : string;
    
    public abstract function getColumnNames() : array;
}
