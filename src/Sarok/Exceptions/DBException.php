<?php namespace Sarok\Exceptions;

use Exception;

/**
 * Thrown when a database operation can not be completed for any reason.
 */
class DBException extends Exception {
    
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
