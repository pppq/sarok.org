<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\SessionService;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class LogoutAction extends Action
{
    private SessionService $sessionService;

    public function __construct(Logger $logger, Context $context, SessionService $sessionService)
    {
        parent::__construct($logger, $context);
        $this->sessionService = $sessionService;
    }

    public function execute() : array
    {
        $this->log->debug('Running LogoutAction');

        $this->sessionService->logout();
        $location = '/';
        return compact('location');
    }
}
