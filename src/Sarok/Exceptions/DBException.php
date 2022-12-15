<?php declare(strict_types=1);

namespace Sarok\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when a database operation can not be completed for any reason.
 */
final class DBException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
}
