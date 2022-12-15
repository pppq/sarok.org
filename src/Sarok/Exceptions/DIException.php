<?php declare(strict_types=1);

namespace Sarok\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when an issue is encountered during the dependency injection process.
 */
final class DIException extends Exception 
{
    public const CLASS_NOT_EXISTS     = 100;
    public const ZERO_ARG_CONSTRUCTOR = 101;
    public const NO_VALUE_FOUND       = 102;
    public const OTHER_CONSTRUCTOR    = 103;

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
}
