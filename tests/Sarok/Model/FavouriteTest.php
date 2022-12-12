<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\Favourite;
use PHPUnit\Framework\TestCase;

final class FavouriteTest extends TestCase
{
    public function testConstructor() : void
    {
        $f = new Favourite();
        $this->assertInstanceOf(DateTime::class, $f->getLastVisited(), 
            "Datum should be an instance of DateTime.");
    }

    public function testDateSetter() : void
    {
        $lastVisited = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $f = new Favourite();
        $f->setLastVisited($lastVisited);

        $this->assertEquals($lastVisited->getTimestamp(), $f->getLastVisited()->getTimestamp(),
            "Timestamp should match value given in setter.");
    }

    public function testDateMagicSetter() : void
    {
        $lastVisited = Util::utcDateTimeFromString('2020-03-07 10:55:22');

        // Simulate what mysqli does when reading a row returned from a query
        $f = new Favourite();
        $f->lastVisited = Util::dateTimeToString($lastVisited);

        $this->assertEquals($lastVisited->getTimestamp(), $f->getLastVisited()->getTimestamp(),
            "Timestamp should match value set via magic method.");
    }

    public function testToArray() : void
    {
        $f = new Favourite();
        $f->setEntryID(21335);
        $f->setLastVisited(Util::utcDateTimeFromString('2020-03-07 10:55:22'));
        $f->setNewComments(22);
        $f->setUserID(1441);

        $this->assertEquals(array(
            Favourite::FIELD_USER_ID => 1441,
            Favourite::FIELD_ENTRY_ID => 21335,
            Favourite::FIELD_LAST_VISITED => '2020-03-07 10:55:22',
            Favourite::FIELD_NEW_COMMENTS => 22,
        ), $f->toArray(), "Array contents should match previously set values.");
    }
}
