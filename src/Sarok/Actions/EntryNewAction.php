<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Models\AccessType;
use Sarok\Util;

class EntryNewAction extends Action
{
	public function __construct(Logger $logger, Context $context)
	{
		parent::__construct($logger, $context);
	}

	public function execute(): array
	{
		$this->log->debug('Running EntryNewAction');

		$user = $this->getUser();
		if ($user->getID() === User::ID_ANONYMOUS) {
			return Action::NO_DATA;
		}

		$blog = $this->getBlog();
		$blogLogin = $blog->getLogin();
		$messageAccess = $blog->getUserData(User::KEY_MESSAGE_ACCESS, AccessType::ALL->value);
		$commentAccess = $blog->getUserData(User::KEY_COMMENT_ACCESS, AccessType::ALL->value);

		$userID = $user->getID();
		$userLogin = $user->getLogin();
		$body = $user->getUserData(User::KEY_BACKUP);
		$posX = $user->getUserData(User::KEY_POS_X);
		$posY = $user->getUserData(User::KEY_POS_Y);
		$bindToMap = $user->getUserData(User::KEY_BIND_TO_MAP, 'N');

		$createDate = Util::utcDateTimeFromString();
		
		return compact('blogLogin', 'userID', 'userLogin', 'body', 'posX', 'posY', 'bindToMap', 'createDate');
	}
}
