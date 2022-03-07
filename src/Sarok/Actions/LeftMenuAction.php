<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Models\MenuItem;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class LeftMenuAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }
    
    public function execute() : array
    {
        $this->log->debug("Running LeftMenuAction");

        if ($this->context->hasLeftMenuItems()) {
            // If the page registered some links for display, use those...
            $menu = $this->context->getLeftMenuItems();
        } else {
            // ...display default links otherwise
            $menu = [
                new MenuItem('Bemutató',            '/about/'),
                new MenuItem('Páciensek listája',   '/about/pacients/'),
                new MenuItem('Felhasználói térkép', '/about/map/'),
            ];
        }
        
        return compact('menu');
    }
}
