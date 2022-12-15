<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\Util;
use Sarok\DB;
use Sarok\Repository\Repository;
use Sarok\Models\MessagePartner;
use Sarok\Models\Message;
use InvalidArgumentException;
use DateTime;

final class MessageRepository extends Repository
{
    public const TABLE_NAME = 'mail';
    
    public const COLUMN_NAMES = array(
        Message::FIELD_ID,
        Message::FIELD_RECIPIENT,
        Message::FIELD_SENDER,
        Message::FIELD_DATE,
        Message::FIELD_TITLE,
        Message::FIELD_BODY,
        Message::FIELD_IS_READ,
        Message::FIELD_IS_DELETED_BY_RECIPIENT,
        Message::FIELD_IS_DELETED_BY_SENDER,
        Message::FIELD_REPLY_ON,
    );
    
    public function __construct(DB $db)
    {
        parent::__construct($db);
    }
    
    public function getUnreadMessage(int $recipientID, bool $latestOrEarliest) : ?Message
    {
        $selectColumns = array(
            Message::FIELD_ID,
            Message::FIELD_SENDER,
            Message::FIELD_DATE,
            Message::FIELD_TITLE,
        );

        $selectColumnList = $this->toColumnList($selectColumns);
        $t_mail = self::TABLE_NAME;
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isRead = Message::FIELD_IS_READ;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $c_date = Message::FIELD_DATE;
        $order = $latestOrEarliest ? 'ASC' : 'DESC';
        
        $q = "SELECT `${selectColumnList}` FROM `${t_mail}` " .
            "WHERE `${c_recipient}` = ? AND `${c_isRead}` = 'N' AND `${c_isDeletedByRecipient}` = 'N' " .
            "ORDER BY `${c_date}` ${order} LIMIT 1";
        
        return $this->db->querySingleObject($q, Message::class, 'i', $recipientID);
    }
    
    public function getUnreadMessageCount(int $recipientID) : int
    {
        $c_id = Message::FIELD_ID;
        $t_mail = self::TABLE_NAME;
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isRead = Message::FIELD_IS_READ;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;

        $q = "SELECT COUNT(`${c_id}`) FROM `${t_mail}` " .
            "WHERE `${c_recipient}` = ? AND `${c_isRead}` = 'N' AND `${c_isDeletedByRecipient}` = 'N'";
        
        return $this->db->queryInt($q, 0, 'i', $recipientID);
    }
    
    public function getPartners(int $recipientID) : array
    {
        $c_sender = Message::FIELD_SENDER;
        $c_id = Message::FIELD_ID;
        $t_mail = self::TABLE_NAME;
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        
        $q = "SELECT `${c_sender}` AS `sender`, COUNT(`${c_id}`) AS `messages` FROM `${t_mail}` " .
            "WHERE `${c_recipient}` = ? AND `${c_isDeletedByRecipient}` = 'N' " . 
            "GROUP BY `${c_sender}`";
        
        return $this->db->queryObjects($q, MessagePartner::class, 'i', $recipientID);
    }

    public function getParticipants(int $mailID, /*out*/ int &$senderID, /*out*/ int &$recipientID) : bool
    {
        $c_sender = Message::FIELD_SENDER;
        $c_recipient = Message::FIELD_RECIPIENT;
        $t_mail = self::TABLE_NAME;
        $c_id = Message::FIELD_ID;
        
        $q = "SELECT `${c_sender}`, `${c_recipient}` FROM `${t_mail}` WHERE `${c_id}` = ? LIMIT 1";
        
        $result = $this->db->query($q, 'i', $mailID);
        if ($result !== false && $participants = $result->fetch_row()) {
            $senderID = (int) $participants[0];
            $recipientID = (int) $participants[1];
            return true;
        }
        
        return false;
    }
    
    public function getMessage(string $encryptionKey, int $messageID, int $participantID) : ?Message
    {
        if ($encryptionKey === '') {
            throw new InvalidArgumentException('Encryption key is required for retrieving messages.');
        }
        
        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        // Add key and decryption function around body column
        $c_body = Message::FIELD_BODY;
        str_replace("`${c_body}`", "AES_DECRYPT(`${c_body}`, ?)", $columnList);
        
        $t_mail = self::TABLE_NAME;
        $c_id = Message::FIELD_ID;
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $c_sender = Message::FIELD_SENDER;
        $c_isDeletedBySender = Message::FIELD_IS_DELETED_BY_SENDER;
        
        // Query will report "not found" if the participant is not the sender or the recipient of the message
        $q = "SELECT `${columnList}` FROM `${t_mail}` " .
            "WHERE `${c_id}` = ? AND " .
            "((`${c_recipient}` = ? AND `${c_isDeletedByRecipient}` = 'N') OR (`${c_sender}` = ? AND `${c_isDeletedBySender}` = 'N'))";
        
        return $this->db->querySingleObject($q, Message::class, 'siii',
            $encryptionKey, $messageID, $participantID, $participantID);
    }
    
    public function findMessages(string $encryptionKey, array $filters, int $offset = 0, int $limit = 50) : array
    {
        if ($encryptionKey === '') {
            throw new InvalidArgumentException('Encryption key is required for retrieving messages.');
        }

        $selectColumns = self::COLUMN_NAMES;
        $columnList = $this->toColumnList($selectColumns);
        // Add key and decryption function around body column
        $c_body = Message::FIELD_BODY;
        str_replace("`${c_body}`", "AES_DECRYPT(`${c_body}`, ?)", $columnList);

        $t_mail = self::TABLE_NAME;
        $c_sender = Message::FIELD_SENDER;
        $c_isDeletedBySender = Message::FIELD_IS_DELETED_BY_SENDER;
        $c_title = Message::FIELD_TITLE;
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $c_date = Message::FIELD_DATE;
        
        $q = "SELECT `${columnList}` FROM `${t_mail}` WHERE 1";

        // Extract query parameters to local variables
        list(
            'senderID'    => $senderID,
            'keyword'     => $keyword,
            'recipientID' => $recipientID,
            'date'        => $date,
        ) = $filters;

        $params = array($encryptionKey);
        
        if (isset($senderID) && $senderID > 0) {
            $q .= " AND `${c_sender}` = ? AND `${c_isDeletedBySender}` = 'N'";
            $params[] = $senderID;
        }
        
        if (isset($keyword) && strlen($keyword) > 0) {
            $q .= " AND (`${c_title}` LIKE ? OR AES_DECRYPT(`${c_body}`, ?) LIKE ?)";
            $keywordWithWildcard = "%$keyword%";
            array_push($params, $keywordWithWildcard, $encryptionKey, $keywordWithWildcard);
        }
        
        if (isset($recipientID) && $recipientID > 0) {
            $q .= " AND `${c_recipient}` = ? AND `${c_isDeletedByRecipient}` = 'N'";
            $params[] = $recipientID;
        }
        
        if (isset($date) && is_a($date, DateTime::class)) {
            $q .= " AND DATE(`${c_date}`) = ?";
            $params[] = Util::dateToString($date);
        }
        
        $q .= " ORDER BY `${c_date}` DESC LIMIT ?, ?";
        array_push($params, $offset, $limit);
        return $this->db->queryObjectsWithParams($q, Message::class, $params);
    }
    
    private function setBooleanFlag(string $column, int $mailID) : bool
    {
        $t_mail = self::TABLE_NAME;
        $c_id = Message::FIELD_ID;
        
        $q = "UPDATE `${t_mail}` SET `${column}` = 'Y' WHERE `${c_id}` = ? LIMIT 1";
        return $this->db->execute($q, 'i', $mailID) > 0;
    }

    public function markRead(int $mailID) : bool
    {
        return $this->setBooleanFlag(Message::FIELD_IS_READ, $mailID);
    }

    public function markDeletedByRecipient(int $mailID) : bool
    {
        return $this->setBooleanFlag(Message::FIELD_IS_DELETED_BY_RECIPIENT, $mailID);
    }

    public function markDeletedBySender(int $mailID) : bool
    {
        return $this->setBooleanFlag(Message::FIELD_IS_DELETED_BY_SENDER, $mailID);
    }
    
    public function save(string $encryptionKey, Message $message) : int
    {
        $t_mail = self::TABLE_NAME;
        $messageArray = $message->toArray();
        
        $insertColumns = array_keys($messageArray);
        $values = array_values($messageArray);
        if ($message->getID() < 0) {
            array_shift($insertColumns);
            array_shift($values);
        }
        
        $columnList = $this->toColumnList($insertColumns);
        
        // Body text should be encrypted; modify the corresponding placeholder
        $bodyIdx = array_search(Message::FIELD_BODY, $insertColumns);
        $placeholders = array_fill(0, count($insertColumns), '?');
        $placeholders[$bodyIdx] = 'AES_ENCRYPT(?, ?)';
        $placeholderList = implode(', ', $placeholders);
        
        // Insert key after value for 'body'
        array_splice($values, $bodyIdx + 1, 0, $encryptionKey);

        $q = "INSERT INTO `${t_mail}` (`${columnList}`) VALUES (${placeholderList})";
        
        // Type string is also extended with an extra 's' for the key
        if ($message->getID() < 0) {
            $affectedRows = $this->db->execute($q, 'iisssssssi', ...$values);
            if ($affectedRows > 0) {
                $message->setID($this->db->getLastInsertID());
            }
        } else {
            $affectedRows = $this->db->execute($q, 'iiisssssssi', ...$values);
        }

        return $affectedRows;
    }
}
