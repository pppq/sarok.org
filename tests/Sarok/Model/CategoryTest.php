<?php declare(strict_types=1); 

namespace Sarok\Model;

use Sarok\Models\Category;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase
{
    public function testToArray() : void
    {
        $ca = Category::create(54832, 'cats');

        $this->assertEquals(array(
            Category::FIELD_ENTRY_ID => 54832,
            Category::FIELD_NAME => 'cats',
        ), $ca->toArray(), "Array contents should match previously set values.");
    }
}
