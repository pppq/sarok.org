<?php declare(strict_types=1); 

namespace Sarok;

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UtilTest extends TestCase
{
    public function testUtcDateTimeFromString() : void
    {
        $now = Util::utcDateTimeFromString();
        $this->assertInstanceOf(DateTime::class, $now, 
            "Utility method should create an instance of DateTime.");

        $dt = Util::utcDateTimeFromString('2022-12-12 22:45:03');
        // https://www.epochconverter.com agrees
        $this->assertEquals(1670885103, $dt->getTimestamp(),
            "Timestamp should match the provided value in UTC timezone.");
    }

    public function testInvalidUtcDateTimeString() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Input string must be a valid date, got 'hetfo'.");
        Util::utcDateTimeFromString('hetfo');
    }

    public function testDateTimeToString() : void
    {
        $value = '2022-12-12 22:45:03';
        $dt = Util::utcDateTimeFromString($value);
        $this->assertEquals($value, Util::dateTimeToString($dt),
            "Value should match date and time found in the original input.");
    }

    public function testDateToString() : void
    {
        $value = '2022-05-18';
        $dt = Util::utcDateTimeFromString($value);
        $this->assertEquals($value, Util::dateToString($dt),
            "Value should match date found in the original input.");
    }

    public function testYesNoToBool() : void
    {
        $this->assertTrue(Util::yesNoToBool('Y'), "Value should be true given 'Y' as the string input.");
        $this->assertFalse(Util::yesNoToBool('N'), "Value should be false given 'N' as the string input.");

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Input string must be 'Y' or 'N', got 'Maybe?'.");
        Util::yesNoToBool('Maybe?');
    }

    public function testBoolToYesNo() : void
    {
        $this->assertEquals('Y', Util::boolToYesNo(true), "Value should be 'Y' given true as the boolean input.");
        $this->assertEquals('N', Util::boolToYesNo(false), "Value should be 'N' given false as the boolean input.");
    }
}
