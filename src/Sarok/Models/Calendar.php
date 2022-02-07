<?php namespace Sarok\Models;

class Calendar {

    const FIELD_USER_ID = 'userID';
    const FIELD_Y = 'y';
    const FIELD_M = 'm';
    const FIELD_D = 'd';
    const FIELD_NUM_PUBLIC = 'numPublic';
    const FIELD_NUM_REGISTERED = 'numRegistered';
    const FIELD_NUM_FRIENDS = 'numFriends';
    const FIELD_NUM_ALL = 'numAll';
    const FIELD_NUM_MAILS_RECEIVED = 'numMailsReceived';
    const FIELD_NUM_MAILS_SENT = 'numMailsSent';

    private int $userID = 0;
    private int $y = 0;
    private int $m = 0;
    private int $d = 0;
    private int $numPublic = 0;
    private int $numRegistered = 0;
    private int $numFriends = 0;
    private int $numAll = 0;
    private int $numMailsReceived = 0;
    private int $numMailsSent = 0;
    
    public function getUserID() : int {
        return $this->userID;
    }

    public function setUserID(int $userID) {
        $this->userID = $userID;
    }

    public function getY() : int {
        return $this->y;
    }

    public function setY(int $y) {
        $this->y = $y;
    }

    public function getM() : int {
        return $this->m;
    }

    public function setM(int $m) {
        $this->m = $m;
    }

    public function getD() : int {
        return $this->d;
    }

    public function setD(int $d) {
        $this->d = $d;
    }

    public function getNumPublic() : int {
        return $this->numPublic;
    }

    public function setNumPublic(int $numPublic) {
        $this->numPublic = $numPublic;
    }

    public function getNumRegistered() : int {
        return $this->numRegistered;
    }

    public function setNumRegistered(int $numRegistered) {
        $this->numRegistered = $numRegistered;
    }

    public function getNumFriends() : int {
        return $this->numFriends;
    }

    public function setNumFriends(int $numFriends) {
        $this->numFriends = $numFriends;
    }

    public function getNumAll() : int {
        return $this->numAll;
    }

    public function setNumAll(int $numAll) {
        $this->numAll = $numAll;
    }

    public function getNumMailsReceived() : int {
        return $this->numMailsReceived;
    }

    public function setNumMailsReceived(int $numMailsReceived) {
        $this->numMailsReceived = $numMailsReceived;
    }

    public function getNumMailsSent() : int {
        return $this->numMailsSent;
    }

    public function setNumMailsSent(int $numMailsSent) {
        $this->numMailsSent = $numMailsSent;
    }

    public function toArray() : array {
        return array(
            $this->userID,
            $this->y,
            $this->m,
            $this->d,
            $this->numPublic,
            $this->numRegistered,
            $this->numFriends,
            $this->numAll,
            $this->numMailsReceived,
            $this->numMailsSent,
        );
    }
}
