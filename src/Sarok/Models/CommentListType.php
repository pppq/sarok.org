<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Enumerates all comment lists that are shown on the dashboard.
 */
enum CommentListType : int
{
    /**
     * Comments on all entries the current user has access to
     */
    case ALL_COMMENTS = 0;

    /**
     * Comments on entries that have "friends only" access and the current user is a
     * friend of the owner
     */
    case FRIENDS_COMMENTS = 1;
    
    /**
     * Comments on the user's entries (regardless of whether they are in the user's
     * diary or a guest post in a different diary)
     */
    case OWN_ENTRY_COMMENTS = 2;

    /**
     * Comments the user wrote
     */
    case MY_COMMENTS = 3;
}
