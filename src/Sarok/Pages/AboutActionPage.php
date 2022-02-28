<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\ActionPage;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\UserListAction;
use Sarok\Actions\ShowArticleAction;
use Sarok\Actions\GeneralMapAction;
use Sarok\Actions\Action;

class AboutActionPage extends ActionPage
{
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
        $this->logger->debug("Initializing map");

        $actionMap = array(
            ""             => 15287,
            "us"           => 15287,
            "mediaajanlat" => 20505,
            "map"          => GeneralMapAction::class,
            "pacients"     => UserListAction::class,
        );

        $params = $this->context->params;
        $path = implode("/", $params);
        $this->logger->debug("path is $path");

        if (!isset($this->actionMap[$pathline])) {
            $this->logger->warning("item not found in map");
            $key = 15287;
        } else {
            $key = $this->actionMap[$pathline];
        }
        
        $this->logger->debug("key set to ".$this->key);
        
        if (is_numeric($key)) {
            $this->logger->warning("found numeric key for the item");
            $this->addAction("main", ShowArticleAction::class);
            $this->context->setProperty(Context::PROP_ENTRY_ID, $key);
        } else {
            $this->addAction("main", $key);
        }
    }
}
