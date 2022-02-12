<?php namespace Sarok;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

final class Util {

    const ZERO_DATE_TIME_VALUE = '0000-01-01 00:00:00';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    const DATE_FORMAT = 'Y-m-d';
    
    private static DateTimeZone $dtz;
    
    private static function getUtcTimeZone() : DateTimeZone {
        if (!isset(static::$dtz)) {
            static::$dtz = new DateTimeZone('UTC');
        }
        
        return static::$dtz;
    }
    
    public static function autoload(string $name) {
        require_once '../src/' . str_replace('\\', '/', $name) . '.php';
    }
    
    public static function zeroDateTime() : DateTime {
        return self::utcDateTimeFromString(self::ZERO_DATE_TIME_VALUE);
    }
    
    public static function utcDateTimeFromString(string $value = 'now') : DateTime {
        return new DateTime($value, static::getUtcTimeZone());
    }
    
    public static function dateTimeToString(DateTime $dt) : string {
        return $dt->format(self::DATE_TIME_FORMAT);
    }
    
    public static function dateToString(DateTime $dt) : string {
        return $dt->format(self::DATE_FORMAT);
    }
    
    public static function yesNoToBool(string $yesNo) : bool {
        if ($yesNo === 'Y') {
            return true;
        } else if ($yesNo === 'N') {
            return false;
        } else {
            throw new InvalidArgumentException("Input string must be 'Y' or 'N', got '$yesNo'.");
        }
    }

    public static function boolToYesNo(bool $value) : string {
        return $value ? 'Y' : 'N';
    }
    
    private function __construct() {
        // Not supposed to be instantiated
    }
}
