<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\SettingsImagesAction;

final class ImageBrowserPage extends Page
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        // parent::init() is not needed
        
        // Use the image browser action from the settings page here
        $this->log->debug('Initializing ImageBrowserPage');
        $this->addAction(self::TILE_MAIN, SettingsImagesAction::class);
        $this->setTemplateName('empty');
    }
}
