<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Models\Feed;
use Sarok\Service\DB;
use DateTime;

class FeedRepository extends AbstractRepository {

    const TABLE_NAME = 'feeds';
    
    private const COLUMN_NAMES = array(
        Feed::FIELD_ID,
        Feed::FIELD_FEED_URL,
        Feed::FIELD_BLOG_ID,
        Feed::FIELD_LAST_UPDATE,
        Feed::FIELD_NEXT_UPDATE,
        Feed::FIELD_LAST_ENTRY,
        Feed::FIELD_CONTACT_EMAIL,
        Feed::FIELD_STATUS,
        Feed::FIELD_COMMENT,
    );
    
    public function __construct(DB $db) {
        parent::__construct($db);
    }
    
    protected function getTableName() : string {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array {
        return self::COLUMN_NAMES;
    }
    
    public function getFeedsRequiringUpdate(DateTime $nextUpdateBefore) : array {
        $feeds = $this->getTableName();
        $selectColumns = $this->getColumnNames();
        $statusColumn = Feed::FIELD_STATUS;
        $nextUpdate = Feed::FIELD_NEXT_UPDATE;
        
        $q = "SELECT (`$selectColumns`) FROM `$feeds` WHERE `$statusColumn` <> ? AND `$nextUpdate` <= ? ORDER BY `$nextUpdate`";
        
        $statusIsNot = Feed::STATUS_BANNED;
        $nextUpdateBeforeString = Util::dateTimeToString($nextUpdateBefore);
        return $this->db->queryObjects($q, Feed::class, 'ss', $statusIsNot, $nextUpdateBeforeString);
    }

    public function update(DateTime $lastUpdate, DateTime $nextUpdate, string $lastEntry, int $ID) : int {
        $feeds = $this->getTableName();
        $lastUpdateColumn = Feed::FIELD_LAST_UPDATE;
        $nextUpdateColumn = Feed::FIELD_NEXT_UPDATE;
        $lastEntryColumn = Feed::FIELD_LAST_ENTRY;
        $IDColumn = Feed::FIELD_ID;
        
        $q = "UPDATE `$feeds` SET `$lastUpdateColumn` = ?, `$nextUpdateColumn` = ?, `$lastEntryColumn` = ? " .
            "WHERE `$IDColumn` = ? LIMIT 1";

        $lastUpdateString = Util::dateTimeToString($lastUpdate);
        $nextUpdateString = Util::dateTimeToString($nextUpdate);
        return $this->db->execute($q, 'sssi', $lastUpdateString, $nextUpdateString, $lastEntry, $ID);
    }
    
    public function delete(int $blogID) : int {
        $feeds = $this->getTableName();
        $blogIDColumn = Feed::FIELD_BLOG_ID;
        
        $q = "DELETE FROM `$feeds` WHERE `$blogIDColumn` = ? LIMIT 1";
        return $this->db->execute($q, 'i', $blogID);
    }
    
    public function insert(Feed $data) : int {
        $feeds = $this->getTableName();
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$feeds`(`$columnList`) VALUES ($placeholderList)";
        $values = $data->toArray();
        
        if ($data->getID() < 0) {
            // Need auto-generated ID - don't send in the negative value
            array_shift($columnList);
            array_shift($placeholderList);
            array_shift($values);
            
            $this->db->execute($q, 'sissssss', ...$values);
            $data->setID($this->db->getLastInsertID());
        } else {
            $this->db->execute($q, 'isissssss', ...$values);
        }
    }
}
