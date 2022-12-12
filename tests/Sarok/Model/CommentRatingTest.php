<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\CommentRating;
use PHPUnit\Framework\TestCase;

final class CommentRatingTest extends TestCase
{
    public function testConstructor() : void
    {
        $cr = new CommentRating();
        $this->assertInstanceOf(DateTime::class, $cr->getCreateDate(), 
            "Datum should be an instance of DateTime.");
    }

    public function testDateSetter() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $cr = new CommentRating();
        $cr->setCreateDate($createDate);

        $this->assertEquals($createDate->getTimestamp(), $cr->getCreateDate()->getTimestamp(),
            "Timestamp should match value given in setter.");
    }

    public function testDateMagicSetter() : void
    {
        $createDate = Util::utcDateTimeFromString('2020-03-07 10:55:22');

        // Simulate what mysqli does when reading a row returned from a query
        $cr = new CommentRating();
        $cr->createDate = Util::dateTimeToString($createDate);

        $this->assertEquals($createDate->getTimestamp(), $cr->getCreateDate()->getTimestamp(),
            "Timestamp should match value set via magic method.");
    }

    public function testToArray() : void
    {
        $cr = new CommentRating();
        $cr->setCommentID(44147);
        $cr->setCreateDate(Util::utcDateTimeFromString('2020-03-07 10:55:22'));
        $cr->setRate(CommentRating::RATE_NEGATIVE);
        $cr->setUserID(32448);

        $this->assertEquals(array(
            CommentRating::FIELD_USER_ID => 32448,
            CommentRating::FIELD_COMMENT_ID => 44147,
            CommentRating::FIELD_RATE => 'sux',
            CommentRating::FIELD_CREATE_DATE => '2020-03-07 10:55:22',
        ), $cr->toArray(), "Array contents should match previously set values.");
    }
}
