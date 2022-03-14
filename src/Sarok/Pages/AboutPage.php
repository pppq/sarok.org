<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\Page;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\UserMapAction;
use Sarok\Actions\UserListAction;
use Sarok\Actions\ShowArticleAction;
use Sarok\Actions\Action;

class AboutPage extends Page
{
    private const ENTRY_ABOUT_US = 15287;
    private const ENTRY_MEDIA_OFFER = 20505;

    private const ACTION_MAP = [
        ''             => self::ENTRY_ABOUT_US,
        'us'           => self::ENTRY_ABOUT_US,
        'mediaajanlat' => self::ENTRY_MEDIA_OFFER,
        'map'          => UserMapAction::class,
        'pacients'     => UserListAction::class,
    ];

    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function canExecute() : bool
    {
        return true;
    }

    public function init() : void
    {
        $this->logger->debug('Initializing AboutPage');
        parent::init();

        $firstSegment = $this->removeFirstSegment();

        if (!isset(self::ACTION_MAP[$firstSegment])) {
            $this->logger->warning('Path segment not found in map, using default entry');
            $action = self::ENTRY_ABOUT_US;
        } else {
            $action = self::ACTION_MAP[$firstSegment];
        }
        
        if (is_int($action)) {
            $this->logger->debug("Displaying entry with ID '$action'");
            $this->context->setEntryID($action);
            $action = ShowArticleAction::class;
        }
        
        $this->logger->debug("Action set to '$action'");
        $this->addAction(self::TILE_MAIN, $action);
    }
}
