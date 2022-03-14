<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\UserService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\BlogService;

class SettingsBlogAction extends Action
{
    /** @var array<string> */
    private const KEYS = [
        User::KEY_BLOG_ACCESS,
        User::KEY_BLOG_NAME,
        User::KEY_BLOG_TEXT,
        User::KEY_COMMENT_ACCESS,
        User::KEY_COPYRIGHT,
        User::KEY_COPYRIGHT_TEXT,
        User::KEY_GOOGLE,
        User::KEY_MESSAGE_ACCESS,
        User::KEY_STATISTICS,
        User::KEY_ENTRIES_PER_PAGE,
    ];

    private UserService $userService;
    private BlogService $blogService;

    public function __construct(
        Logger $logger,
        Context $context,
        UserService $userService,
        BlogService $blogService,
    ) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->blogService = $blogService;
    }

    public function execute(): array
    {
        $this->log->debug('Running SettingsBlogAction');

        $user = $this->getUser();

        if ($this->isPOST()) {
            return $this->update($user);
        }

        // Populate all properties we intend to display
        return $this->userService->populateUserDataAssoc($user, ...self::KEYS);
    }

    private function update(User $user): array
    {
        foreach (self::KEYS as $key) {
            $user->setUserData($key, $this->getPOST($key));
        }

        $this->userService->saveUserData($user);
        $this->blogService->uncache($user->getID());

        $location = $this->getPOST('location', '/settings/blog');
        return compact('location');
    }
}
