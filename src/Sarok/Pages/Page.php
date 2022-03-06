<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

abstract class ActionPage
{
    protected Logger $logger;
    protected Context $context;

    private array $actions = array();

    protected function __construct(Logger $logger, Context $context)
    {
        $this->logger = $logger;
        $this->context = $context;
    }

    public function getTemplateName() : string
    {
        return $this->context->getTemplateName();
    }

    public function setTemplateName(string $templateName) : void
    {
        $this->context->setTemplateName($templateName);
    }

    public function addAction(string $tile, string $action) : void
    {
        if (!isset($this->actions[$tile])) {
            $this->actions[$tile] = array($action);
        } else {
            $this->actions[$tile][] = $action;
        }
    }

    public function getActions() : array
    {
        return $this->actions;
    }

    public function canExecute() : bool
    {
        /* 
         * The default implementation permits access to logged in users only. Subclasses should override if 
         * they have another way to determine if the page should be displayed to the user (eg. public pages).
         */
        return $this->context->getProperty(Context::PROP_IS_LOGGED_IN);
    }
    
    public function init() : void
    {
        // Subclasses should override to register more actions
    }

    public function execute() : array
    {
        $data = array();
        
        foreach ($this->actions as $tile => $tileActions) {
            foreach ($tileActions as $action) {
                // Process actions once per action name
                if (!isset($data[$action])) {
                    $data[$action] = $this->context->getAction($action)->execute();
                }
            }
        }

        return $data;
    }
}
