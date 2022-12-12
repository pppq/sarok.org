<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Points to the section the comment digest appears in.
 */
enum CommentDigestCategory : string
{
    /**
     * Contributes to the "all comments" section of the dashboard.
     * 
     * @see CommentListType::ALL_COMMENTS
     * @see CommentListType::FRIENDS_COMMENTS
     */
    case COMMENTS = 'comments';

    /**
     * Contributes to the "comments on my entries" section of the dashboard.
     * 
     * @see CommentListType::OWN_ENTRY_COMMENTS
     */
    case COMMENTS_OF_ENTRIES = 'commentsOfEntries';

    /**
     * Contributes to the "my comments" section of the dashboard.
     * 
     * @see CommentListType::MY_COMMENTS
     */
    case MY_COMMENTS = 'myComments';
}
