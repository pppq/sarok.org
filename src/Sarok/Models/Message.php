<?php declare(strict_types=1);

namespace Sarok\Models;

use DateTime;
use Sarok\Util;

/** 
 * Represents a single private message sent by a user to another user (or themselves).
 * 
 * Table structure for `mail`:
 *
 * ```sql
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
 * ```
 */
class Message
{
    const FIELD_ID                      = 'ID';
    const FIELD_RECIPIENT               = 'Recipient';
    const FIELD_SENDER                  = 'Sender';
    const FIELD_DATE                    = 'Date';
    const FIELD_TITLE                   = 'Title';
    const FIELD_BODY                    = 'Body';
    const FIELD_IS_READ                 = 'isRead';
    const FIELD_IS_DELETED_BY_RECIPIENT = 'isDeletedByRecipient';
    const FIELD_IS_DELETED_BY_SENDER    = 'isDeletedBySender';
    const FIELD_REPLY_ON                = 'replyOn';
    
    // XXX: Field name capitalization must match column names in the corresponding SQL table
    private int $ID                    = -1;
    private int $Recipient             = 0;
    private int $Sender                = 0;
    private DateTime $_Date;
    private string $Title              = '';
    private string $Body               = '';
    private bool $isRead               = false;
    private bool $isDeletedByRecipient = false;
    private bool $isDeletedBySender    = false;
    private int $replyOn               = 0;
    
    public function __construct()
    {
        if (!isset($this->_Date)) {
            // MySQL's default is 'zero date', but 'now' is more appropriate
            $this->_Date = Util::utcDateTimeFromString();
        }
    }
    
    public function __set(string $name, mixed $value) : void
    {
        if (self::FIELD_DATE === $name && is_string($value)) {
            $this->setDate(Util::utcDateTimeFromString($value));
        }
        
        if (self::FIELD_IS_READ === $name && is_string($value)) {
            $this->setRead(Util::yesNoToBool($value));
        }
        
        if (self::FIELD_IS_DELETED_BY_RECIPIENT === $name && is_string($value)) {
            $this->setDeletedByRecipient(Util::yesNoToBool($value));
        }
        
        if (self::FIELD_IS_DELETED_BY_SENDER === $name && is_string($value)) {
            $this->setDeletedBySender(Util::yesNoToBool($value));
        }
    }

    public function getID() : int
    {
        return $this->ID;
    }

    public function setID(int $ID) : void
    {
        $this->ID = $ID;
    }

    public function getRecipient() : int
    {
        return $this->Recipient;
    }

    public function setRecipient(int $recipient) : void
    {
        $this->Recipient = $recipient;
    }

    public function getSender() : int
    {
        return $this->Sender;
    }

    public function setSender(int $sender) : void
    {
        $this->Sender = $sender;
    }

    public function getDate() : DateTime
    {
        return $this->_Date;
    }
    
    public function setDate(DateTime $date) : void
    {
        $this->_Date = $date;
    }
    
    public function getTitle() : string
    {
        return $this->Title;
    }

    public function setTitle(string $title) : void
    {
        $this->Title = $title;
    }

    public function getBody() : string
    {
        return $this->Body;
    }

    public function setBody(string $body) : void
    {
        $this->Body = $body;
    }

    public function isRead() : bool
    {
        return $this->isRead;
    }

    public function setRead(bool $read) : void
    {
        $this->isRead = $read;
    }

    public function isDeletedByRecipient() : bool
    {
        return $this->isDeletedByRecipient;
    }

    public function setDeletedByRecipient(bool $deletedByRecipient) : void
    {
        $this->isDeletedByRecipient = $deletedByRecipient;
    }

    public function isDeletedBySender() : bool
    {
        return $this->isDeletedBySender;
    }

    public function setDeletedBySender(bool $deletedBySender) : void
    {
        $this->isDeletedBySender = $deletedBySender;
    }

    public function getReplyOn() : int
    {
        return $this->replyOn;
    }

    public function setReplyOn(int $replyOn) : void
    {
        $this->replyOn = $replyOn;
    }
    
    public function toArray() : array
    {
        return array(
            self::FIELD_ID                      => $this->ID,
            self::FIELD_RECIPIENT               => $this->Recipient,
            self::FIELD_SENDER                  => $this->Sender,
            self::FIELD_DATE                    => Util::dateTimeToString($this->_Date),
            self::FIELD_TITLE                   => $this->Title,
            self::FIELD_BODY                    => $this->Body,
            self::FIELD_IS_READ                 => Util::boolToYesNo($this->isRead),
            self::FIELD_IS_DELETED_BY_RECIPIENT => Util::boolToYesNo($this->isDeletedByRecipient),
            self::FIELD_IS_DELETED_BY_SENDER    => Util::boolToYesNo($this->isDeletedBySender),
            self::FIELD_REPLY_ON                => $this->replyOn,
        );
    }
}
