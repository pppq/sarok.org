<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\AccessLog;
use PHPUnit\Framework\TestCase;

final class AccessLogTest extends TestCase
{
    public function testConstructor() : void
    {
        $al = new AccessLog();
        $this->assertInstanceOf(DateTime::class, $al->getDatum(), 
            "Datum should be an instance of DateTime.");
    }

    public function testDateSetter() : void
    {
        $datum = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $al = new AccessLog();
        $al->setDatum($datum);

        $this->assertEquals($datum->getTimestamp(), $al->getDatum()->getTimestamp(),
            "Timestamp should match value given in setter.");
    }

    public function testDateMagicSetter() : void
    {
        $datum = Util::utcDateTimeFromString('2020-03-07 10:55:22');

        // Simulate what mysqli does when reading a row returned from a query
        $al = new AccessLog();
        $al->datum = Util::dateTimeToString($datum);

        $this->assertEquals($datum->getTimestamp(), $al->getDatum()->getTimestamp(),
            "Timestamp should match value set via magic method.");
    }

    public function testToArray() : void
    {
        $al = new AccessLog();
        $al->setAction('action');
        $al->setDatum(Util::utcDateTimeFromString('2020-03-07 10:55:22'));
        $al->setIp('201.202.203.204');
        $al->setMicros(123456789012345678);
        $al->setNumQueries(100);
        $al->setReferrer('referrer');
        $al->setRunTime(876543210987654321);
        $al->setSessid(10012);
        $al->setUserCode(32110);

        $this->assertEquals(array(
            AccessLog::FIELD_DATUM => '2020-03-07 10:55:22',
            AccessLog::FIELD_MICROS => 123456789012345678,
            AccessLog::FIELD_SESSID => 10012,
            AccessLog::FIELD_ACTION => 'action',
            AccessLog::FIELD_REFERRER => 'referrer',
            AccessLog::FIELD_IP => '201.202.203.204',
            AccessLog::FIELD_USER_CODE => 32110,
            AccessLog::FIELD_RUNTIME => 876543210987654321,
            AccessLog::FIELD_NUM_QUERIES => 100,
        ), $al->toArray(), "Array contents should match previously set values.");
    }
}
