<?php namespace Sarok\Exceptions;

use Exception;

/**
 * Thrown when an issue is encountered while adding an entry to the application log.
 */
class LogException extends Exception {

    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
