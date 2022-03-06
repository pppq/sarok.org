<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class LeftMenuAction extends Action
{
    // Links to be displayed when the page does not set any
    private const DEFAULT_MENU = array(
        array('name' => 'Bemutató', 'url' => '/about/' ),
        array('name' => 'Páciensek listája', 'url' => '/about/pacients/' ),
        array('name' => 'Felhasználói térkép', 'url' => '/about/map/' ),
    );

    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }
    
    public function execute() : array
    {
        $this->log->debug("Running LeftMenuAction");
        $menu = $this->context->getProperty(Context::PROP_MENU_ITEMS, self::DEFAULT_MENU);
        return compact('menu');
    }
}
