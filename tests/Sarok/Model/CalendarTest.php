<?php declare(strict_types=1); 

namespace Sarok\Model;

use Sarok\Models\Calendar;
use PHPUnit\Framework\TestCase;

final class CalendarTest extends TestCase
{
    public function testToArray() : void
    {
        $ca = new Calendar();
        $ca->setUserID(12313);
        $ca->setY(2009);
        $ca->setM(11);
        $ca->setD(30);
        $ca->setNumAll(120);
        $ca->setNumRegistered(44);
        $ca->setNumFriends(89);
        $ca->setNumPublic(18);
        $ca->setNumMailsReceived(4);
        $ca->setNumMailsSent(8);

        $this->assertEquals(array(
            Calendar::FIELD_USER_ID => 12313,
            Calendar::FIELD_Y => 2009,
            Calendar::FIELD_M => 11,
            Calendar::FIELD_D => 30,
            Calendar::FIELD_NUM_PUBLIC => 18,
            Calendar::FIELD_NUM_REGISTERED => 44,
            Calendar::FIELD_NUM_FRIENDS => 89,
            Calendar::FIELD_NUM_ALL => 120,
            Calendar::FIELD_NUM_MAILS_RECEIVED => 4,
            Calendar::FIELD_NUM_MAILS_SENT => 8,
        ), $ca->toArray(), "Array contents should match previously set values.");
    }
}
