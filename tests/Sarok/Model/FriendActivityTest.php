<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\FriendActivity;
use PHPUnit\Framework\TestCase;

final class FriendActivityTest extends TestCase
{
    public function testConstructor() : void
    {
        $fa = new FriendActivity();
        $this->assertInstanceOf(DateTime::class, $fa->getActivationDate(), 
            "Activation date should be an instance of DateTime.");
    }

    public function testDateFactoryMethod() : void
    {
        $activationDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $fa = FriendActivity::create(3442, 'login', $activationDate);

        $this->assertEquals($activationDate->getTimestamp(), $fa->getActivationDate()->getTimestamp(),
            "Timestamp should match value given in setter.");
    }

    public function testDateMagicSetter() : void
    {
        $activationDate = Util::utcDateTimeFromString('2020-03-07 10:55:22');

        // Simulate what mysqli does when reading a row returned from a query
        $fa = new FriendActivity();
        $fa->activationDate = Util::dateTimeToString($activationDate);

        $this->assertEquals($activationDate->getTimestamp(), $fa->getActivationDate()->getTimestamp(),
            "Timestamp should match value set via magic method.");
    }
}
