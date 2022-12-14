<?php namespace Sarok\Repository;

use Sarok\DB;

abstract class AbstractRepository
{
    /** @var DB */
    protected DB $db;
    
    protected function __construct(DB $db)
    {
        $this->db = $db;
    }
    
    protected function toColumnList(array $columns) : string
    {
        return implode('`, `', $columns);
    }
    
    protected function toPlaceholderList(array $values) : string
    {
        // Produce the same number of question marks as there are values
        $placeholders = array_fill(0, count($values), '?');
        return implode(', ', $placeholders);
    }

    abstract protected function getTableName() : string;
    
    abstract protected function getColumnNames() : array;
}
