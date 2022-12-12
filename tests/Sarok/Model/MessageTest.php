<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testConstructor() : void
    {
        $m = new Message();
        $this->assertInstanceOf(DateTime::class, $m->getDate(), 
            "Date should be an instance of DateTime.");
    }

    public function testDateSetter() : void
    {
        $date = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $m = new Message();
        $m->setDate($date);

        $this->assertEquals($date->getTimestamp(), $m->getDate()->getTimestamp(),
            "Timestamp should match value given in setter.");
    }

    public function testDateMagicSetter() : void
    {
        $date = Util::utcDateTimeFromString('2020-03-07 10:55:22');

        // Simulate what mysqli does when reading a row returned from a query
        $m = new Message();
        $m->Date = Util::dateTimeToString($date);

        $this->assertEquals($date->getTimestamp(), $m->getDate()->getTimestamp(),
            "Timestamp should match value set via magic method.");
    }

    public function testBooleanMagicSetters() : void
    {
        // Simulate what mysqli does when reading a row returned from a query
        $m = new Message();
        $m->isDeletedByRecipient = 'Y';
        $m->isDeletedBySender = 'Y';
        $m->isRead = 'Y';

        $this->assertTrue($m->isDeletedByRecipient(), 
            "Deleted by recipient flag should match value set via magic method.");
        $this->assertTrue($m->isDeletedBySender(), 
            "Deleted by sender flag should match value set via magic method.");
        $this->assertTrue($m->isRead(), 
            "Read flag should match value set via magic method.");
    }

    public function testToArray() : void
    {
        $m = new Message();
        $m->setBody('body');
        $m->setDate(Util::utcDateTimeFromString('2020-03-07 10:55:22'));
        $m->setDeletedByRecipient(true);
        $m->setDeletedBySender(false);
        $m->setRead(true);
        $m->setRecipient(48852);
        $m->setReplyOn(12337);
        $m->setSender(32420);
        $m->setTitle('title');

        $this->assertEquals(array(
            Message::FIELD_ID => -1,
            Message::FIELD_RECIPIENT => 48852,
            Message::FIELD_SENDER => 32420,
            Message::FIELD_DATE => '2020-03-07 10:55:22',
            Message::FIELD_TITLE => 'title',
            Message::FIELD_BODY => 'body',
            Message::FIELD_IS_READ => 'Y',
            Message::FIELD_IS_DELETED_BY_RECIPIENT => 'Y',
            Message::FIELD_IS_DELETED_BY_SENDER => 'N',
            Message::FIELD_REPLY_ON => 12337,
        ), $m->toArray(), "Array contents should match previously set values.");
    }
}
