<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Service\DB;
use Sarok\Repository\AccessLogRepository;
use Sarok\Models\AccessLog;
use Sarok\DIContainer;
use PHPUnit\Framework\TestCase;

final class AccessLogRepositoryTest extends TestCase
{
    private static DIContainer $container;

    public static function setUpBeforeClass() : void
    {
        $container = new DIContainer();
        
        $container->put('logPath', '../logs/log.txt');
        $container->put('logLevel', 5);

        $container->put("db_host", "mysql");
        $container->put("db_name", "sarok");
        $container->put("db_user", "sarok");
        $container->put("db_password", "such_sec0re");

        self::$container = $container;
    }

    /** @var AccessLogRepository */
    private AccessLogRepository $alr;

    public function setUp() : void
    {
        $this->alr = self::$container->get(AccessLogRepository::class);

        $db = self::$container->get(DB::class);
        $t_accesslog = AccessLogRepository::TABLE_NAME;
        $db->execute("TRUNCATE TABLE `$t_accesslog`");
    }

    public function testGetIpAddressesOfUser() : void
    {
        $al1 = new AccessLog();
        $al1->setUserCode(100);
        $al1->setIp("10.0.0.1");
        $this->alr->save($al1);

        $al2 = new AccessLog();
        $al2->setUserCode(101);
        $al2->setIp("10.0.0.2");
        $this->alr->save($al2);

        $al3 = new AccessLog();
        $al3->setUserCode(100);
        $al3->setIp("10.0.0.3");
        $this->alr->save($al3);

        $ipAddresses = $this->alr->getIpAddressesOfUser(100);
        $this->assertEqualsCanonicalizing([ $al1->getIp(), $al3->getIp() ], $ipAddresses,
            "Both IP addresses of user '100' should appear in the result set.");
    }

    public function testGetUserActionsFromDate() : void
    {
        $al1 = new AccessLog();
        $al1->setDatum(Util::utcDateTimeFromString("2022-02-25 12:34:56"));
        $al1->setAction("users/testuser/m_123467");
        $al1->setIp("192.168.200.201");
        $al1->setMicros(1234);
        $al1->setNumQueries(123);
        $al1->setReferrer("https://www.example.com/");
        $al1->setRunTime(123456);
        $al1->setSessid("123456789012345678");
        $al1->setUserCode(123456789);
        $this->alr->save($al1);
        
        $al2 = new AccessLog();
        $al2->setDatum(Util::utcDateTimeFromString("2022-02-26 21:43:10"));
        $al2->setAction("users/usertest/m_234568");
        $al2->setIp("192.168.200.202");
        $al2->setMicros(4321);
        $al2->setNumQueries(321);
        $al2->setReferrer("https://www.example.org/");
        $al2->setRunTime(654321);
        $al2->setSessid("1122334455");
        $al2->setUserCode(10002);
        $this->alr->save($al2);

        $als = $this->alr->getUserActionsFromDate(Util::utcDateTimeFromString("2022-01-01"));
        $this->assertEquals(2, count($als), "Number of access log entries should be 2.");

        $expectedDates = array($al1->getDatum(), $al2->getDatum());
        $actualDates = array_map(function (AccessLog $al) { return $al->getDatum(); }, $als);
        $this->assertEquals($expectedDates, $actualDates, "Date of access log entries should be in ascending order.");

        $als = $this->alr->getUserActionsFromDate(Util::utcDateTimeFromString("2022-02-25"), 1);
        $this->assertEquals(1, count($als), "Number of access log entries with limit 1 should be 1.");

        $this->assertEquals($al1->getDatum(), $als[0]->getDatum(), "Date of access log entry should match.");
        $this->assertEquals($al1->getSessid(), $als[0]->getSessid(), "Session ID of access log entry should match.");
        $this->assertEquals($al1->getAction(), $als[0]->getAction(), "Action of access log entry should match.");
        $this->assertEquals($al1->getReferrer(), $als[0]->getReferrer(), "Referrer of access log entry should match.");
        $this->assertEquals($al1->getIp(), $als[0]->getIp(), "IP address of access log entry should match.");
        $this->assertEquals($al1->getUserCode(), $als[0]->getUserCode(), "User ID of access log entry should match.");

        $als = $this->alr->getUserActionsFromDate(Util::utcDateTimeFromString("2022-02-26"));
        $this->assertEquals(1, count($als), "Number of access log entries matching lower date bound should be 1.");

        $this->assertEquals($al2->getDatum(), $als[0]->getDatum(), "Date of access log entry should match.");
        $this->assertEquals($al2->getSessid(), $als[0]->getSessid(), "Session ID of access log entry should match.");
        $this->assertEquals($al2->getAction(), $als[0]->getAction(), "Action of access log entry should match.");
        $this->assertEquals($al2->getReferrer(), $als[0]->getReferrer(), "Referrer of access log entry should match.");
        $this->assertEquals($al2->getIp(), $als[0]->getIp(), "IP address of access log entry should match.");
        $this->assertEquals($al2->getUserCode(), $als[0]->getUserCode(), "User ID of access log entry should match.");
    }

    public function testSave() : void
    {
        $al = new AccessLog();

        $al->setAction("users/testuser/m_123467");
        $al->setIp("192.168.200.201");
        $al->setMicros(1234);
        $al->setNumQueries(123);
        $al->setReferrer("https://www.example.com/");
        $al->setRunTime(123456);
        $al->setSessid("123456789012345678");
        $al->setUserCode(123456789);

        $updatedRows = $this->alr->save($al);
        $this->assertEquals(1, $updatedRows, 
            "Saving an access log entry should modify a row.");
    }
}
