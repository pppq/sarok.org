<?php declare(strict_types=1); 

namespace Sarok\Model;

use Sarok\Models\Friend;
use PHPUnit\Framework\TestCase;
use Sarok\Models\FriendType;

final class FriendTest extends TestCase
{
    public function testToArray() : void
    {
        $f = Friend::create(54832, 32440, FriendType::BANNED);

        $this->assertEquals(array(
            Friend::FIELD_FRIEND_OF => 54832,
            Friend::FIELD_USER_ID => 32440,
            Friend::FIELD_FRIEND_TYPE => 'banned',
        ), $f->toArray(), "Array contents should match previously set values.");
    }
}
