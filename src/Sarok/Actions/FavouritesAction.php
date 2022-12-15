<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Context;
use Sarok\Logger;
use Sarok\Service\UserService;

final class FavouritesAction extends Action
{
	private UserService $userService;

	public function __construct(
		Logger $logger,
		Context $context,
		UserService $userService
	) {
		parent::__construct($logger, $context);
		$this->userService = $userService;
	}

	public function execute() : array
	{
		$this->log->debug('Executing FavouritesAction');

		$user = $this->getUser();
		$userID = $user->getID();
		$favourites = $this->userService->getFavourites($userID);

		// FIXME: Code was looking for path paramers earlier. But why?
		// if(!sizeof($this->context->params))
		
		return compact('favourites');
 	}
}
