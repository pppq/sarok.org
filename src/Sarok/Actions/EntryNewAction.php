<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Models\AccessType;
use Sarok\Service\UserService;
use Sarok\Util;

final class EntryNewAction extends Action
{
	private UserService $userService;

	public function __construct(Logger $logger, Context $context, UserService $userService)
	{
		parent::__construct($logger, $context);
		$this->userService = $userService;
	}

	public function execute(): array
	{
		$this->log->debug('Executing EntryNewAction');

		$user = $this->getUser();
		$userID = $user->getID();
		$userLogin = $user->getLogin();

		if ($userID === User::ID_ANONYMOUS) {
			return Action::NO_DATA;
		}

		$blog = $this->getBlog();
		$blogLogin = $blog->getLogin();

		$this->userService->populateUserData($blog, 
			User::KEY_MESSAGE_ACCESS, 
			User::KEY_COMMENT_ACCESS);
			
		$messageAccess = $blog->getUserData(User::KEY_MESSAGE_ACCESS, AccessType::ALL->value);
		$commentAccess = $blog->getUserData(User::KEY_COMMENT_ACCESS, AccessType::ALL->value);

		$this->userService->populateUserData($user, 
			User::KEY_BACKUP, 
			User::KEY_POS_X,
			User::KEY_POS_Y, 
			User::KEY_BIND_TO_MAP);

		$body = $user->getUserData(User::KEY_BACKUP);
		$posX = $user->getUserData(User::KEY_POS_X);
		$posY = $user->getUserData(User::KEY_POS_Y);
		$bindToMap = $user->getUserData(User::KEY_BIND_TO_MAP, 'N');

		$createDate = Util::utcDateTimeFromString();
		
		return compact('blogLogin', 'userID', 'userLogin', 'body', 'posX', 'posY', 'bindToMap', 'createDate');
	}
}
