<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testConstructor() : void
    {
        $u = new User();
        $this->assertInstanceOf(DateTime::class, $u->getCreateDate(), 
            "Date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $u->getLoginDate(), 
            "Date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $u->getActivationDate(), 
            "Date should be an instance of DateTime.");
    }

    public function testDateSetter() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $loginDate = Util::utcDateTimeFromString('2021-07-20 11:24:30');
        $activationDate = Util::utcDateTimeFromString('2006-03-05 14:26:22');

        $u = new User();
        $u->setCreateDate($createDate);
        $u->setLoginDate($loginDate);
        $u->setActivationDate($activationDate);

        $this->assertEquals($createDate->getTimestamp(), $u->getCreateDate()->getTimestamp(),
            "Creation date should match value given in setter.");
        $this->assertEquals($loginDate->getTimestamp(), $u->getLoginDate()->getTimestamp(),
            "Last login date should match value given in setter.");
        $this->assertEquals($activationDate->getTimestamp(), $u->getActivationDate()->getTimestamp(),
            "Last activity date should match value given in setter.");
    }

    public function testDateMagicSetter() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $loginDate = Util::utcDateTimeFromString('2021-07-20 11:24:30');
        $activationDate = Util::utcDateTimeFromString('2006-03-05 14:26:22');

        // Simulate what mysqli does when reading a row returned from a query
        $u = new User();
        $u->createDate = Util::dateTimeToString($createDate);
        $u->loginDate = Util::dateTimeToString($loginDate);
        $u->activationDate = Util::dateTimeToString($activationDate);

        $this->assertEquals($createDate->getTimestamp(), $u->getCreateDate()->getTimestamp(),
            "Creation date should match value set via magic method.");
        $this->assertEquals($loginDate->getTimestamp(), $u->getLoginDate()->getTimestamp(),
            "Last login date should match value set via magic method.");
        $this->assertEquals($activationDate->getTimestamp(), $u->getActivationDate()->getTimestamp(),
            "Last activity date should match value set via magic method.");
    }

    public function testBooleanMagicSetters() : void
    {
        // Simulate what mysqli does when reading a row returned from a query
        $u = new User();
        $u->isTerminated = 'Y';

        $this->assertTrue($u->isTerminated(), 
            "Soft deletion flag should match value set via magic method.");
    }

    public function testToArray() : void
    {
        $u = new User();
        $u->setActivationDate(Util::utcDateTimeFromString('2006-03-05 14:26:22'));
        $u->setCreateDate(Util::utcDateTimeFromString('2022-12-12 16:39:00'));
        $u->setID(1336);
        $u->setLogin('login');
        $u->setLoginDate(Util::utcDateTimeFromString('2021-07-20 11:24:30'));
        $u->setPass('again-not-a-hashed-password');
        $u->setTerminated(true);

        $this->assertEquals(array(
            User::FIELD_ID => 1336,
            User::FIELD_LOGIN => 'login',
            User::FIELD_PASS => 'again-not-a-hashed-password',
            User::FIELD_CREATE_DATE => '2022-12-12 16:39:00',
            User::FIELD_LOGIN_DATE => '2021-07-20 11:24:30',
            User::FIELD_ACTIVATION_DATE => '2006-03-05 14:26:22',
            User::FIELD_IS_TERMINATED => 'Y',
        ), $u->toArray(), "Array contents should match previously set values.");
    }

    public function testUserData() : void
    {
        $u = new User();
        
        $this->assertEquals('default', $u->getUserData('some-key', 'default'),
            "Property not defined previously should return the default value.");
        $this->assertEquals('', $u->putUserData('some-key', 'some-value'),
            "Property not defined previously should return an empty string as the previous value.");
        $this->assertEquals('some-value', $u->putUserData('some-key', 'another-value'),
            "Defined proeprty should return the value that was last set.");
        $this->assertEquals('another-value', $u->getUserData('some-key', 'default'),
            "Defined property should return the value that was last set.");
        $this->assertEquals(array('some-key' => 'another-value'), $u->flushUserData(), 
            "Array contents should match 'dirty' property values.");
        $this->assertEquals(array(), $u->flushUserData(), 
            "Properties marked as 'dirty' should reset after each flush call.");
    }
}
