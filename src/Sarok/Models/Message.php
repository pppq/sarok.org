<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

class Message {

    const FIELD_ID = 'ID';
    const FIELD_RECIPIENT = 'Recipient';
    const FIELD_SENDER = 'Sender';
    const FIELD_DATE = 'Date';
    const FIELD_TITLE = 'Title';
    const FIELD_BODY = 'Body';
    const FIELD_IS_READ = 'isRead';
    const FIELD_IS_DELETED_BY_RECIPIENT = 'isDeletedByRecipient';
    const FIELD_IS_DELETED_BY_SENDER = 'isDeletedBySender';
    const REPLY_ON = 'replyOn';
    
    // Assignment requires conversion via magic method (__set)
    private DateTime $_Date;
    private bool $isRead = false;
    private bool $isDeletedByRecipient = false;
    private bool $isDeletedBySender = false;
    
    // Assignment from string directly supported
    private int $ID = -1;
    private int $Recipient = 0;
    private int $Sender = 0;
    private string $Title = '';
    private string $Body = '';
    private int $replyOn = 0;
    
    public function __construct() {
        if (!isset($this->_lastUpdate)) {
            // Default is 'zero date', but 'now' is more appropriate
            $this->_date = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, $value) {
        // Support conversion from string for fetch_object()
        if ($name === self::FIELD_DATE && is_string($value)) {
            $this->setDate(Util::utcDateTimeFromString($value));
        }
        
        if ($name === self::FIELD_IS_READ && is_string($value)) {
            $this->setRead(Util::yesNoToBool($value));
        }
        
        if ($name === self::FIELD_IS_DELETED_BY_RECIPIENT && is_string($value)) {
            $this->setDeletedByRecipient(Util::yesNoToBool($value));
        }
        
        if ($name === self::FIELD_IS_DELETED_BY_SENDER && is_string($value)) {
            $this->setDeletedBySender(Util::yesNoToBool($value));
        }
    }

    public function getID() : int {
        return $this->ID;
    }

    public function setID(int $ID) {
        $this->ID = $ID;
    }

    public function getRecipient() : int {
        return $this->recipient;
    }

    public function setRecipient(int $recipient) {
        $this->recipient = $recipient;
    }

    public function getSender() : int {
        return $this->sender;
    }

    public function setSender(int $sender) {
        $this->sender = $sender;
    }

    public function getDate() : DateTime {
        return $this->_date;
    }
    
    public function setDate(DateTime $date) {
        $this->_date = $date;
    }
    
    public function getTitle() : string {
        return $this->title;
    }

    public function setTitle(string $title) {
        $this->title = $title;
    }

    public function getBody() : string {
        return $this->body;
    }

    public function setBody(string $body) {
        $this->body = $body;
    }

    public function isRead() : bool {
        return $this->read;
    }

    public function setRead(bool $read) {
        $this->read = $read;
    }

    public function getDeletedByRecipient() : bool {
        return $this->deletedByRecipient;
    }

    public function setDeletedByRecipient(bool $deletedByRecipient) {
        $this->deletedByRecipient = $deletedByRecipient;
    }

    public function getDeletedBySender() : bool {
        return $this->deletedBySender;
    }

    public function setDeletedBySender(bool $deletedBySender) {
        $this->deletedBySender = $deletedBySender;
    }

    public function getReplyOn() : int {
        return $this->replyOn;
    }

    public function setReplyOn(int $replyOn) {
        $this->replyOn = $replyOn;
    }
    
    public function toArray() : array {
        return array(
            $this->ID, // 0
            $this->recipient, // 1
            $this->sender, // 2
            Util::dateTimeToString($this->_date), // 3
            $this->title, // 4
            $this->body, // 5
            Util::boolToYesNo($this->read),
            Util::boolToYesNo($this->deletedByRecipient),
            Util::boolToYesNo($this->deletedBySender),
            $this->replyOn,
        );
    }
}
