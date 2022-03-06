<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\MenuAction;
use Sarok\Actions\LogoutFormAction;
use Sarok\Actions\LeftMenuAction;
use Sarok\Actions\Action;

abstract class Page
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

    protected function isLoggedIn() : bool
    {
        return $this->context->getProperty(Context::PROP_IS_LOGGED_IN);
    }

    public function canExecute() : bool
    {
        /* 
         * The default implementation permits access to logged in users only. Subclasses should override if 
         * they have another way to determine if the page should be displayed to the user (eg. public pages).
         */
        return $this->isLoggedIn();
    }
    
    public function init() : void
    {
        $this->logger->debug('Initializing Page (adding common actions)');

        // Subclasses should call this method first, then register more actions
        $this->addAction('menu', MenuAction::class);
        $this->addAction('logout', LogoutFormAction::class);
        $this->addAction('leftMenu', LeftMenuAction::class);
        
        if ($this->isLoggedIn()) {
            $this->addAction('friendList', FriendListAction::class);
            $this->addAction('newMail', CheckMailAction::class);
        }
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
