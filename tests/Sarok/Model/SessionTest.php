<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\Session;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function testConstructor() : void
    {
        $s = new Session();
        $this->assertInstanceOf(DateTime::class, $s->getCreateDate(), 
            "Date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $s->getLoginDate(), 
            "Date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $s->getActivationDate(), 
            "Date should be an instance of DateTime.");
    }

    public function testDateSetter() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $loginDate = Util::utcDateTimeFromString('2021-07-20 11:24:30');
        $activationDate = Util::utcDateTimeFromString('2006-03-05 14:26:22');

        $s = new Session();
        $s->setCreateDate($createDate);
        $s->setLoginDate($loginDate);
        $s->setActivationDate($activationDate);

        $this->assertEquals($createDate->getTimestamp(), $s->getCreateDate()->getTimestamp(),
            "Creation date should match value given in setter.");
        $this->assertEquals($loginDate->getTimestamp(), $s->getLoginDate()->getTimestamp(),
            "Last login date should match value given in setter.");
        $this->assertEquals($activationDate->getTimestamp(), $s->getActivationDate()->getTimestamp(),
            "Last activity date should match value given in setter.");
    }

    public function testDateMagicSetter() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $loginDate = Util::utcDateTimeFromString('2021-07-20 11:24:30');
        $activationDate = Util::utcDateTimeFromString('2006-03-05 14:26:22');

        // Simulate what mysqli does when reading a row returned from a query
        $s = new Session();
        $s->createDate = Util::dateTimeToString($createDate);
        $s->loginDate = Util::dateTimeToString($loginDate);
        $s->activationDate = Util::dateTimeToString($activationDate);

        $this->assertEquals($createDate->getTimestamp(), $s->getCreateDate()->getTimestamp(),
            "Creation date should match value set via magic method.");
        $this->assertEquals($loginDate->getTimestamp(), $s->getLoginDate()->getTimestamp(),
            "Last login date should match value set via magic method.");
        $this->assertEquals($activationDate->getTimestamp(), $s->getActivationDate()->getTimestamp(),
            "Last activity date should match value set via magic method.");
    }

    public function testToArray() : void
    {
        $s = new Session();
        $s->setActivationDate(Util::utcDateTimeFromString('2006-03-05 14:26:22'));
        $s->setCreateDate(Util::utcDateTimeFromString('2022-12-12 16:39:00'));
        $s->setID(1336);
        $s->setIP('220.221.222.223');
        $s->setLoginDate(Util::utcDateTimeFromString('2021-07-20 11:24:30'));
        $s->setUserID(8451);

        $this->assertEquals(array(
            Session::FIELD_ID => 1336,
            Session::FIELD_USER_ID => 8451,
            Session::FIELD_CREATE_DATE => '2022-12-12 16:39:00',
            Session::FIELD_LOGIN_DATE => '2021-07-20 11:24:30',
            Session::FIELD_ACTIVATION_DATE => '2006-03-05 14:26:22',
            Session::FIELD_IP => '220.221.222.223',
        ), $s->toArray(), "Array contents should match previously set values.");
    }
}
