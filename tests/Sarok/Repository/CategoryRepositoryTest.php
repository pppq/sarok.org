<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Repository\RepositoryTest;
use Sarok\Repository\EntryRepository;
use Sarok\Repository\CategoryRepository;
use Sarok\Models\Friend;
use Sarok\Models\Entry;
use Sarok\Models\Category;
use Sarok\Models\AccessType;

final class CategoryRepositoryTest extends RepositoryTest
{
    private CategoryRepository $cr;

    public function setUp() : void
    {
        $this->clearTable(CategoryRepository::TABLE_NAME);
        $this->cr = self::get(CategoryRepository::class);
    }

    public function testNamesByPrefix() : void
    {
        $c1 = Category::create(1000, "cats");
        $c2 = Category::create(1003, "catfish");
        $c3 = Category::create(1000, "bobcat");

        $this->cr->save($c1);
        $this->cr->save($c2);
        $this->cr->save($c3);

        // 'f' < 's', so "catfish" is returned before "cats"
        $expected = array($c2->getName(), $c1->getName());
        $actual = $this->cr->getNamesByPrefix("cat");
        $this->assertEquals($expected, $actual,
            "All categories matching the prefix should be returned in ascending order.");

        $expected = array($c2->getName());
        $actual = $this->cr->getNamesByPrefix("cat", 1);
        $this->assertEquals($expected, $actual,
            "Suggestion limit should be honored.");

        $actual = $this->cr->getNamesByPrefix("ham");
        $this->assertEmpty($actual,
            "Prefix should return an empty array of matching categories.");
    }

    public function testNamesByEntryId() : void
    {
        $c1 = Category::create(2000, "fish");
        $c2 = Category::create(2003, "salmon");
        $c3 = Category::create(2000, "shark");

        $this->cr->save($c1);
        $this->cr->save($c2);
        $this->cr->save($c3);

        // 'f' < 's', so "fish" is returned before "shark"
        $expected = array($c1->getName(), $c3->getName());
        $actual = $this->cr->getNamesByEntryID(2000);
        $this->assertEquals($expected, $actual,
            "All categories for the entry should be returned in ascending order.");

        $expected = array($c1->getName());
        $actual = $this->cr->getNamesByEntryID(2000, 1);
        $this->assertEquals($expected, $actual,
            "Suggestion limit should be honored.");

        $actual = $this->cr->getNamesByEntryID(999);
        $this->assertEmpty($actual,
            "Non-existent entry ID should return an empty array of categories.");
    }

    public function testCategoriesByEntryIds() : void
    {
        $c1 = Category::create(2000, "shark");
        $c2 = Category::create(2000, "raptor");
        $c3 = Category::create(2003, "salmon");

        $this->cr->save($c1);
        $this->cr->save($c2);
        $this->cr->save($c3);

        $expected = array(
            2000 => array("raptor", "shark"),
            2001 => array(),
            2003 => array("salmon"),
        );
        
        $actual = $this->cr->getCategoriesByEntryIDs(array(2000, 2001, 2003));
        foreach ($actual as $key => &$value) {
            sort($value);
        }

        $this->assertEquals($expected, $actual,
            "All categories for the requested entries should be returned.");
    }

    public function testTagCloud() : void
    {
        $this->clearTable(EntryRepository::TABLE_NAME);
        $er = self::get(EntryRepository::class);

        $e1 = new Entry();
        $e1->setID(2000);
        $e1->setDiaryID(5000);
        $e1->setAccess(AccessType::ALL);

        $e2 = new Entry();
        $e2->setID(2003);
        $e2->setDiaryID(5000);
        $e2->setAccess(AccessType::AUTHOR_ONLY);

        $e3 = new Entry();
        $e3->setID(2006);
        $e3->setDiaryID(5001);
        $e3->setAccess(AccessType::REGISTERED);

        $er->save($e1);
        $er->save($e2);
        $er->save($e3);

        $c1 = Category::create(2000, "fish");
        $c2 = Category::create(2003, "salmon");
        $c3 = Category::create(2000, "shark");
        $c4 = Category::create(2006, "fish");

        $this->cr->save($c1);
        $this->cr->save($c2);
        $this->cr->save($c3);
        $this->cr->save($c4);

        $expected = array("shark" => 1, "fish" => 2);
        $actual = $this->cr->getTagCloud();
        $this->assertEquals($expected, $actual,
            "All tags for global tag cloud should be returned.");

        // Second "fish" is in a different diary, so tag counts are different
        $expected = array("shark" => 1, "fish" => 1);
        $actual = $this->cr->getTagCloud(5000);
        $this->assertEquals($expected, $actual,
            "All tags for diary tag cloud should be returned.");
    }
    
    public function testSave() : void
    {
        $c = Category::create(12345, "cats");
        
        $affectedRows = $this->cr->save($c);
        $this->assertEquals(1, $affectedRows,
            "Saving a category should insert a row.");

        $affectedRows = $this->cr->save($c);
        $this->assertEquals(0, $affectedRows,
            "Re-saving a category should not modify any rows.");
    }
}
