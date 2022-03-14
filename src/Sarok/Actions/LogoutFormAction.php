<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class LogoutFormAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function execute() : array
    {
        $this->log->debug("Running LogoutFormAction");

        $user = $this->getUser();
        $name = $user->getLogin();
        $loggedin = $this->isLoggedIn();

        return compact('name', 'loggedin');
    }
}
