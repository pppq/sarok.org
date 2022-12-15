<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Models\AccessType;
use Sarok\Service\BlogService;
use Sarok\Service\UserService;
use Sarok\Util;

final class EntryUpdateAction extends Action
{
	private BlogService $blogService;
	private UserService $userService;

	public function __construct(
		Logger $logger,
		Context $context,
		BlogService $blogService,
		UserService $userService
	) {
		parent::__construct($logger, $context);
		$this->blogService = $blogService;
		$this->userService = $userService;
	}

	public function execute() : array
	{
		$this->log->debug('Executing EntryUpdateAction');

		$blog = $this->getBlog();
		$blogID = $blog->getID();
		$blogLogin = $blog->getLogin();

		$user = $this->getUser();
		$userID = $user->getID();
		$userLogin = $user->getLogin();

		$tags = $this->getPOST('tags');
		$tags = preg_split("/[ ,;]+/", strip_tags($tags));
		$tags = array_unique($tags);

		$userList = array();
		$list = $this->getPOST('list');

		if ($list !== '') {
			$list = preg_split("/[ ,;]+/", strip_tags($list));

			foreach ($list as $listLogin) {
				$listUser = $this->userService->getUserByLogin($listLogin);

				if ($listUser !== null) {
					$userList[] = $listUser->getID();
					$this->log->debug("Adding ${listLogin} to entry access list");
				}
			}
		}

		$needsMap = $this->getPOST('needsMap', 'N');
		if ($needsMap !== 'Y') {
			$posX = $posY = 0.0;
		} else {
			$posX = $this->getPOST('posX');
			$posY = $this->getPOST('posY');
		}

		// Visibility falls back to "private" when not specified correctly
		$access = AccessType::tryFrom($this->getPOST('access')) ?? AccessType::AUTHOR_ONLY;
		$comments = AccessType::tryFrom($this->getPOST('comments')) ?? AccessType::AUTHOR_ONLY;
		$title = $this->getPOST('title');

		$newDiaryLogin = $this->getPOST('diaryLogin');
		$body = $this->getPOST('body');
		list ($body1, $body2) = $this->blogService->splitBodies($body);

		$location = "/users/${blogLogin}/";

		$entryID = $this->getPOST('ID', -1);
		if ($entryID > 0) {
			$this->log->debug("Updating entry #${entryID}");

			$newDiary = $this->userService->getUserByLogin($newDiaryLogin);
			$newDiaryID = $newDiary->getID();
			
			$entry = $this->blogService->getEntryByID($entryID);
			$authorID = $entry->getUserID(); // The ID of the original author (may not be equal to the current user)
			$diaryID = $entry->getDiaryID(); // The ID of the original diary the entry was posted in

			if ($this->blogService->canChangeEntry($userID, $entryID) === false) {
				$this->log->error("User ${userID} can not change entry #{$entryID}");
				return compact('location');
			}
			
			if ($this->blogService->canAddEntry($userID, $newDiaryID) === false) {
				$this->log->error("User ${userID} can not add entries to diary #{$newDiaryID}");
				return compact('location');
			}
			
			$this->blogService->updateEntry(
				$entryID, 
				$newDiaryID, 
				$access, 
				$userList, 
				$comments, 
				$title, 
				$body1, 
				$body2, 
				$tags, 
				$posX, 
				$posY
			);

			$location = "/users/${blogLogin}/m_${entryID}";
			return compact('location');
		}

		// A new entry will be created instead
		if ($newDiaryLogin !== '') {
			$newDiary = $this->userService->getUserByLogin($newDiaryLogin);
			$newDiaryID = $newDiary->getID();
		} else {
			$newDiaryLogin = $blogLogin;
			$newDiaryID = $blogID;
		}

		if ($this->blogService->canAddEntry($userID, $newDiaryID) === false) {
			$this->log->error("User ${userID} can not add entries to diary #{$newDiaryID}");
			return compact('location');
		}

		$this->log->debug("Creating entry in diary '${newDiaryLogin}', author is ${userLogin}");
		
		$entryID = $this->blogService->addEntry(
			$newDiaryID,
			$userID, 
			Util::utcDateTimeFromString(), 
			$access,
			$userList,
			$comments,
			$title,
			$body1,
			$body2,
			$tags,
			$posX,
			$posY
		);

		$location = "/users/${blogLogin}/m_${entryID}";
		return compact('location');
	}
}
