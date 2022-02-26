<?php declare(strict_types=1);

namespace Sarok\Models;

enum AccessType : string
{
    /**
     * Comment or entry access allowed for both registered and anonymous users.
     */
    case ALL = 'ALL';

    /**
     * Comment or entry access allowed for registered users only.
     */
    case REGISTERED = 'REGISTERED';

    /**
     * Comment or entry access allowed for the author and friends of the author.
     */
    case FRIENDS = 'FRIENDS';
    
    /**
     * Comment or entry access allowed for the author of the entry only.
     */
    case AUTHOR_ONLY = 'PRIVATE';

    /**
     * Comment or entry access allowed for the author of the entry and a list of users.
     */
    case LIST = 'LIST';
}
