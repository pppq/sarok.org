<?php namespace Sarok\Models;

use Sarok\Util;
use DateTime;

/*
 * Table structure for `mail`:
 *
 * `ID`        int(10) unsigned NOT NULL AUTO_INCREMENT,
 * `Recipient` int(10) unsigned NOT NULL DEFAULT '0',
 * `Sender`    int(10) unsigned NOT NULL DEFAULT '0',
 * `Date`      datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `Title`     varchar(255)     NOT NULL DEFAULT '',
 * `Body`      longtext         NOT NULL DEFAULT '',
 * `isRead`    enum('Y','N')    NOT NULL DEFAULT 'N',
 * `isDeletedByRecipient` enum('Y','N') NOT NULL DEFAULT 'N',
 * `isDeletedBySender`    enum('Y','N') NOT NULL DEFAULT 'N',
 * `replyOn`   int(10) unsigned NOT NULL DEFAULT '0',
 */
class Message
{
    const FIELD_ID = 'ID';
    const FIELD_RECIPIENT = 'Recipient';
    const FIELD_SENDER = 'Sender';
    const FIELD_DATE = 'Date';
    const FIELD_TITLE = 'Title';
    const FIELD_BODY = 'Body';
    const FIELD_IS_READ = 'isRead';
    const FIELD_IS_DELETED_BY_RECIPIENT = 'isDeletedByRecipient';
    const FIELD_IS_DELETED_BY_SENDER = 'isDeletedBySender';
    const FIELD_REPLY_ON = 'replyOn';
    
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
    
    public function __construct()
    {
        if (!isset($this->_lastUpdate)) {
            // Default is 'zero date', but 'now' is more appropriate
            $this->_date = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, $value)
    {
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

    public function getID() : int
    {
        return $this->ID;
    }

    public function setID(int $ID)
    {
        $this->ID = $ID;
    }

    public function getRecipient() : int
    {
        return $this->recipient;
    }

    public function setRecipient(int $recipient)
    {
        $this->recipient = $recipient;
    }

    public function getSender() : int
    {
        return $this->sender;
    }

    public function setSender(int $sender)
    {
        $this->sender = $sender;
    }

    public function getDate() : DateTime
    {
        return $this->_date;
    }
    
    public function setDate(DateTime $date)
    {
        $this->_date = $date;
    }
    
    public function getTitle() : string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getBody() : string
    {
        return $this->body;
    }

    public function setBody(string $body)
    {
        $this->body = $body;
    }

    public function isRead() : bool
    {
        return $this->read;
    }

    public function setRead(bool $read)
    {
        $this->read = $read;
    }

    public function getDeletedByRecipient() : bool
    {
        return $this->deletedByRecipient;
    }

    public function setDeletedByRecipient(bool $deletedByRecipient)
    {
        $this->deletedByRecipient = $deletedByRecipient;
    }

    public function getDeletedBySender() : bool
    {
        return $this->deletedBySender;
    }

    public function setDeletedBySender(bool $deletedBySender)
    {
        $this->deletedBySender = $deletedBySender;
    }

    public function getReplyOn() : int
    {
        return $this->replyOn;
    }

    public function setReplyOn(int $replyOn)
    {
        $this->replyOn = $replyOn;
    }
    
    public function toArray() : array
    {
        return array(
            self::FIELD_ID        => $this->ID,
            self::FIELD_RECIPIENT => $this->recipient,
            self::FIELD_SENDER    => $this->sender,
            self::FIELD_DATE      => Util::dateTimeToString($this->_date),
            self::FIELD_TITLE     => $this->title,
            self::FIELD_BODY      => $this->body,
            self::FIELD_IS_READ   => Util::boolToYesNo($this->read),
            self::FIELD_IS_DELETED_BY_RECIPIENT => Util::boolToYesNo($this->deletedByRecipient),
            self::FIELD_IS_DELETED_BY_SENDER => Util::boolToYesNo($this->deletedBySender),
            self::FIELD_REPLY_ON  => $this->replyOn,
        );
    }
}
