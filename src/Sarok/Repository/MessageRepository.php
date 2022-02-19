<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\Models\Message;
use Sarok\Service\DB;
use DateTime;
use InvalidArgumentException;

class MessageRepository extends AbstractRepository
{
    const TABLE_NAME = 'mail';
    
    private const COLUMN_NAMES = array(
        Message::FIELD_ID,
        Message::FIELD_RECIPIENT,
        Message::FIELD_SENDER,
        Message::FIELD_DATE,
        Message::FIELD_TITLE,
        Message::FIELD_BODY,
        Message::FIELD_IS_READ,
        Message::FIELD_IS_DELETED_BY_RECIPIENT,
        Message::FIELD_IS_DELETED_BY_SENDER,
        Message::REPLY_ON,
    );
    
    public function __construct(DB $db)
    {
        parent::__construct($db);
    }
    
    protected function getTableName() : string
    {
        return self::TABLE_NAME;
    }
    
    protected function getColumnNames() : array
    {
        return self::COLUMN_NAMES;
    }
    
    public function getUnreadMessage(int $recipientID, bool $latestOrEarliest) : Message
    {
        $selectColumns = array(
            Message::FIELD_ID,
            Message::FIELD_SENDER,
            Message::FIELD_DATE,
            Message::FIELD_TITLE,
        );

        $selectColumnList = $this->toColumnList($selectColumns);
        $mail = $this->getTableName();
        $recipient = Message::FIELD_RECIPIENT;
        $isRead = Message::FIELD_IS_READ;
        $isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $date = Message::FIELD_DATE;
        $order = $latestOrEarliest ? 'ASC' : 'DESC';
        
        $q = "SELECT `$selectColumnList` FROM `$mail` " .
            "WHERE `$recipient` = ? AND `$isRead` = 'N' AND `$isDeletedByRecipient` = 'N'" .
            "ORDER BY `$date` $order LIMIT 1";
        
        $messages = $this->db->queryObjects($q, Message::class, 'i', $recipientID);
        return $messages[0];
    }
    
    public function getUnreadMessageCount(int $recipientID) : int
    {
        $id = Message::FIELD_ID;
        $mail = $this->getTableName();
        $recipient = Message::FIELD_RECIPIENT;
        $isRead = Message::FIELD_IS_READ;
        $isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;

        $q = "SELECT COUNT(`$id`) FROM `$mail` " .
            "WHERE `$recipient` = ? AND `$isRead` = 'N' AND `$isDeletedByRecipient` = 'N'";
        
        $results = $this->db->query($q, 'i', $recipientID);
        if ($count = $results->fetch_row()) {
            return (int) $count[0];
        }
        
        return 0;
    }
    
    public function getPartners(int $recipientID) : array
    {
        $sender = Message::FIELD_SENDER;
        $id = Message::FIELD_ID;
        $mail = $this->getTableName();
        $recipient = Message::FIELD_RECIPIENT;
        $isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $messages = 'messages';
        
        $q = "SELECT `$sender`, COUNT(`$id`) AS `$messages` FROM `$mail` " .
            "WHERE `$recipient` = ? AND `$isDeletedByRecipient` = 'N' GROUP BY `$sender`";
        
        $result = $this->db->query($q, 'i', $recipientID);
        $partnerList = array();
        while ($userID = $result->fetch_row()) {
            $partnerList[] = array(
                $sender => $userID[0],
                $messages => (int) $userID[1],
            );
        }
        
        return $partnerList;
    }

    public function getParticipants(int $mailID) : array
    {
        $sender = Message::FIELD_SENDER;
        $recipient = Message::FIELD_RECIPIENT;
        $mail = $this->getTableName();
        $id = Message::FIELD_ID;
        
        $q = "SELECT `$sender`, `$recipient` FROM `$mail` WHERE `$id` = ? LIMIT 1";
        $result = $this->db->query($q, 'i', $mailID);
        if ($participants = $result->fetch_row()) {
            return array(
                $sender => $participants[0],
                $recipient => $participants[1],
            );
        }
        
        return array();
    }
    
    public function getMessage(string $encryptionKey, int $messageID, int $participantID) : ?Message
    {
        if (!isset($encryptionKey) || strlen($encryptionKey) <= 0) {
            throw new InvalidArgumentException("Encryption key is required for retrieving messages.");
        }
        
        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        
        // Add key to body column selecting function
        $body = Message::FIELD_BODY;
        str_replace("`$body`", "AES_DECRYPT(`$body`, ?)", $columnList);
        
        $mail = $this->getTableName();
        $id = Message::FIELD_ID;
        $recipient = Message::FIELD_RECIPIENT;
        $isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $sender = Message::FIELD_SENDER;
        $isDeletedBySender = Message::FIELD_IS_DELETED_BY_SENDER;
        
        // Query will report "not found" if the participant is not the sender or the recipient of the message
        $q = "SELECT `$columnList` FROM `$mail` " .
            "WHERE `$id` = ? AND " .
            "(`$recipient` = ? AND `$isDeletedByRecipient` = 'N' OR " .
            "`$sender` = ? AND `$isDeletedBySender` = 'N')";
        
        $message = $this->db->queryObjects($q, Message::class, 'siii',
            $encryptionKey,
            $messageID,
            $participantID,
            $participantID);
        
        if (count($message) > 0) {
            return $message[0];
        }
        
        return null;
    }
    
    public function findMessages(string $encryptionKey, array $filters, int $offset = 0, int $limit = 50) : array
    {
        if (!isset($encryptionKey) || strlen($encryptionKey) <= 0) {
            throw new InvalidArgumentException("Encryption key is required for retrieving messages.");
        }

        $params = array();
        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        
        // Add key to body column selecting function
        $body = Message::FIELD_BODY;
        str_replace("`$body`", "AES_DECRYPT(`$body`, ?)", $columnList);
        $params[] = $encryptionKey;
        
        $mail = $this->getTableName();
        $sender = Message::FIELD_SENDER;
        $isDeletedBySender = Message::FIELD_IS_DELETED_BY_SENDER;
        $title = Message::FIELD_TITLE;
        $recipient = Message::FIELD_RECIPIENT;
        $isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $dateColumn = Message::FIELD_DATE;
        
        $q = "SELECT `$columnList` FROM `$mail` WHERE 1";

        // Extract query parameters to local variables
        list(
            'senderID' => $senderID,
            'keyword' => $keyword,
            'recipientID' => $recipientID,
            'date' => $date,
        ) = $filters;
        
        if (isset($senderID) && $senderID > 0) {
            $q .= " AND `$sender` = ? AND `$isDeletedBySender` = 'N'";
            $params[] = $senderID;
        }
        
        if (isset($keyword) && strlen($keyword) > 0) {
            $keywordWithWildcard = "%$keyword%";
            
            $q .= " AND (`$title` LIKE ? OR AES_DECRYPT(`$body`, ?) LIKE ?)";
            $params[] = $keywordWithWildcard;
            $params[] = $encryptionKey;
            $params[] = $keywordWithWildcard;
        }
        
        if (isset($recipientID) && $recipientID > 0) {
            $q .= " AND `$recipient` = ? AND `$isDeletedByRecipient` = 'N'";
            $params[] = $recipientID;
        }
        
        if (isset($date) && is_a($date, DateTime::class)) {
            $q.= " AND DATE(`$dateColumn`) = ?";
            $params[] = Util::dateToString($date);
        }
        
        $q .= " ORDER BY `$date` DESC LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
        
        return $this->db->queryObjectsWithParams($q, Message::class, $params);
    }
    
    private function setBooleanFlag(int $mailID, string $fieldName) : bool
    {
        $mail = $this->getTableName();
        $id = Message::FIELD_ID;
        $q = "UPDATE `$mail` SET `$fieldName` = 'Y' WHERE `$id` = ? LIMIT 1";
        return $this->db->execute($q, 'i', $mailID) > 0;
    }

    public function markRead(int $mailID) : bool
    {
        return $this->setBooleanFlag($mailID, Message::FIELD_IS_READ);
    }

    public function markDeletedByRecipient(int $mailID) : bool
    {
        return $this->setBooleanFlag($mailID, Message::FIELD_IS_DELETED_BY_RECIPIENT);
    }

    public function markDeletedBySender(int $mailID) : bool
    {
        return $this->setBooleanFlag($mailID, Message::FIELD_IS_DELETED_BY_SENDER);
    }
    
    public function insert(string $encryptionKey, Message $data) : int
    {
        $mail = $this->getTableName();
        $messageArray = $data->toArray();
        $insertColumns = array_keys($messageArray);
        $columnList = $this->toColumnList($insertColumns);

        // Body text should be encrypted; modify placeholder
        $placeholders = array_fill(0, count($columnList), '?');
        $placeholders[5] = 'AES_ENCRYPT(?, ?)';
        $placeholderList = implode(', ', $placeholders);
        
        $q = "INSERT INTO `$mail`(`$columnList`) VALUES ($placeholderList)";
        $values = array_values($messageArray);
        
        // Insert key to values after field 'body' (index 5)
        array_splice($values, 6, 0, $encryptionKey);
        
        // Type string is also extended with an extra 's' for the key
        return $this->db->execute($q, 'iiisssssssi', ...$values);
    }
}
