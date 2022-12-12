<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\Feed;
use PHPUnit\Framework\TestCase;
use Sarok\Models\FeedStatus;

final class FeedTest extends TestCase
{
    public function testConstructor() : void
    {
        $f = new Feed();
        
        $this->assertInstanceOf(DateTime::class, $f->getLastUpdate(), 
            "Last update should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $f->getNextUpdate(), 
            "Next update should be an instance of DateTime.");
    }

    public function testDateSetters() : void
    {
        $lastUpdate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $nextUpdate = Util::utcDateTimeFromString('2012-03-22');
        
        $f = new Feed();
        $f->setLastUpdate($lastUpdate);
        $f->setNextUpdate($nextUpdate);

        $this->assertEquals($lastUpdate->getTimestamp(), $f->getLastUpdate()->getTimestamp(),
            "Last update timestamp should match value given in setter.");
        $this->assertEquals($nextUpdate->getTimestamp(), $f->getNextUpdate()->getTimestamp(),
            "Next update timestamp should match value given in setter.");
    }

    public function testDateMagicSetters() : void
    {
        $lastUpdate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $nextUpdate = Util::utcDateTimeFromString('2012-03-22');

        // Simulate what mysqli does when reading a row returned from a query
        $f = new Feed();
        $f->lastUpdate = Util::dateTimeToString($lastUpdate);
        $f->nextUpdate = Util::dateToString($nextUpdate);

        $this->assertEquals($lastUpdate->getTimestamp(), $f->getLastUpdate()->getTimestamp(),
            "Last update timestamp should match value set via magic method.");
        $this->assertEquals($nextUpdate->getTimestamp(), $f->getNextUpdate()->getTimestamp(),
            "Next update timestamp should match value set via magic method.");
    }

    public function testStatusMagicSetter() : void
    {
        // Simulate what mysqli does when reading a row returned from a query
        $f = new Feed();
        $f->status = 'banned';

        $this->assertEquals(FeedStatus::BANNED, $f->getStatus(), 
            "Feed status should match string value set via magic method.");
    }

    public function testToArray() : void
    {
        $f = new Feed();
        $f->setBlogID(31238);
        $f->setComment('comment');
        $f->setContactEmail('contactEmail');
        $f->setFeedURL('feedURL');
        $f->setID(21336);
        $f->setLastEntry('lastEntry');
        $f->setLastUpdate(Util::utcDateTimeFromString('2022-12-12 16:39:00'));
        $f->setNextUpdate(Util::utcDateTimeFromString('2012-03-22'));
        $f->setStatus(FeedStatus::ALLOWED);

        $this->assertEquals(array(
            Feed::FIELD_ID => 21336,
            Feed::FIELD_FEED_URL => 'feedURL',
            Feed::FIELD_BLOG_ID => 31238,
            Feed::FIELD_LAST_UPDATE => '2022-12-12 16:39:00',
            Feed::FIELD_NEXT_UPDATE => '2012-03-22 00:00:00',
            Feed::FIELD_LAST_ENTRY => 'lastEntry',
            Feed::FIELD_CONTACT_EMAIL => 'contactEmail',
            Feed::FIELD_STATUS => 'allowed',
            Feed::FIELD_COMMENT => 'comment',
        ), $f->toArray(), "Array contents should match previously set values.");
    }
}
