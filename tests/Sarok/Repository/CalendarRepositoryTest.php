<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Repository\RepositoryTest;
use Sarok\Repository\FriendRepository;
use Sarok\Repository\CalendarRepository;
use Sarok\Models\User;
use Sarok\Models\Friend;
use Sarok\Models\Calendar;

final class CalendarRepositoryTest extends RepositoryTest
{
    private CalendarRepository $cr;

    public function setUp() : void
    {
        $this->clearTable(CalendarRepository::TABLE_NAME);
        $this->cr = self::get(CalendarRepository::class);
    }

    public function testGetBlogMonthsBefore() : void
    {
        $c1 = new Calendar();
        $c1->setUserID(1000);
        $c1->setY(2022);
        $c1->setM(2);
        $c1->setD(10);
        $c1->setNumPublic(99);

        $c2 = new Calendar();
        $c2->setUserID(1000);
        $c2->setY(2022);
        $c2->setM(3);
        $c2->setD(11);
        $c2->setNumRegistered(5);

        $this->cr->save($c1);
        $this->cr->save($c2);

        $expectedEntries = array('2022/3', '2022/2');
        $actualEntries = $this->cr->getBlogMonthsBefore(1000, 2022, false);
        $this->assertEquals($expectedEntries, $actualEntries, 
            "Blog activity months should be returned in descending order.");

        $expectedEntries = array('2022/2');
        $actualEntries = $this->cr->getBlogMonthsBefore(1000, 2022, true);
        $this->assertEquals($expectedEntries, $actualEntries, 
            "Only public activity should be considered when the flag is set to true.");
    }

    public function testGetCalendarEntries() : void
    {
        $c1 = new Calendar();
        $c1->setUserID(1000);
        $c1->setY(2022);
        $c1->setM(2);
        $c1->setD(10);
        $c1->setNumPublic(99);

        $c2 = new Calendar();
        $c2->setUserID(1000);
        $c2->setY(2022);
        $c2->setM(2);
        $c2->setD(11);
        $c2->setNumPublic(30);

        $this->cr->save($c1);
        $this->cr->save($c2);

        $expectedEntries = array($c2, $c1);
        $actualEntries = $this->cr->getCalendarEntries(1000, 2022, 2);
        $this->assertEquals($expectedEntries, $actualEntries, 
            "Both calendar entries should be returned in decreasing date order.");
    }

    public function testGetCalendarEntriesOfFriends() : void
    {
        $c1 = new Calendar();
        $c1->setUserID(1000);
        $c1->setY(2022);
        $c1->setM(2);
        $c1->setD(10);
        $c1->setNumAll(80);

        $c2 = new Calendar();
        $c2->setUserID(1001);
        $c2->setY(2022);
        $c2->setM(2);
        $c2->setD(10);
        $c2->setNumAll(20);

        $c3 = new Calendar();
        $c3->setUserID(1001);
        $c3->setY(2022);
        $c3->setM(2);
        $c3->setD(15);
        $c3->setNumAll(30);

        $this->cr->save($c1);
        $this->cr->save($c2);
        $this->cr->save($c3);

        $this->clearTable(FriendRepository::TABLE_NAME);
        $fr = self::get(FriendRepository::class);
        $fr->save(Friend::create(900, 1000));
        $fr->save(Friend::create(900, 1001));

        $ec1 = new Calendar();
        $ec1->setY(2022);
        $ec1->setM(2);
        $ec1->setD(15);
        $ec1->setNumAll(30); // matches user 1001's 'numAll' count for the 15th, userID is zeroed out

        $ec2 = new Calendar();
        $ec2->setY(2022);
        $ec2->setM(2);
        $ec2->setD(10);
        $ec2->setNumAll(100); // sum of 'numAll' for users 1000 and 1001, userID is zeroed out

        $expectedEntries = array($ec1, $ec2);
        $actualEntries = $this->cr->getCalendarEntriesOfFriends(900, 2022, 2);
        $this->assertEquals($expectedEntries, $actualEntries, 
            "Calendar entries should be aggregated and returned in decreasing date order.");
    }
    
    public function testSave() : void
    {
        $c = new Calendar();
        $c->setUserID(1000);
        $c->setY(2022);
        $c->setM(2);
        $c->setD(10);

        $affectedRows = $this->cr->save($c);
        $this->assertEquals(1, $affectedRows, "Saving a calendar entry should insert a row.");

        $affectedRows = $this->cr->save($c);
        $this->assertEquals(0, $affectedRows, "Re-saving an existing calendar entry should not modify any rows.");

        $c->setNumPublic(99);
        $affectedRows = $this->cr->save($c);
        // MySQL's ON DUPLICATE KEY UPDATE returns 2 when the row is updated, so don't rely on the actual number.
        $this->assertGreaterThanOrEqual(1, $affectedRows, "Re-saving a modified calendar entry should update a row.");
    }
}
