<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\MenuAction;
use Sarok\Actions\LogoutFormAction;
use Sarok\Actions\LeftMenuAction;
use Sarok\Actions\Action;
use Sarok\Models\User;

abstract class Page
{
    // Major sections in main (index.php) templates
    const TILE_MENU = 'menu';
    const TILE_LOGOUT = 'logout';
    const TILE_LEFT_MENU = 'leftMenu';
    const TILE_FRIEND_LIST = 'friendlist';
    const TILE_MAIN = 'main';
    const TILE_CALENDAR = 'calendar';
    const TILE_NAVIGATION = 'navigation';
    const TILE_SIDEBAR = 'sidebar';
    const TILE_HEADER = 'header';
    const TILE_NEW_MAIL = 'newMail';

    protected Logger $logger;
    protected Context $context;

    /** @var array<string, array<string>> */
    private array $actions = array();

    protected function __construct(Logger $logger, Context $context)
    {
        $this->logger = $logger;
        $this->context = $context;
    }

    protected function setTemplateName(string $templateName) : void
    {
        $this->context->setTemplateName($templateName);
    }

    protected function isLoggedIn() : bool
    {
        return $this->context->isLoggedIn();
    }

    protected function getUser() : User
    {
        return $this->context->getUser();
    }

    protected function getPathSegment(int $segment) : string
    {
        return $this->context->getPathSegment($segment);
    }

    protected function removeFirstSegment() : string
    {
        return $this->context->removeFirstSegment();
    }

    protected function getBlog() : User
    {
        return $this->context->getBlog();
    }

    protected function isPOST() : bool
    {
        return $this->context->isPOST();
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
        return $this->isLoggedIn();
    }
    
    public function init() : void
    {
        $this->logger->debug('Initializing Page (adding common actions)');

        // Subclasses should call this method first, then register more actions
        $this->addAction(self::TILE_MENU, MenuAction::class);
        $this->addAction(self::TILE_LOGOUT, LogoutFormAction::class);
        $this->addAction(self::TILE_LEFT_MENU, LeftMenuAction::class);
        
        if ($this->isLoggedIn()) {
            $this->addAction(self::TILE_FRIEND_LIST, FriendListAction::class);
            $this->addAction(self::TILE_NEW_MAIL, CheckMailAction::class);
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
