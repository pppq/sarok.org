<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class CustomCssAction extends Action
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function execute() : array
    {
        $this->log->debug("Running CustomCssAction");
        
        $user = $this->context->getUser();
        $blog = $this->context->getBlog();
        $userID = $user->getID();
        $blogID = $blog->getID();
        
        if ($userID === User::ID_ANONYMOUS || $userID === $blogID) {
            $css = $blog->getUserData(User::KEY_CSS);
        } else {
            $css = '';
        }

        return compact('css');
    }
}
