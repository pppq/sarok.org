<?php declare(strict_types=1);

namespace Sarok\Models;

/*
 * Table structure for `categories`:
 * 
 * `entryID` int(11)  NOT NULL DEFAULT '0',
 * `Name`    char(30) NOT NULL DEFAULT '',
 */
class Category
{
    const FIELD_ENTRY_ID = 'entryID';
    const FIELD_NAME = 'Name';

    private int $entryID = 0;
    private string $Name = '';

    public static function create(int $entryID, string $Name) : Category
    {
        $c = new Category();
        $c->setEntryID($entryID);
        $c->setName($Name);

        return $c;
    }

    public function getEntryID() : int
    {
        return $this->entryID;
    }

    public function setEntryID(int $entryID) : void
    {
        $this->entryID = $entryID;
    }

    public function getName() : string
    {
        return $this->Name;
    }

    public function setName(string $Name) : void
    {
        $this->Name = $Name;
    }

    public function toArray() : array
    {
        return array(
            self::FIELD_ENTRY_ID => $this->entryID,
            self::FIELD_NAME     => $this->Name,
        );
    }
}
