<?php declare(strict_types=1);

namespace Sarok;

use Sarok\DIContainer;
use PHPUnit\Framework\TestCase;

final class ViewHandlerTest extends TestCase
{
    private static DIContainer $container;
    private ViewHandler $vh;

    public static function setUpBeforeClass() : void
    {
        self::$container = new DIContainer();
        self::$container->put('logPath', './logs/log.txt');
        self::$container->put('logLevel', 5);
    }

    public function setUp() : void
    {
        // Create a new instance each time as ViewHandler is stateful
        $this->vh = self::$container->get(ViewHandler::class, true);
    }

    public function testRender() : void
    {
        $this->vh->setTemplatePath(dirname(__FILE__) . '/../templates');

        // Actions are rendered using a dedicated template and can appear in multiple tiles...
        $this->vh->addAction('main', 'testAction1');
        $this->vh->putAllActionData(array(
            'testAction1' => array(
                'variable' => 'Hello world!'
            )
        ));

        // ...but tile content or other variables can be populated directly if required
        $this->vh->putTileData('sidebar', 'Sidebar goes here');

        $this->assertEquals('Layout pre Action pre Hello world! action post Sidebar goes here layout post', 
            $this->vh->render(),
            'View handler should substitute input variable in both the action and the main template.');
    }
}
