<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Service\DB;
use Sarok\Repository\UserRepository;
use Sarok\Repository\RepositoryTest;
use Sarok\Repository\FriendRepository;
use Sarok\Models\User;
use Sarok\Models\FriendType;
use Sarok\Models\Friend;

final class FriendRepositoryTest extends RepositoryTest
{
    private FriendRepository $fr;

    public function setUp() : void
    {
        $this->clearTable(FriendRepository::TABLE_NAME);
        $this->fr = self::get(FriendRepository::class);
    }

    public function testGetSourceUserIds() : void
    {
        $f1 = Friend::create(100, 200);
        $f2 = Friend::create(150, 200);
        $f3 = Friend::create(150, 210); // different destination
        $f4 = Friend::create(110, 200, FriendType::BANNED); // different type
        
        $this->fr->save($f1);
        $this->fr->save($f2);
        $this->fr->save($f3);
        $this->fr->save($f4);
        
        $sourceIds = $this->fr->getSourceUserIds(200, FriendType::FRIEND);
        $this->assertEqualsCanonicalizing([ $f1->getFriendOf(), $f2->getFriendOf() ], $sourceIds,
            "All users choosing user '200' as a friend should appear in the result set.");

        $sourceIds = $this->fr->getSourceUserIds(200, FriendType::BANNED);
        $this->assertEqualsCanonicalizing([ $f4->getFriendOf() ], $sourceIds,
            "All users banning user '200' should appear in the result set.");
    }

    public function testGetDestinationUserIds() : void
    {
        $f1 = Friend::create(100, 200);
        $f2 = Friend::create(100, 220);
        $f3 = Friend::create(150, 210); // different source
        $f4 = Friend::create(100, 230, FriendType::READER); // different type
        
        $this->fr->save($f1);
        $this->fr->save($f2);
        $this->fr->save($f3);
        $this->fr->save($f4);
        
        $destinationIds = $this->fr->getDestinationUserIds(100, FriendType::FRIEND);
        $this->assertEqualsCanonicalizing([ $f1->getUserID(), $f2->getUserID() ], $destinationIds,
            "All friends of user '100' should appear in the result set.");

        $destinationIds = $this->fr->getDestinationUserIds(100, FriendType::READER);
        $this->assertEqualsCanonicalizing([ $f4->getUserID() ], $destinationIds,
            "All users followed by user '100' should appear in the result set.");
    }

    public function testAssociationExistsQuery() : void
    {
        $f = Friend::create(100, 230, FriendType::READER);
        $this->fr->save($f);
        
        $db = parent::get(DB::class);
        $q = $this->fr->getAssociationExistsQuery();
        $friendType = FriendType::READER;
        
        $exists = (bool) $db->queryInt($q, 0, 'iis', 100, 230, $friendType->value);
        $this->assertTrue($exists, "An association of type 'reader' between users '100' and '230' should exist.");

        $friendType = FriendType::FRIEND;
        $exists = (bool) $db->queryInt($q, 0, 'iis', 199, 299, $friendType->value);
        $this->assertFalse($exists, "An association of type 'friend' between users '199' and '299' should not exist.");
    }

    public function testDestinationLoginsQuery() : void
    {
        $f1 = Friend::create(100, 230);
        $f2 = Friend::create(100, 299);
        
        $this->fr->save($f1);
        $this->fr->save($f2);
        
        // Create a user for ID 230, but not for 299
        $u = new User();
        $u->setID(230);
        $u->setLogin('friend');
        
        $this->clearTable(UserRepository::USER_TABLE_NAME);
        $ur = parent::get(UserRepository::class);
        $ur->save($u);
        
        $db = parent::get(DB::class);
        $q = $this->fr->getDestinationLoginsQuery();
        $friendType = FriendType::FRIEND;

        $logins = $db->queryArray($q, 'is', 100, $friendType->value);
        $this->assertEqualsCanonicalizing([ $u->getLogin(), $f2->getUserID() ], $logins,
            "Login names of friends of user '100' should appear in the result set (if a corresponding user exists).");
    }

    public function testDeleteByDestinationUserId() : void
    {
        $f1 = Friend::create(100, 200);
        $f2 = Friend::create(150, 200);
        $f3 = Friend::create(150, 210); // different destination
        $f4 = Friend::create(110, 200, FriendType::BANNED); // different type
        
        $this->fr->save($f1);
        $this->fr->save($f2);
        $this->fr->save($f3);
        $this->fr->save($f4);

        $affectedRows = $this->fr->deleteByDestinationUserId(200, FriendType::FRIEND);
        $this->assertEquals(2, $affectedRows, "Deleting associations should affect two rows.");
        
        $sourceIds = $this->fr->getSourceUserIds(200, FriendType::FRIEND);
        $this->assertEmpty($sourceIds, "All users choosing user '200' as a friend should be removed.");

        $sourceIds = $this->fr->getSourceUserIds(200, FriendType::BANNED);
        $this->assertEqualsCanonicalizing([ $f4->getFriendOf() ], $sourceIds,
            "All users banning user '200' should still appear in the result set.");
    }

    public function testDeleteBySourceUserId() : void
    {
        $f1 = Friend::create(100, 200);
        $f2 = Friend::create(100, 220);
        $f3 = Friend::create(150, 210); // different source
        $f4 = Friend::create(100, 230, FriendType::READER); // different type
        
        $this->fr->save($f1);
        $this->fr->save($f2);
        $this->fr->save($f3);
        $this->fr->save($f4);
        
        $affectedRows = $this->fr->deleteBySourceUserId(100, FriendType::FRIEND);
        $this->assertEquals(2, $affectedRows, "Deleting associations should affect two rows.");

        $destinationIds = $this->fr->getDestinationUserIds(100, FriendType::FRIEND);
        $this->assertEmpty($destinationIds, "All friends of user '100' should be removed.");

        $destinationIds = $this->fr->getDestinationUserIds(100, FriendType::READER);
        $this->assertEqualsCanonicalizing([ $f4->getUserID() ], $destinationIds,
            "All users followed by user '100' should still appear in the result set.");
    }

    public function testSave() : void
    {
        $f = Friend::create(100, 200);

        $affectedRows = $this->fr->save($f);
        $this->assertEquals(1, $affectedRows, "Saving an association should insert a row.");
        $affectedRows = $this->fr->save($f);
        $this->assertEquals(0, $affectedRows, "Re-saving an existing association should not modify any rows.");
    }
}
