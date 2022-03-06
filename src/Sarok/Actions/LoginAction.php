<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\SessionService;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class LoginAction extends Action
{
    private SessionService $sessionService;

    public function __construct(Logger $logger, Context $context, SessionService $sessionService)
    {
        parent::__construct($logger, $context);
        $this->sessionService = $sessionService;
    }

    public function execute() : array
    {
        $this->log->debug('Running LoginAction');

        if (!$this->context->isPostRequest()) {
            $location = '/';
            return compact('location');
        }

        $loginName = $this->context->getPost('login');
        $password = $this->context->getPost('pass');
        $location = $this->context->getPost('from', '/');

        $success = $this->sessionService->loginUser($loginName, $password);

        if ($success) {
            return compact('location');
        } else {
            // In case of error, a template will be rendered on the login page instead of a redirect
            $this->context->setTemplateName('splash');
            return Action::NO_DATA;
        }
    }
}
