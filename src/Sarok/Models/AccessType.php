<?php namespace Sarok\Models;

class AccessType
{
    const ALL = 'ALL';
    const REGISTERED = 'REGISTERED';
    const FRIENDS = 'FRIENDS';
    /*
     * XXX: Constant is not named PRIVATE as it is a PHP keyword. While it is allowed to be used
     * as a constant, some parsers are confused by it.
     */
    const AUTHOR_ONLY = 'PRIVATE';
    const LIST = 'LIST';
}
