<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\ActionPage;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\RegistrationStep2Action;
use Sarok\Actions\RegistrationStep1Action;

class RegistrationActionPage extends ActionPage
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function canExecute() : bool
    {
        return !($this->context->getProperty(Context::PROP_IS_LOGGED_IN));
    }

    public function init() : void
    {
        $actionMap = array(
            "step1" => RegistrationStep1Action::class,
            "step2" => RegistrationStep2Action::class,
        );

        $path = $this->context->getPathSegment(0);

        if (!isset($actionMap[$path])) {
            $action = RegistrationStep1Action::class;
        } else {
            $action = $actionMap[$path];
        }

        $this->addAction("main", $action);
    }
}
