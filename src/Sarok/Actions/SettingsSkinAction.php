<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\UserService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class SettingsSkinAction extends Action
{
    private UserService $userService;

    public function __construct(Logger $logger, Context $context, UserService $userService)
    {
        parent::__construct($logger, $context);
        $this->userService = $userService;
    }

    public function execute() : array
    {
        $this->log->debug('Running SettingsSkinAction');
        
        $user = $this->getUser();
        $this->userService->populateUserData($user, User::KEY_CSS, User::KEY_SKIN_NAME);

        if ($this->isPOST()) {
            return update($user);
        }

        $css = $user->getUserData(User::KEY_CSS);
        $skinName = $user->getUserData(User::KEY_SKIN_NAME);
        $skins = [
            'default' => 'Alap',
            'classic' => 'Régi',
            'yellow'  => 'Szep, sarga (oldstyle)',
            'minimal' => 'Csunya (munkahelyi)',
            'greybox' => 'GreyBox',
        ];
        
        return compact('css', 'skinName', 'skins');
    }

    public function update(User $user) : array
    {
        $css = $this->getPOST(User::KEY_CSS);
        if (isset($css)) {
            $user->setUserData(User::KEY_CSS, $css);
        }
        
        $skinName = $this->getPOST(User::KEY_SKIN_NAME);
        if (isset($skinName)) {
            $user->setUserData(User::KEY_SKIN_NAME, $skinName);
        }

        $this->userService->saveUserData($user);
        
        $this->setTemplateName('empty');
        $location = $this->getPOST('location', '/settings/skin');
        return compact('location');
    }
}
