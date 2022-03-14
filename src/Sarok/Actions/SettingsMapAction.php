<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Service\UserService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;

class SettingsMapAction extends Action
{
    private UserService $userService;

    public function __construct(Logger $logger, Context $context, UserService $userService) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
    }

    public function execute() : array
    {
        $this->log->debug('Running SettingsMapAction');

        $user = $this->getUser();

        if ($this->isPOST()) {
            return $this->update($user);
        }

        list($posX, $posY, $bindtoMap) = $this->userService->populateUserData($user, 
            User::KEY_POS_X, 
            User::KEY_POS_Y, 
            User::KEY_BIND_TO_MAP);

        $coords = $this->blogFacade->getMapMarkers($this->context->user);

        return compact('posX', 'posY', 'bindToMap', 'coords');
    }
    
    private function update(User $user) : array
    {
        $clearMap = $this->getPOST('clearMap');
        if (isset($clearMap)) {
            $user->setUserData(User::KEY_POS_X, '');
            $user->setUserData(User::KEY_POS_Y, '');
        } else {
            $posX = $this->getPOST(User::KEY_POS_X);
            if (isset($posX)) {
                $user->setUserData(User::KEY_POS_X, $posX);
            }
    
            $posY = $this->getPOST(User::KEY_POS_Y);
            if (isset($posY)) {
                $user->setUserData(User::KEY_POS_Y, $posY);
            }
        }
        
        $bindToMap = $this->getPOST(User::KEY_BIND_TO_MAP);
        if (isset($bindToMap)) {
            $user->setUserData(User::KEY_BIND_TO_MAP, $bindToMap);
        }

        $this->userService->saveUserData($user);
        
        $this->setTemplateName('empty');
        $location = $this->getPOST('location', '/settings/skin');
        return compact('location');
    }    
}
