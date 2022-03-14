<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Models\MenuItem;

class MenuAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function execute(): array
    {
        $this->log->debug('Running MenuAction');

        if ($this->isLoggedIn()) {
            $user = $this->getUser();
            $login = $user->getLogin();

            $menu = [
                new MenuItem('Főlap', '/'),
                new MenuItem('Bejegyzés irása', "/users/$login/new/"),
                new MenuItem('Beállitások', '/settings/'),
                new MenuItem('Levelezés', '/privates/'),
                new MenuItem('Pizzaszelet', "/users/$login/"),
            ];
        } else {
            $menu = [
                new MenuItem('Főlap', '/'),
                new MenuItem('Regisztráció', '/registration/'),
                new MenuItem('Rolunk', '/about/'),
            ];
        }

        $path = $this->context->getPath();
        return compact('menu', 'path');
    }
}
