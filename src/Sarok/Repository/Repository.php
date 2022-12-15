<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\DB;

abstract class Repository
{
    protected DB $db;

    protected function __construct(DB $db)
    {
        $this->db = $db;
    }
    
    protected final function toColumnList(array $columns) : string
    {
        /* 
         * Produce inner backtick and comma-separated list of column names (outer 
         * backticks will need to be provided by callers)
         */
        return implode('`, `', $columns);
    }
    
    protected final function toPlaceholderList(array $values) : string
    {
        // Produce the same number of question marks as there are values
        $placeholders = array_fill(0, count($values), '?');
        return implode(', ', $placeholders);
    }
}
