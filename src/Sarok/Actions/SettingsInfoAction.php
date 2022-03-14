<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\UserService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class SettingsInfoAction extends Action
{
	/** @var array<string> */
	private const KEYS = [
		User::KEY_NAME,
		User::KEY_BLOG_NAME,
		User::KEY_OCCUPATION,
		User::KEY_HAIR_COLOR,
		User::KEY_EYE_COLOR,
		User::KEY_DESCRIPTION,
		User::KEY_SEX,
		User::KEY_DISTRICT,
		User::KEY_COUNTRY,
		User::KEY_CITY,
		User::KEY_STATE,
		User::KEY_EMAIL,
		User::KEY_WIW,
		User::KEY_MSN,
		User::KEY_ICQ,
		User::KEY_SKYPE,
		User::KEY_PHONE,
		User::KEY_BIRTH_YEAR,
		User::KEY_BIRTHDATE,
		User::KEY_KEYWORDS,
		User::KEY_PUBLIC_INFO,
	];

    private UserService $userService;

    public function __construct(Logger $logger, Context $context, UserService $userService) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
    }

    public function execute() : array
    {
        $this->log->debug('Running SettingsInfoAction');

		$user = $this->getUser();

		if ($this->isPOST()) {
            return $this->update($user);
        }

        // Populate all properties we intend to display
        return $this->userService->populateUserDataAssoc($user, ...self::KEYS);
 	}

	 private function update(User $user) : array
	 {
		 foreach (self::KEYS as $key) {
			$user->setUserData($key, $this->getPOST($key));
		 }
	 
		 $this->userService->saveUserData($user);
        
		 $this->setTemplateName('empty');
		 $location = $this->getPOST('location', '/settings/info');
		 return compact('location');
	 }
}
