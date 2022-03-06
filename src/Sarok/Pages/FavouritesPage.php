<?php declare(strict_types=1);

namespace Sarok\Pages;

use Sarok\Pages\ActionPage;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\FavouritesAction;

class FavouritesActionPage extends ActionPage
{
    public function __construct(Logger $logger, Context $context)
    {
        parent::__construct($logger, $context);
    }

    public function init() : void
    {
        $this->addAction("main", FavouritesAction::class);

        if ($this->context->isPOST()) {
            // TODO: POST requests should update favourites
            $this->setTemplateName("empty");
        }
    }
}
