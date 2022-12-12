<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\Entry;
use PHPUnit\Framework\TestCase;
use Sarok\Models\AccessType;

final class EntryTest extends TestCase
{
    public function testConstructor() : void
    {
        $e = new Entry();
        
        $this->assertInstanceOf(DateTime::class, $e->getCreateDate(), 
            "Creation date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $e->getModifyDate(), 
            "Modification date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $e->getDayDate(), 
            "Day date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $e->getLastComment(), 
            "Last comment date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $e->getLastVisit(), 
            "Last visit date should be an instance of DateTime.");
    }

    public function testDateSetters() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $modifyDate = Util::utcDateTimeFromString('2012-03-22 21:54:22');
        $dayDate = Util::utcDateTimeFromString('2012-03-22');
        $lastComment = Util::utcDateTimeFromString('2020-11-16 01:24:32');
        $lastVisit = Util::utcDateTimeFromString('2018-05-09 20:17:57');
        
        $e = new Entry();
        $e->setCreateDate($createDate);
        $e->setModifyDate($modifyDate);
        $e->setDayDate($dayDate);
        $e->setLastComment($lastComment);
        $e->setLastVisit($lastVisit);

        $this->assertEquals($createDate->getTimestamp(), $e->getCreateDate()->getTimestamp(),
            "Create date timestamp should match value given in setter.");
        $this->assertEquals($modifyDate->getTimestamp(), $e->getModifyDate()->getTimestamp(),
            "Modify date timestamp should match value given in setter.");
        $this->assertEquals($dayDate->getTimestamp(), $e->getDayDate()->getTimestamp(),
            "Day date timestamp should match value given in setter.");
        $this->assertEquals($lastComment->getTimestamp(), $e->getLastComment()->getTimestamp(),
            "Last comment timestamp should match value given in setter.");
        $this->assertEquals($lastVisit->getTimestamp(), $e->getLastVisit()->getTimestamp(),
            "Last visit timestamp should match value given in setter.");
    }

    public function testDateMagicSetters() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $modifyDate = Util::utcDateTimeFromString('2012-03-22 21:54:22');
        $dayDate = Util::utcDateTimeFromString('2012-03-22');
        $lastComment = Util::utcDateTimeFromString('2020-11-16 01:24:32');
        $lastVisit = Util::utcDateTimeFromString('2018-05-09 20:17:57');

        // Simulate what mysqli does when reading a row returned from a query
        $e = new Entry();
        $e->createDate = Util::dateTimeToString($createDate);
        $e->modifyDate = Util::dateTimeToString($modifyDate);
        $e->dayDate = Util::dateToString($dayDate);
        $e->lastComment = Util::dateTimeToString($lastComment);
        $e->lastVisit = Util::dateTimeToString($lastVisit);

        $this->assertEquals($createDate->getTimestamp(), $e->getCreateDate()->getTimestamp(),
            "Create date timestamp should match value set via magic method.");
        $this->assertEquals($modifyDate->getTimestamp(), $e->getModifyDate()->getTimestamp(),
            "Modify date timestamp should match value set via magic method.");
        $this->assertEquals($dayDate->getTimestamp(), $e->getDayDate()->getTimestamp(),
            "Day date timestamp should match value set via magic method.");
        $this->assertEquals($lastComment->getTimestamp(), $e->getLastComment()->getTimestamp(),
            "Last comment timestamp should match value set via magic method.");
        $this->assertEquals($lastVisit->getTimestamp(), $e->getLastVisit()->getTimestamp(),
            "Last visit timestamp should match value set via magic method.");
    }

    public function testSetBooleanMagicSetter() : void
    {
        // Simulate what mysqli does when reading a row returned from a query
        $e = new Entry();
        $e->isTerminated = 'Y';

        $this->assertTrue($e->isTerminated(), 
            "Boolean value should match string set via magic method.");
    }

    public function testSetFloatMagicSetters() : void
    {
        // Simulate what mysqli does when reading a row returned from a query
        $e = new Entry();
        $e->posX = '3.5346';
        $e->posY = '8.3421';

        $this->assertEquals(3.5346, $e->getPosX(), 
            "X float value should match string set via magic method.");
        $this->assertEquals(8.3421, $e->getPosY(), 
            "Y float value should match string set via magic method.");            
    }

    public function testEnumMagicSetters() : void
    {
        // Simulate what mysqli does when reading a row returned from a query
        $e = new Entry();
        $e->access = 'PRIVATE';
        $e->comments = 'LIST';

        $this->assertEquals(AccessType::AUTHOR_ONLY, $e->getAccess(), 
            "Access type should match string value set via magic method.");
        $this->assertEquals(AccessType::LIST, $e->getComments(), 
            "Comment access type should match string value set via magic method.");
    }    

    public function testToArray() : void
    {
        $e = new Entry();
        $e->setAccess(AccessType::AUTHOR_ONLY);
        $e->setBody('body');
        $e->setBody2('body2');
        $e->setCategory(99);
        $e->setComments(AccessType::AUTHOR_ONLY);
        $e->setCreateDate(Util::utcDateTimeFromString('2022-12-12 16:39:00'));
        $e->setDayDate(Util::utcDateTimeFromString('2022-03-20'));
        $e->setDiaryID(21339);
        $e->setID(12336);
        $e->setLastComment(Util::utcDateTimeFromString('2020-11-16 01:24:32'));
        $e->setLastVisit(Util::utcDateTimeFromString('2018-05-09 20:17:57'));
        $e->setModeratorComment('moderatorComment');
        $e->setModifyDate(Util::utcDateTimeFromString('2012-03-22 21:54:22'));
        $e->setNumComments(73);
        $e->setPosX(3.554);
        $e->setPosY(9.531);
        $e->setRssURL('rssUrl');
        $e->setTerminated(true);
        $e->setUserID(12384);

        $this->assertEquals(array(
            Entry::FIELD_ID => 12336,
            Entry::FIELD_DIARY_ID => 21339,
            Entry::FIELD_USER_ID => 12384,
            Entry::FIELD_CREATE_DATE => '2022-12-12 16:39:00',
            Entry::FIELD_MODIFY_DATE => '2012-03-22 21:54:22',
            Entry::FIELD_ACCESS => 'PRIVATE',
            Entry::FIELD_COMMENTS => 'PRIVATE',
            Entry::FIELD_TITLE => '',
            Entry::FIELD_BODY_1 => 'body',
            Entry::FIELD_BODY_2 => 'body2',
            Entry::FIELD_NUM_COMMENTS => 73,
            Entry::FIELD_LAST_COMMENT => '2020-11-16 01:24:32',
            Entry::FIELD_LAST_VISIT => '2018-05-09 20:17:57',
            Entry::FIELD_IS_TERMINATED => 'Y',
            Entry::FIELD_MODERATOR_COMMENT => 'moderatorComment',
            Entry::FIELD_CATEGORY => 99,
            Entry::FIELD_DAY_DATE => '2022-03-20',
            Entry::FIELD_RSS_URL => 'rssUrl',
            Entry::FIELD_POS_X => 3.554,
            Entry::FIELD_POS_Y => 9.531,
        ), $e->toArray(), "Array contents should match previously set values.");
    }
}
