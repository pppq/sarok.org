<?php declare(strict_types=1);

namespace Sarok\Models;

use DateTime;

/**
 * Represents a bookmarked entry for presentation on the UI.
 */
class FavouriteItem
{
    private int      $entryID;
    private DateTime $lastVisited;
    private int      $newComments;
    private string   $authorLogin = '';
    private string   $diaryLogin  = '';
    
    public function __construct(Favourite $favourite) {
        $this->entryID = $favourite->getEntryID();
        $this->lastVisited = $favourite->getLastVisited();
        $this->newComments = $favourite->getNewComments();
    }
    
    public function getEntryID() : int
    {
        return $this->entryID;
    }
    
    public function getLastVisited() : DateTime
    {
        return $this->lastVisited;
    }
    
    public function getNewComments() : int
    {
        return $this->newComments;
    }

    public function getAuthorLogin() : string
    {
        return $this->authorLogin;
    }

    public function setAuthorLogin(string $authorLogin) : void
    {
        $this->authorLogin = $authorLogin;
    }

    public function getDiaryLogin() : string
    {
        return $this->diaryLogin;
    }

    public function setDiaryLogin(string $diaryLogin) : void
    {
        $this->diaryLogin = $diaryLogin;
    }
}
