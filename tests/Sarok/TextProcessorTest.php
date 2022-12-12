<?php declare(strict_types=1);

namespace Sarok;

use Sarok\DIContainer;
use PHPUnit\Framework\TestCase;

final class TextProcessorTest extends TestCase
{
    private static TextProcessor $tp;

    public static function setUpBeforeClass() : void
    {
        $container = new DIContainer();
        
        $container->put('logPath', './logs/log.txt');
        $container->put('logLevel', 5);

        self::$tp = $container->get(TextProcessor::class);
    }

    /**
     * @dataProvider preFormatProvider
     */
    public function testPreFormat(string $in, string $out) : void
    {
        $this->assertEquals($out, self::$tp->preFormat($in),
            "Preformatter should produce the expected output.");
    }

    public function preFormatProvider() : array
    {
        return array(
            array('aaa', 'aaa'),
            array('aaa<h1>allowed tag</h1>', 'aaa<h1>allowed tag</h1>'),
            array('aaa<script>forbidden tag</script>', 'aaaforbidden tag'),
            array('bbb!!!!!!!!!!!!!!!', 'bbbIdióta vagyok! Valaki lőjön le engem!'),
            array('really???????????????', 'reallyIdióta vagyok? Valaki lőjön le engem!'),
            array('blabla lol asd', 'blabla' . TextProcessor::WRONG_WINDOW . 'asd'),
            array("a few exclamation marks!!!\r\n!!", 'a few exclamation marks!!!'),
            array("a few question marks???\r\n??", 'a few question marks???'),
            array("a few interrobangs?!?!?!\r\n?!?!", 'a few interrobangs?!?!'),
            array("(c) 2022 Serious Business Ltd. (r)(tm)", "&copy; 2022 Serious Business Ltd. &reg;&trade;"),
        );
    }
    
    /**
     * @dataProvider postFormatProvider
     */
    public function testPostFormat(string $in, string $out) : void
    {
        $this->assertEquals($out, self::$tp->postFormat($in, 'needle'),
            "Post-formatter should produce the expected output.");
    }

    public function postFormatProvider() : array
    {
        return array(
            array('zzz', 'zzz'),
            array('Budapest -- Miskolc', 'Budapest &ndash; Miskolc'),
            array('Budapest,-- Miskolc', 'Budapest,&ndash; Miskolc'),
            array('Budapest --,Miskolc', 'Budapest &ndash;,Miskolc'),
            array('uid_dimi', '<a href="/users/dimi/" class="personid">dimi</a>'),
            array(
                'Are you looking for an <needle>element</needle> or the needle?', 
                'Are you looking for an <needle>element</needle> or the <span class="search">needle</span>?'
            ),
        );
    }

    /**
     * @dataProvider tidyProvider
     */
    public function testTidy(string $in, string $out) : void
    {
        $this->assertEquals($out, self::$tp->tidy($in),
            "HTML Tidy should produce the expected output.");
    }

    public function tidyProvider() : array
    {
        return array(
            array('eee', 'eee'),
            array('<h1>incomplete tag', "<h1>incomplete tag</h1>\n"),
            // TODO: add more representative examples!
        );
    }
}
