<?php namespace Sarok\Models;

class CommentListType
{
    /**
     * Comments on all entries the current user has access to
     */
    const ALL_COMMENTS = 0;

    /**
     * Comments on entries that have "friends only" access and the current user is a
     * friend of the owner
     */

    const FRIENDS_COMMENTS = 1;
    
    /**
     * Comments on the user's entries (regardless of whether they are in the user's
     * diary or a guest post in a different diary)
     */
    const OWN_ENTRY_COMMENTS = 2;

    /**
     * Comments the user wrote
     */
    const MY_COMMENTS = 3;
}
