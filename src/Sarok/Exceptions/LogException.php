<?php declare(strict_types=1);

namespace Sarok\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when an issue is encountered while adding an entry to the application log.
 */
class LogException extends Exception 
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
}
