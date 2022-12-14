<?php namespace Sarok\Repository;

use Sarok\DB;
use Sarok\Models\CommentRating;
use Sarok\Repository\AbstractRepository;

class CommentRatingRepository extends AbstractRepository
{
    const TABLE_NAME = 'commentrates';
    
    private const COLUMN_NAMES = array(
        CommentRating::FIELD_USER_ID,
        CommentRating::FIELD_COMMENT_ID,
        CommentRating::FIELD_RATE,
        CommentRating::FIELD_CREATE_DATE,
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
    
    public function getRatedCommentIds(int $userID, array $allCommentIds) : array
    {
        $c_commentID = CommentRating::FIELD_COMMENT_ID;
        $t_commentrates = $this->getTableName();
        $c_userID = CommentRating::FIELD_USER_ID;
        $placeholderList = $this->toPlaceholderList($allCommentIds);
        
        $q = "SELECT `$c_commentID` FROM `$t_commentrates` WHERE `$c_userID` = ? AND `$c_commentID` IN ($placeholderList)";
        $params = &$allCommentIds;
        
        // Parameter 1 is the user ID
        array_unshift($params, (string) $userID);
        return $this->db->queryArrayWithParams($q, $params);
    }
    
    public function getCommentScore(int $commentID) : int
    {
        $c_rate = CommentRating::FIELD_RATE;
        $v_positive = CommentRating::RATE_POSITIVE;
        $t_commentrates = $this->getTableName();
        $c_commentID = CommentRating::FIELD_COMMENT_ID;
        
        $q = "SELECT SUM(IF(`$c_rate` = '$v_positive', +1, -1)) " .
            "FROM `$t_commentrates` " .
            "WHERE `$c_commentID` = ? " .
            "GROUP BY `$c_commentID`";
        
        $result = $this->db->query($q, 'i', $commentID);
        if ($score = $result->fetch_row) {
            return $score[0];
        }
        
        return 0;
    }
    
    public function save(CommentRating $commentRating) : int
    {
        $t_commentrates = $this->getTableName();
        $commentRatingArray = $commentRating->toArray();
        $insertColumns = array_keys($commentRatingArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `$t_commentrates` (`$columnList`) VALUES ($placeholderList)";
        $values = array_values($commentRatingArray);
        return $this->db->execute($q, 'iiss', ...$values);
    }
}
