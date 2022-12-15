<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Models\Comment;
use Sarok\Models\User;
use Sarok\Service\BlogService;
use Sarok\Service\UserService;
use Sarok\TextProcessor;
use Sarok\Util;

final class EntryReadAction extends Action
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
        $this->log->debug('Executing EntryReadAction');

        $loggedIn = $this->isLoggedIn();
        $yourName = $this->getCookie('your_name');
        $yourWeb = $this->getCookie('your_web');

        $user = $this->getUser();
        $userID = $user->getID();

        $blog = $this->getBlog();
        $blogID = $blog->getID();
        $this->userService->populateUserData($blog, User::KEY_BLOG_NAME);
        $blogName = $blog->getUserData(User::KEY_BLOG_NAME);

        $entryID = $this->context->getEntryID();

        if ($this->blogService->canViewEntry($userID, $blogID, $entryID) === false) {
            $this->setTemplateName('error');
            return array();
        }

        $entry = $this->blogService->getEntryByID($entryID);
        $authorID = $entry->getUserID();

        $visitDate = Util::utcDateTimeFromString();
        if ($authorID === $userID) {
            /* 
             * Reset the entry's timestamp used for showing "new comments since your last visit" 
             * if the reader is also the entry's author
             */
            $this->blogService->updateEntryLastVisit($entryID, $visitDate);
        }

        /* 
         * Reset the favorite's timestamp used for showing "new comments since your last visit" 
         * if the reader has favorited the entry; also record this fact in a variable
         */
        $favorited = $this->blogService->updateFavoriteLastVisit($entryID, $userID, $visitDate);

        // Postformatting steps are always applied to the content in the DB
        $entry->setBody($this->textProcessor->postFormat($entry->getBody()));
        $entry->setBody2($this->textProcessor->postFormat($entry->getBody2()));
        
        $comments = $this->blogService->getComments($entryID, $userID);

        $userIDs = array($authorID);
        $commentIDs = array();

        foreach ($comments as $c) {
            $userIDs[] = $c->getUserID();
            $commentIDs[] = $c->getID();
        }

        $logins = $this->userService->getLogins(array_unique($userIDs));

        // Fetch commentIDs the current reader has already rated
        $ratedCommentIDs = $this->blogService->getRatedCommentIDs($commentIDs, $userID);
        $canRateComment = function (Comment $c) use ($userID, $ratedCommentIDs) {
            /* 
             * A comment can be rated by the current reader if:
             * 
             * - they are logged in
             * - they are not the person who wrote the comment 
             * - they haven't rated the comment already
             */
            return $userID !== User::ID_ANONYMOUS
                && $userID !== $c->getUserID()
                && !in_array($c->getID(), $ratedCommentIDs);
        };

        $canAddComment = $this->blogService->canAddComment($userID, $entryID);
        $canChangeEntry = $this->blogService->canChangeEntry($userID, $entryID);
        $canFavoriteEntry = $this->isLoggedIn();
        
        // Make this a closure so the template can call it with different arguments
        $canDeleteComment = $this->blogService->canDeleteComment(...);

        // Filter comments made by users banned by the reader, or users who have banned the reader
        $bans = $this->userService->getBans($userID);
        $banOfs = $this->userService->getBanOfs($userID);
        $comments = array_filter($comments, function (Comment $c) use ($bans, $banOfs) {
            $commentAuthorID = $c->getUserID();
            return !in_array($commentAuthorID, $bans) && !in_array($commentAuthorID, $banOfs);
        });

        $tags = $this->blogService->getTags($entryID);

        return compact(
            'loggedIn',
            'yourName',
            'yourWeb',
            'readerID',
            'blogID',
            'blogName',
            'authorID',
            'entry',
            'comments',
            'tags',
            'favorited',
            'logins',
            'canRateComment',
            'canAddComment',
            'canDeleteComment',
            'canChangeEntry',
            'canFavoriteEntry'
        );
    }
}
