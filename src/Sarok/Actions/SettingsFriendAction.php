<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Models\User;
use Sarok\Service\SessionService;
use Sarok\Service\UserService;
use Sarok\Util;

class SettingsFriendAction extends Action
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct(
        Logger $logger, 
        Context $context, 
        UserService $userService,
        SessionService $sessionService
    ) {
        parent::__construct($logger, $context);
        $this->userService = $userService;
        $this->sessionService = $sessionService;
    }

    public function execute(): array
    {
        $this->log->debug('Running SettingsFriendAction');

        $user = $this->getUser();
        $userID = $user->getID();
        $friends = $this->userService->getFriends($userID);
        $bans = $this->userService->getBans($userID);

        if ($this->isPOST()) {
            return $this->update($user, $friends, $bans);
        }

        $IDs = array_unique(array_merge($friends, $bans));
        $logins = $this->userService->getLogins($IDs);

        // Mutual friends/foes will be highlighted
        $friendOfs = $this->userService->getFriendOfs($userID);
        $banOfs = $this->userService->getBanOfs($userID);

        return compact('friends', 'friendOfs', 'bans', 'banOfs', 'logins');
    }

    /**
     * @param User $user
     * @param array<int> $existingFriends
     * @param array<int> $existingBans
     */
    private function update(User $user, array $existingFriends, array $existingBans): void
    {
        $userID = $user->getID();
        $usersToUpdate = array();

        $newFriends = (array) $this->getPOST('friends', array());
        $friendsToRemove = array_diff($existingFriends, $newFriends);

        /* 
         * Removed friends will need a front page refresh, as some entries and comments may no 
         * longer be available to them.
         */
        array_push($usersToUpdate, $friendsToRemove);

        $newBans = (array) $this->getPOST('bans', array());
        $bansToRemove = array_diff($existingBans, $newBans);

        /* 
         * Similar to above, users that are no longer banned might see some comments/entries appear.
         */
        array_push($usersToUpdate, $bansToRemove);

        $friendToAdd = (string) $this->getPOST('newFriend');
        if (strlen($friendToAdd) > 0) {
            $newUser = $this->userService->getUserByLogin($friendToAdd);
            $newUserID = $newUser->getID();
            $newFriends[] = $newUserID;
            $usersToUpdate[] = $newUserID;
        }

        $banToAdd = (string) $this->getPOST('newBan');
        if (strlen($banToAdd) > 0) {
            $newUser = $this->userService->getUserByLogin($banToAdd);
            $newUserID = $newUser->getID();
            $newBans[] = $newUserID;
            $usersToUpdate[] = $newUserID;

            // Banning a user also affects the current user's front page
            $usersToUpdate[] = $user->getID();
        }

        if (count($friendsToRemove) > 0 || strlen($friendToAdd) > 0) {
            $this->userService->setFriends($userID, $newFriends);
        }

        if (count($bansToRemove) > 0 || strlen($banToAdd) > 0) {
            $this->userService->setBans($userID, $newBans);
        }

        $usersToUpdate = array_unique($usersToUpdate);
        if (count($usersToUpdate) > 0) {
            $activeAfter = Util::utcDateTimeFromString();
            $activeAfter->modify('-1 hour');
            $activeUsersToUpdate = $this->sessionService->getActiveUserIDs($usersToUpdate, $activeAfter);

            foreach ($activeUsersToUpdate as $ID) {
                $current = $this->userService->getUserByID($ID);
                
                $current->setUserData(User::KEY_COMMENTS_LOADED, '0');
                $current->setUserData(User::KEY_ENTRIES_LOADED, '0');
                $current->setUserData(User::KEY_COMMENTS_OF_ENTRIES_LOADED, '0');
                $current->setUserData(User::KEY_MY_COMMENTS_LOADED, '0');

                $this->userService->saveUserData($current);
            }
        }
    }
}
