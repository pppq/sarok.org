<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Models\User;
use Sarok\Service\BlogService;
use Sarok\Service\UserService;
use Sarok\TextProcessor;

class EntryListAction extends Action
{
	private BlogService $blogService;
	private UserService $userService;
	private TextProcessor $textProcessor;

	public function __construct(
		Logger $logger, 
		Context $context, 
		BlogService $blogService,
		UserService $userService,
		TextProcessor $textProcessor
	) {
		parent::__construct($logger, $context);
		$this->blogService = $blogService;
		$this->userService = $userService;
		$this->textProcessor = $textProcessor;
	}

	public function execute(): array
	{
		$this->log->debug("Running EntryListAction");

		$blog = $this->getBlog();
		$blogLogin = $blog->getLogin();
		$blogName = $blog->getUserData(User::KEY_BLOG_NAME);

		$user = $this->getUser();
		$entries = $this->blogService->getEntries($user->getID(), $blog->getID(), $this->getPathParams());

		$entryIDs = array();
		$userIDs = array();
		
		foreach ($entries as $e) {
			$entryIDs[] = $e->getID();
			$userIDs[] = $e->getUserID();
			$userIDs[] = $e->getDiaryID();

			// Apply some processing to the stored text
			$e->setBody($this->textProcessor->postFormat($e->getBody()));
			$e->setBody2($this->textProcessor->postFormat($e->getBody2()));
		}

		$tags = $this->blogService->getTagCloudForEntries($entryIDs);
		$logins = $this->userService->getLogins($userIDs);

		// Convert method to a callable so it can be used in the action template
		$canChangeEntry = $this->blogService->canChangeEntry(...);

		return compact('blogLogin', 'blogName', 'entries', 'logins', 'tags', 'canChangeEntry');
	}
}
