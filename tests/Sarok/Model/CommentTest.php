<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\Comment;
use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase
{
    public function testConstructor() : void
    {
        $co = new Comment();
        
        $this->assertInstanceOf(DateTime::class, $co->getCreateDate(), 
            "Creation date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $co->getDayDate(), 
            "Day date should be an instance of DateTime.");
    }

    public function testDateSetters() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $dayDate = Util::utcDateTimeFromString('2012-03-22');
        
        $co = new Comment();
        $co->setCreateDate($createDate);
        $co->setDayDate($dayDate);

        $this->assertEquals($createDate->getTimestamp(), $co->getCreateDate()->getTimestamp(),
            "Create date's timestamp should match value given in setter.");
        $this->assertEquals($dayDate->getTimestamp(), $co->getDayDate()->getTimestamp(),
            "Day date's timestamp should match value given in setter.");
    }

    public function testDateMagicSetters() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $dayDate = Util::utcDateTimeFromString('2012-03-22');

        // Simulate what mysqli does when reading a row returned from a query
        $co = new Comment();
        $co->createDate = Util::dateTimeToString($createDate);
        $co->dayDate = Util::dateToString($dayDate);

        $this->assertEquals($createDate->getTimestamp(), $co->getCreateDate()->getTimestamp(),
            "Create date's timestamp should match value set via magic method.");
        $this->assertEquals($dayDate->getTimestamp(), $co->getDayDate()->getTimestamp(),
            "Day date's timestamp should match value set via magic method.");
    }

    public function testTerminatedMagicSetter() : void
    {
        // Simulate what mysqli does when reading a row returned from a query
        $co = new Comment();
        $co->isTerminated = 'Y';

        $this->assertTrue($co->isTerminated(), 
            "Boolean value should be 'true' after setting string 'Y' via magic method.");
    }

    public function testToArray() : void
    {
        $co = new Comment();
        $co->setBody('body');
        $co->setCreateDate(Util::utcDateTimeFromString('2022-12-12 16:39:00'));
        $co->setDayDate(Util::utcDateTimeFromString('2012-03-22'));
        $co->setEntryID(12331);
        $co->setID(8832);
        $co->setIP('211.212.213.214');
        $co->setParentID(17764);
        $co->setRate(5);
        $co->setTerminated(true);
        $co->setUserID(88494);

        $this->assertEquals(array(
            Comment::FIELD_ID => 8832,
            Comment::FIELD_IS_TERMINATED => 'Y',
            Comment::FIELD_PARENT_ID => 17764,
            Comment::FIELD_ENTRY_ID => 0,
            Comment::FIELD_USER_ID => 88494,
            Comment::FIELD_CREATE_DATE => '2022-12-12 16:39:00',
            Comment::FIELD_BODY => 'body',
            Comment::FIELD_IP => '211.212.213.214',
            Comment::FIELD_DAY_DATE => '2012-03-22',
            Comment::FIELD_RATE => 5,
        ), $co->toArray(), "Array contents should match previously set values.");
    }
}
