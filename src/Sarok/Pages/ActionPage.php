<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

abstract class ActionPage
{
    protected Logger $logger;
    protected Context $context;

    private string $templateName = "default";
    private array $actions = array();

    protected function __construct(Logger $logger, Context $context)
    {
        $this->logger = $logger;
        $this->context = $context;
    }

    public function getTemplateName() : string
    {
        return $this->templateName;
    }

    public function setTemplateName(string $templateName) : void
    {
        $this->templateName = $templateName;
    }

    public function addAction(string $tile, string $action) : void
    {
        if (!isset($this->actions[$tile])) {
            $this->actions[$tile] = array();
        }

        $this->actions[$tile][] = $action;
    }

    public function getActions() : array
    {
        return $this->actions;
    }

    public function canExecute() : bool
    {
        // Subclasses should override to determine if the page can be displayed to the user
        return false;
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
