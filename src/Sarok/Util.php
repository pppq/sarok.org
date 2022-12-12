<?php declare(strict_types=1);

namespace Sarok;

use InvalidArgumentException;
use DateTimeZone;
use DateTime;
use Exception;

/**
 * A fine selection of utility methods that couldn't be placed in another class.
 */
final class Util 
{
    const ZERO_DATE_TIME_VALUE = '0000-01-01 00:00:00';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    const DATE_FORMAT = 'Y-m-d';
    
    private static DateTimeZone $utcTimeZone;
    private static DateTime $zeroDateTime;

    private static function getUtcTimeZone() : DateTimeZone 
    {
        if (!isset(static::$utcTimeZone)) {
            static::$utcTimeZone = new DateTimeZone('UTC');
        }
        
        return static::$utcTimeZone;
    }

    public static function zeroDateTime() : DateTime 
    {
        if (!isset(static::$zeroDateTime)) {
            static::$zeroDateTime = self::utcDateTimeFromString(self::ZERO_DATE_TIME_VALUE);
        }

        return static::$zeroDateTime;
    }
    
    public static function utcDateTimeFromString(string $value = 'now') : DateTime 
    {
        try {
            return new DateTime($value, static::getUtcTimeZone());
        } catch (Exception $e) {
            throw new InvalidArgumentException("Input string must be a valid date, got '${value}'.", 0, $e);
        }
    }
    
    public static function dateTimeToString(DateTime $dt) : string 
    {
        return $dt->format(self::DATE_TIME_FORMAT);
    }
    
    public static function dateToString(DateTime $dt) : string 
    {
        return $dt->format(self::DATE_FORMAT);
    }
    
    public static function yesNoToBool(string $yesNo) : bool 
    {
        if ($yesNo === 'Y') {
            return true;
        } else if ($yesNo === 'N') {
            return false;
        } else {
            throw new InvalidArgumentException("Input string must be 'Y' or 'N', got '${yesNo}'.");
        }
    }

    public static function boolToYesNo(bool $value) : string 
    {
        return $value ? 'Y' : 'N';
    }
        
    public static function autoload(string $className) : void
    {
        require_once '../src/' . str_replace('\\', '/', $className) . '.php';
    }

    private function __construct() {
        // Not supposed to be instantiated
    }
}
