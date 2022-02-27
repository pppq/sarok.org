<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Repository\RepositoryTest;
use Sarok\Repository\EntryRepository;
use Sarok\Repository\CommentDigestRepository;
use Sarok\Models\Entry;
use Sarok\Models\CommentDigestCategory;
use Sarok\Models\CommentDigest;
use Sarok\Models\AccessType;

final class CommentDigestRepositoryTest extends RepositoryTest
{
    private CommentDigestRepository $cdr;

    public function setUp() : void
    {
        $this->clearTable(CommentDigestRepository::TABLE_NAME);
        $this->cdr = self::get(CommentDigestRepository::class);
    }

    public function testUpdateLastUsed() : void
    {
        $cd1 = new CommentDigest();
        $cd1->setAccess(AccessType::REGISTERED);
        $cd1->setEntryID(1000);
        $cd1->setID(2000);

        $cd2 = new CommentDigest();
        $cd2->setAccess(AccessType::FRIENDS);
        $cd2->setEntryID(1001);
        $cd2->setID(2001);
        
        $this->cdr->save($cd1);
        $this->cdr->save($cd2);

        $affectedRows = $this->cdr->updateLastUsed(
            Util::utcDateTimeFromString("2022-02-27 14:30"), 
            CommentDigestCategory::COMMENTS,
            array(2000));

        $this->assertEquals(1, $affectedRows,
            "Updating 'last update' timestamp on comment digest should modify one row.");
    }

    public function testUpdateAccess() : void
    {
        $cd1 = new CommentDigest();
        $cd1->setAccess(AccessType::REGISTERED);
        $cd1->setEntryID(1000);
        $cd1->setID(2000);

        $cd2 = new CommentDigest();
        $cd2->setAccess(AccessType::FRIENDS);
        $cd2->setEntryID(1001);
        $cd2->setID(2001);
        
        $this->cdr->save($cd1);
        $this->cdr->save($cd2);

        $affectedRows = $this->cdr->updateAccess(AccessType::AUTHOR_ONLY, array(1000, 1001));
        $this->assertEquals(2, $affectedRows,
            "Updating access on comment digests should modify both rows.");
    }

    public function testSave() : void
    {
        $cd = new CommentDigest();
        $cd->setAccess(AccessType::REGISTERED);
        $cd->setBody("body");
        $cd->setCategory(CommentDigestCategory::MY_COMMENTS);
        $cd->setCreateDate(Util::utcDateTimeFromString("2022-02-27 10:56:00"));
        $cd->setDiaryID("diaryID");
        $cd->setEntryID(1000);
        $cd->setID(2000);
        $cd->setLastUsed(Util::utcDateTimeFromString("2022-02-27 11:56:00"));
        $cd->setOwnerID(3000);
        $cd->setUserID("userID");
        
        $affectedRows = $this->cdr->save($cd);
        $this->assertEquals(1, $affectedRows,
            "Saving a comment digest should insert a row.");

        $affectedRows = $this->cdr->save($cd);
        $this->assertEquals(0, $affectedRows,
            "Re-saving a comment digest should not modify any rows.");

        $cd->setBody("updated body");

        $affectedRows = $this->cdr->save($cd);
        $this->assertGreaterThanOrEqual(1, $affectedRows,
            "Changing the body of a comment digest should update an existing row.");
    }
}
