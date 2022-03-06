<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\SessionService;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class UserListAction extends Action
{
    private SessionService $sessionService;

    public function __construct(Logger $logger, Context $context, SessionService $sessionService)
    {
        parent::__construct($logger, $context);
        $this->sessionService = $sessionService;
    }

    public function execute() : array
    {
        $this->log->debug('Running UserListAction');

        $secondSegment = $this->context->getPathSegment(1);
        $thirdSegment = $this->context->getPathSegment(2);
        
        if ($secondSegment === 'skip' && preg_match('/^0|[1-9][0-9]*$/', $thirdSegment)) {
            $offset = (int) $thirdSegment;
        } else {
            $offset = 0;
        }
        
        $users = $this->sessionService->getUserList($offset, 300);
        $stat = $this->sessionService->getUserStats();
        
        return compact('users', 'stat');
    }
}
