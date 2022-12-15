<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\UserService;

final class UserMapAction extends Action
{
    private UserService $userService;

    public function __construct(Logger $logger, Context $context, UserService $userService)
    {
        parent::__construct($logger, $context);
        $this->userService = $userService;
    }

    public function execute(): array
    {
        $this->log->debug('Running UserMapAction');

        // FIXME: Starting point is set to Budapest (but we might not need this at all)
        $posX = 47.4984;
        $posY = 19.0405;
        $coords = $this->userService->getPushPinsForUsers();

        return compact('posX', 'posY', 'coords');
    }
}
