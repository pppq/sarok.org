<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\DB;
use Sarok\Models\FeedStatus;
use Sarok\Models\Feed;
use DateTime;

class FeedRepository extends AbstractRepository
{
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
    
    public function getFeedsRequiringUpdate(DateTime $nextUpdateBefore) : array
    {
        $t_feeds = $this->getTableName();
        $selectColumns = $this->getColumnNames();
        $c_status = Feed::FIELD_STATUS;
        $c_nextUpdate = Feed::FIELD_NEXT_UPDATE;
        
        $q = "SELECT (`$selectColumns`) FROM `$t_feeds` WHERE `$c_status` <> ? AND `$c_nextUpdate` <= ? ORDER BY `$c_nextUpdate`";
        
        return $this->db->queryObjects($q, Feed::class, 'ss', 
            FeedStatus::BANNED, 
            Util::dateTimeToString($nextUpdateBefore));
    }

    public function update(DateTime $lastUpdate, DateTime $nextUpdate, string $lastEntry, int $ID) : int
    {
        $t_feeds = $this->getTableName();
        $c_lastUpdate = Feed::FIELD_LAST_UPDATE;
        $c_nextUpdate = Feed::FIELD_NEXT_UPDATE;
        $c_lastEntry = Feed::FIELD_LAST_ENTRY;
        $c_ID = Feed::FIELD_ID;
        
        $q = "UPDATE `$t_feeds` SET `$c_lastUpdate` = ?, `$c_nextUpdate` = ?, `$c_lastEntry` = ? " .
            "WHERE `$c_ID` = ? LIMIT 1";

        return $this->db->execute($q, 'sssi', 
            Util::dateTimeToString($lastUpdate), 
            Util::dateTimeToString($nextUpdate), 
            $lastEntry, 
            $ID);
    }
    
    public function delete(int $blogID) : int
    {
        $t_feeds = $this->getTableName();
        $c_blogID = Feed::FIELD_BLOG_ID;
        
        $q = "DELETE FROM `$t_feeds` WHERE `$c_blogID` = ? LIMIT 1";
        return $this->db->execute($q, 'i', $blogID);
    }
    
    public function save(Feed $feed) : int
    {
        $t_feeds = $this->getTableName();
        $feedArray = $feed->toArray();
        $insertColumns = array_keys($feedArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$t_feeds` (`$columnList`) VALUES ($placeholderList)";
        $values = array_values($feedArray);
        
        if ($feed->getID() < 0) {
            // Need auto-generated ID - don't send in the negative value
            array_shift($columnList);
            array_shift($placeholderList);
            array_shift($values);
            
            $this->db->execute($q, 'sissssss', ...$values);
            $feed->setID($this->db->getLastInsertID());
        } else {
            $this->db->execute($q, 'isissssss', ...$values);
        }
    }
}
