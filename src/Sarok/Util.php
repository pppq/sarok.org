<?php namespace Sarok;

use DateTime;
use DateTimeZone;

final class Util {

    const ZERO_DATE_TIME_VALUE = '0000-01-01 00:00:00';
    const DATE_FORMAT = 'Y-m-d H:i:s';
    
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
        return $dt->format(self::DATE_FORMAT);
    }
    
    private function __construct() {
        // Not supposed to be instantiated
    }
}
