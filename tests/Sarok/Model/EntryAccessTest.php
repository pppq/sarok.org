<?php declare(strict_types=1); 

namespace Sarok\Model;

use Sarok\Models\EntryAccess;
use PHPUnit\Framework\TestCase;

final class EntryAccessTest extends TestCase
{
    public function testToArray() : void
    {
        $ea = EntryAccess::create(54832, 32440);

        $this->assertEquals(array(
            EntryAccess::FIELD_ENTRY_ID => 54832,
            EntryAccess::FIELD_USER_ID => 32440,
        ), $ea->toArray(), "Array contents should match previously set values.");
    }
}
