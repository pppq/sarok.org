<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\DB;
use Sarok\Models\CommentRating;
use Sarok\Repository\Repository;

final class CommentRatingRepository extends Repository
{
    public const TABLE_NAME = 'commentrates';
    
    public const COLUMN_NAMES = array(
        CommentRating::FIELD_USER_ID,
        CommentRating::FIELD_COMMENT_ID,
        CommentRating::FIELD_RATE,
        CommentRating::FIELD_CREATE_DATE,
    );
    
    public function __construct(DB $db)
    {
        parent::__construct($db);
    }
    
    public function getRatedCommentIds(int $userID, array $allCommentIds) : array
    {
        $c_commentID = CommentRating::FIELD_COMMENT_ID;
        $t_commentrates = self::TABLE_NAME;
        $c_userID = CommentRating::FIELD_USER_ID;
        $placeholderList = $this->toPlaceholderList($allCommentIds);
        
        $q = "SELECT `${c_commentID}` FROM `${t_commentrates}` " . 
            "WHERE `${c_userID}` = ? AND `${c_commentID}` IN (${placeholderList})";
        
        $params = array($userID, ...$allCommentIds);
        return $this->db->queryArrayWithParams($q, $params);
    }
    
    public function getCommentScore(int $commentID) : int
    {
        $c_rate = CommentRating::FIELD_RATE;
        $v_positive = CommentRating::RATE_POSITIVE;
        $t_commentrates = self::TABLE_NAME;
        $c_commentID = CommentRating::FIELD_COMMENT_ID;
        
        $q = "SELECT SUM(IF(`${c_rate}` = '${v_positive}', +1, -1)) " .
            "FROM `${t_commentrates}` " .
            "WHERE `${c_commentID}` = ? " .
            "GROUP BY `${c_commentID}`";
        
        return $this->db->queryInt($q, 0, 'i', $commentID);
    }
    
    public function save(CommentRating $commentRating) : int
    {
        $t_commentrates = self::TABLE_NAME;
        $commentRatingArray = $commentRating->toArray();
        $insertColumns = array_keys($commentRatingArray);
        $columnList = $this->toColumnList($insertColumns);
        $placeholderList = $this->toPlaceholderList($insertColumns);
        
        $q = "INSERT INTO `${t_commentrates}` (`${columnList}`) VALUES (${placeholderList})";
        
        $values = array_values($commentRatingArray);
        return $this->db->execute($q, 'iiss', ...$values);
    }
}
