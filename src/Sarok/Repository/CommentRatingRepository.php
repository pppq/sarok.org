<?php namespace Sarok\Repository;

use Sarok\Models\CommentRating;
use Sarok\Service\DB;

class CommentRatingRepository extends AbstractRepository {

    const TABLE_NAME = 'commentrates';
    
    private const COLUMN_NAMES = array(
        CommentRating::FIELD_USER_ID,
        CommentRating::FIELD_COMMENT_ID,
        CommentRating::FIELD_RATE,
        CommentRating::FIELD_CREATE_DATE,
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
    
    public function getRatedCommentIds(int $userID, array $allCommentIds) : array {
        $commentID = CommentRating::FIELD_COMMENT_ID;
        $commentrates = $this->getTableName();
        $userIDColumn = CommentRating::FIELD_USER_ID;
        $placeholderList = $this->toPlaceholderList($allCommentIds);
        
        $q = "SELECT `$commentID` FROM `$commentrates` WHERE `$userIDColumn` = ? AND `$commentID` IN ($placeholderList)";
        $params = &$allCommentIds;
        
        // Parameter 1 is the user ID
        array_unshift($params, (string) $userID);
        $result = $this->db->queryWithParams($q, $params);
        
        $ratedCommentIds = array();
        while ($ipAddress = $result->fetch_row()) {
            $ratedCommentIds[] = $ipAddress[0];
        }
        
        return $ratedCommentIds;
    }
    
    public function getCommentScore(int $commentID) : int {
        $rate = CommentRating::FIELD_RATE;
        $positive = CommentRating::RATE_POSITIVE;
        $commentrates = $this->getTableName();
        $commentIDColumn = CommentRating::FIELD_COMMENT_ID;
        
        $q = "SELECT SUM(IF(`$rate` = '$positive', +1, -1)) " .
            "FROM `$commentrates` " .
            "WHERE `$commentIDColumn` = ? " .
            "GROUP BY `$commentIDColumn`";
        
        $result = $this->db->query($q, 'i', $commentID);
        if ($score = $result->fetch_row) {
            return $score[0];
        }
        
        return 0;
    }
    
    public function insert(CommentRating $data) : int {
        $commentrates = $this->getTableName();
        $insertColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$commentrates`(`$columnList`) VALUES ($placeholderList)";
        return $this->db->execute($q, 'iiss', ...$data->toArray());
    }
}
