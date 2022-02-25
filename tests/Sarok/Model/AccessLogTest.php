<?php declare(strict_types=1); 

namespace Sarok\Model;

use Sarok\Util;
use Sarok\Models\AccessLog;
use PHPUnit\Framework\TestCase;

final class AccessLogTest extends TestCase
{
    public function testDatumConstructor() : void
    {
        $now = Util::utcDateTimeFromString();
        $al = new AccessLog($now);

        $this->assertEquals($now->getTimestamp(), $al->getDatum()->getTimestamp(),
            "Timestamp should match value given in constructor.");
    }

    public function testDatumMagicSetter() : void
    {
        $now = Util::utcDateTimeFromString();
        $al = new AccessLog();
        $al->_datum = Util::dateTimeToString($now);

        $this->assertEquals($now->getTimestamp(), $al->getDatum()->getTimestamp(),
            "Timestamp should match value set via magic method.");
    }
}
