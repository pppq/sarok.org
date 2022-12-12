<?php declare(strict_types=1); 

namespace Sarok\Model;

use DateTime;
use Sarok\Util;
use Sarok\Models\EntryDigest;
use PHPUnit\Framework\TestCase;
use Sarok\Models\AccessType;

final class EntryDigestTest extends TestCase
{
    public function testConstructor() : void
    {
        $ed = new EntryDigest();
        
        $this->assertInstanceOf(DateTime::class, $ed->getCreateDate(), 
            "Creation date should be an instance of DateTime.");
        $this->assertInstanceOf(DateTime::class, $ed->getLastUsed(), 
            "'Last used' date should be an instance of DateTime.");
    }

    public function testDateSetters() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $lastUsed = Util::utcDateTimeFromString('2012-03-22 21:54:22');
        
        $ed = new EntryDigest();
        $ed->setCreateDate($createDate);
        $ed->setLastUsed($lastUsed);

        $this->assertEquals($createDate->getTimestamp(), $ed->getCreateDate()->getTimestamp(),
            "Create date's timestamp should match value given in setter.");
        $this->assertEquals($lastUsed->getTimestamp(), $ed->getLastUsed()->getTimestamp(),
            "'Last used' date's timestamp should match value given in setter.");
    }

    public function testDateMagicSetters() : void
    {
        $createDate = Util::utcDateTimeFromString('2022-12-12 16:39:00');
        $lastUsed = Util::utcDateTimeFromString('2012-03-22 21:54:22');

        // Simulate what mysqli does when reading a row returned from a query
        $ed = new EntryDigest();
        $ed->createDate = Util::dateTimeToString($createDate);
        $ed->lastUsed = Util::dateTimeToString($lastUsed);

        $this->assertEquals($createDate->getTimestamp(), $ed->getCreateDate()->getTimestamp(),
            "Create date's timestamp should match value set via magic method.");
        $this->assertEquals($lastUsed->getTimestamp(), $ed->getLastUsed()->getTimestamp(),
            "'Last used' date's timestamp should match value set via magic method.");
    }

    public function testToArray() : void
    {
        $ed = new EntryDigest();
        $ed->setAccess(AccessType::AUTHOR_ONLY);
        $ed->setBody('body');
        $ed->setCreateDate(Util::utcDateTimeFromString('2022-12-12 16:39:00'));
        $ed->setDiaryID('diary');
        $ed->setID(8832);
        $ed->setLastUsed(Util::utcDateTimeFromString('2012-03-22 21:54:22'));
        $ed->setOwnerID(34242);
        $ed->setUserID('user');

        $this->assertEquals(array(
            EntryDigest::FIELD_ID => 8832,
            EntryDigest::FIELD_OWNER_ID => 34242,
            EntryDigest::FIELD_USER_ID => 'user',
            EntryDigest::FIELD_DIARY_ID => 'diary',
            EntryDigest::FIELD_CREATE_DATE => '2022-12-12 16:39:00',
            EntryDigest::FIELD_ACCESS => 'PRIVATE',
            EntryDigest::FIELD_BODY => 'body',
            EntryDigest::FIELD_LAST_USED => '2012-03-22 21:54:22',
        ), $ed->toArray(), "Array contents should match previously set values.");
    }
}
