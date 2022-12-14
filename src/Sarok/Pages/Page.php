<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\MenuAction;
use Sarok\Actions\LogoutFormAction;
use Sarok\Actions\LeftMenuAction;
use Sarok\Models\MenuItem;
use Sarok\Models\User;

abstract class Page
{
    // Major sections in main templates (index.php)
    const TILE_MENU        = 'menu';
    const TILE_LOGOUT      = 'logout';
    const TILE_LEFT_MENU   = 'leftMenu';
    const TILE_FRIEND_LIST = 'friendlist';
    const TILE_MAIN        = 'main';
    const TILE_CALENDAR    = 'calendar';
    const TILE_NAVIGATION  = 'navigation';
    const TILE_SIDEBAR     = 'sidebar';
    const TILE_HEADER      = 'header';
    const TILE_NEW_MAIL    = 'newMail';

    protected Logger $logger;
    protected Context $context;

    /** 
     * The list of actions to run, keyed by tile name
     * @var array<string, array<string>> 
     */
    private array $actions = array();

    protected function __construct(Logger $logger, Context $context)
    {
        $this->logger = $logger;
        $this->context = $context;
    }

    ///////////////////////////////
    // Request context delegates
    ///////////////////////////////

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

    protected function popFirstSegment() : string
    {
        return $this->context->popFirstSegment();
    }

    protected function setPathParams(array $pathParams) : void
    {
        $this->context->setPathParams($pathParams);
    }

    protected function getBlog() : User
    {
        return $this->context->getBlog();
    }

    protected function setEntryID(int $entryID) : void
    {
        $this->context->setEntryID($entryID);
    }

    protected function setLeftMenuItems(MenuItem ...$leftMenuItems) : void
    {
        $this->context->setLeftMenuItems(...$leftMenuItems);
    }

    protected function isPOST() : bool
    {
        return $this->context->isPOST();
    }

    protected function getPOST(string $name, mixed $defaultValue = '') : mixed
    {
        return $this->context->getPOST($name, $defaultValue);
    }

    ////////////////////
    // Page lifecycle
    ////////////////////

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
        $this->logger->debug('Initializing Page');

        // Subclasses should call this method first then register more actions if required
        $this->addAction(self::TILE_MENU, MenuAction::class);
        $this->addAction(self::TILE_LOGOUT, LogoutFormAction::class);
        $this->addAction(self::TILE_LEFT_MENU, LeftMenuAction::class);
        
        if ($this->isLoggedIn()) {
            $this->addAction(self::TILE_FRIEND_LIST, FriendListAction::class);
            $this->addAction(self::TILE_NEW_MAIL, CheckMailAction::class);
        }
    }

    //////////////////
    // Page actions
    //////////////////

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

    public function execute() : array
    {
        // Process actions once per action name
        $allActions = array_values($this->actions);
        $flattenedActions = array_merge([], ...$allActions);
        $uniqueActions = array_unique($flattenedActions);

        $data = array();
        
        foreach ($uniqueActions as $action) {
            $data[$action] = $this->context->createAction($action)->execute();
        }

        return $data;
    }
}
