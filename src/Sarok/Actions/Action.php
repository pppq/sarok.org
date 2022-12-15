<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Models\User;

abstract class Action
{
    protected const NO_DATA = array();

    protected Logger $logger;
    protected Context $context;

    public function __construct(Logger $logger, Context $context)
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

    protected function getBlog() : User
    {
        return $this->context->getBlog();
    }

    protected function getEntryID() : int
    {
        return $this->context->getEntryID();
    }
    
    protected function getPathSegment(int $segment) : string
    {
        return $this->context->getPathSegment($segment);
    }

    protected function isPOST() : bool
    {
        return $this->context->isPOST();
    }

    protected function getPOST(string $name, mixed $defaultValue = '') : mixed
    {
        return $this->context->getPOST($name, $defaultValue);
    }

    protected function getPathParams() : array
    {
        return $this->context->getPathParams();
    }

    protected function getCookie(string $name, string $defaultValue = '') : string
    {
        return $this->context->getCookie($name, $defaultValue);
    }

    protected function hasLeftMenuItems() : bool
    {
        return $this->context->hasLeftMenuItems();
    }

    protected function getLeftMenuItems() : array
    {
        return $this->context->getLeftMenuItems();
    }

    protected function getPath() : string
    {
        return $this->context->getPath();
    }

    ////////////////////
    // Action lifecycle
    ////////////////////

    abstract public function execute() : array;
}
