<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\CommentDigest;
use PHPUnit\Framework\TestCase;
use Sarok\Models\AccessType;
use Sarok\Models\CommentDigestCategory;

final class CommentDigestTest extends TestCase
{
    public function testConstructor() : void
    {
        $cd = new CommentDigest();
        
        $this->assertInstanceOf(DateTime::class, $cd->getCreateDate(), 
            "Creation date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $cd->getLastUsed(), 
            "'Last used' date should be an instance of DateTime.");
    }

    public function testDateSetters() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $lastUsed = Util::utcDateTimeFromString('2012-03-22 21:54:22');
        
        $cd = new CommentDigest();
        $cd->setCreateDate($createDate);
        $cd->setLastUsed($lastUsed);

        $this->assertEquals($createDate->getTimestamp(), $cd->getCreateDate()->getTimestamp(),
            "Create date's timestamp should match value given in setter.");
        $this->assertEquals($lastUsed->getTimestamp(), $cd->getLastUsed()->getTimestamp(),
            "'Last used' date's timestamp should match value given in setter.");
    }

    public function testDateMagicSetters() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $lastUsed = Util::utcDateTimeFromString('2012-03-22 21:54:22');

        // Simulate what mysqli does when reading a row returned from a query
        $cd = new CommentDigest();
        $cd->createDate = Util::dateTimeToString($createDate);
        $cd->lastUsed = Util::dateTimeToString($lastUsed);

        $this->assertEquals($createDate->getTimestamp(), $cd->getCreateDate()->getTimestamp(),
            "Create date's timestamp should match value set via magic method.");
        $this->assertEquals($lastUsed->getTimestamp(), $cd->getLastUsed()->getTimestamp(),
            "'Last used' date's timestamp should match value set via magic method.");
    }

    public function testToArray() : void
    {
        $cd = new CommentDigest();
        $cd->setAccess(AccessType::AUTHOR_ONLY);
        $cd->setBody('body');
        $cd->setCategory(CommentDigestCategory::COMMENTS_OF_ENTRIES);
        $cd->setCreateDate(Util::utcDateTimeFromString('2022-12-12 16:39:00'));
        $cd->setDiaryID('diary');
        $cd->setEntryID(12331);
        $cd->setID(8832);
        $cd->setLastUsed(Util::utcDateTimeFromString('2012-03-22 21:54:22'));
        $cd->setOwnerID(34242);
        $cd->setUserID('user');

        $this->assertEquals(array(
            CommentDigest::FIELD_CATEGORY => 'commentsOfEntries',
            CommentDigest::FIELD_ID => 8832,
            CommentDigest::FIELD_OWNER_ID => 34242,
            CommentDigest::FIELD_USER_ID => 'user',
            CommentDigest::FIELD_DIARY_ID => 'diary',
            CommentDigest::FIELD_ENTRY_ID => 12331,
            CommentDigest::FIELD_CREATE_DATE => '2022-12-12 16:39:00',
            CommentDigest::FIELD_ACCESS => 'PRIVATE',
            CommentDigest::FIELD_BODY => 'body',
            CommentDigest::FIELD_LAST_USED => '2012-03-22 21:54:22',
        ), $cd->toArray(), "Array contents should match previously set values.");
    }
}
