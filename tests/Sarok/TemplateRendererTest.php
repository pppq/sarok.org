<?php declare(strict_types=1);

namespace Sarok;

use Sarok\DIContainer;
use PHPUnit\Framework\TestCase;

final class TemplateRendererTest extends TestCase
{
    private static TemplateRenderer $tr;

    public static function setUpBeforeClass() : void
    {
        $container = new DIContainer();
        $container->put('logPath', './logs/log.txt');
        $container->put('logLevel', 5);

        self::$tr = $container->get(TemplateRenderer::class);
    }

    public function testInvalidPath() : void
    {
        $variables = array('main' => 'Hello world!');
        $this->assertEmpty(self::$tr->render('no-such-template.php', $variables),
            'Output should be empty if a template can not be read.');
    }

    public function testRender() : void
    {
        $variables = array('main' => 'Hello world!');
        $this->assertEquals('Pre Hello world! post', 
            self::$tr->render(dirname(__FILE__) . '/example-template.php', $variables),
            'Renderer should substitute input variable.');
    }

    public function testLocalVariable() : void
    {
        // This variable should have no effect on the template
        $main = 'Hello world!';

        $this->assertEquals('Pre ', 
            self::$tr->render(dirname(__FILE__) . '/example-template.php', array()),
            'Renderer should produce best-effort results.');
    }    

    public function testGlobalVariable() : void
    {
        try {

            // This global should have no effect on the template either
            global $main;
            $main = 'Hello world!';

            $this->assertEquals('Pre ', 
                self::$tr->render(dirname(__FILE__) . '/example-template.php', array()),
                'Renderer should produce best-effort results.');

        } finally {

            // Clean up the global
            unset($main);
        }
    }    
}
