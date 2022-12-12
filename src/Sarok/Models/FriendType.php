<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Enumerates relationship types between users.
 */
enum FriendType : string
{
    /** 
     * Allows access to "friends only" entries of `friendOf` to `userID`. 
     */
    case FRIEND = 'friend';

    /** 
     * Disallows access to any entries in the diary of `friendOf`, as well as 
     * any entries written by `friendOf`, to `userID`. Where possible, pre-existing 
     * activity of `userID` will be hidden.
     */
    case BANNED = 'banned';

    /**
     * _Currently not implemented._ Used when `friendOf` is interested in 
     * `userID`'s activity (new comments and entries) but does not want to grant 
     * additional access to "friends only" entries of theirs.
     */
    case READER = 'read';
}
