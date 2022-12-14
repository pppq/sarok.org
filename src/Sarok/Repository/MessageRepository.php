<?php namespace Sarok\Repository;

use Sarok\Util;
use Sarok\DB;
use Sarok\Repository\AbstractRepository;
use Sarok\Models\MessagePartner;
use Sarok\Models\Message;
use InvalidArgumentException;
use DateTime;

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
    
    public function getUnreadMessage(int $recipientID, bool $latestOrEarliest) : ?Message
    {
        $selectColumns = array(
            Message::FIELD_ID,
            Message::FIELD_SENDER,
            Message::FIELD_DATE,
            Message::FIELD_TITLE,
        );

        $selectColumnList = $this->toColumnList($selectColumns);
        $t_mail = $this->getTableName();
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isRead = Message::FIELD_IS_READ;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $c_date = Message::FIELD_DATE;
        $order = $latestOrEarliest ? 'ASC' : 'DESC';
        
        $q = "SELECT `$selectColumnList` FROM `$t_mail` " .
            "WHERE `$c_recipient` = ? AND `$c_isRead` = 'N' AND `$c_isDeletedByRecipient` = 'N' " .
            "ORDER BY `$c_date` $order LIMIT 1";
        
        $message = $this->db->queryObjects($q, Message::class, 'i', $recipientID);
        if (count($message) > 0) {
            return $message[0];
        }
        
        return null;
    }
    
    public function getUnreadMessageCount(int $recipientID) : int
    {
        $c_id = Message::FIELD_ID;
        $t_mail = $this->getTableName();
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isRead = Message::FIELD_IS_READ;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;

        $q = "SELECT COUNT(`$c_id`) FROM `$t_mail` " .
            "WHERE `$c_recipient` = ? AND `$c_isRead` = 'N' AND `$c_isDeletedByRecipient` = 'N'";
        
        return $this->db->queryInt($q, 0, 'i', $recipientID);
    }
    
    public function getPartners(int $recipientID) : array
    {
        $c_sender = Message::FIELD_SENDER;
        $c_id = Message::FIELD_ID;
        $t_mail = $this->getTableName();
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        
        $q = "SELECT `$c_sender` AS `sender`, COUNT(`$c_id`) AS `messages` FROM `$t_mail` " .
            "WHERE `$c_recipient` = ? AND `$c_isDeletedByRecipient` = 'N' GROUP BY `$sender`";
        
        $result = $this->db->queryObjects($q, MessagePartner::class, 'i', $recipientID);
    }

    public function getParticipants(int $mailID, /*out*/ int &$senderID, /*out*/ int &$recipientID) : bool
    {
        $c_sender = Message::FIELD_SENDER;
        $c_recipient = Message::FIELD_RECIPIENT;
        $t_mail = $this->getTableName();
        $c_id = Message::FIELD_ID;
        
        $q = "SELECT `$c_sender`, `$c_recipient` FROM `$t_mail` WHERE `$c_id` = ? LIMIT 1";
        
        $result = $this->db->query($q, 'i', $mailID);
        if ($participants = $result->fetch_row()) {
            $senderID = (int) $participants[0];
            $recipientID = (int) $participants[1];
            return true;
        }
        
        return false;
    }
    
    public function getMessage(string $encryptionKey, int $messageID, int $participantID) : ?Message
    {
        if (!isset($encryptionKey) || strlen($encryptionKey) <= 0) {
            throw new InvalidArgumentException("Encryption key is required for retrieving messages.");
        }
        
        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        // Add key to body column selecting function
        $c_body = Message::FIELD_BODY;
        str_replace("`$c_body`", "AES_DECRYPT(`$c_body`, ?)", $columnList);
        
        $t_mail = $this->getTableName();
        $c_id = Message::FIELD_ID;
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $c_sender = Message::FIELD_SENDER;
        $c_isDeletedBySender = Message::FIELD_IS_DELETED_BY_SENDER;
        
        // Query will report "not found" if the participant is not the sender or the recipient of the message
        $q = "SELECT `$columnList` FROM `$t_mail` " .
            "WHERE `$c_id` = ? AND " .
            "(`$c_recipient` = ? AND `$c_isDeletedByRecipient` = 'N' OR `$c_sender` = ? AND `$c_isDeletedBySender` = 'N')";
        
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

        $selectColumns = $this->getColumnNames();
        $columnList = $this->toColumnList($selectColumns);
        // Add key to body column selecting function
        $c_body = Message::FIELD_BODY;
        str_replace("`$c_body`", "AES_DECRYPT(`$c_body`, ?)", $columnList);

        $t_mail = $this->getTableName();
        $c_sender = Message::FIELD_SENDER;
        $c_isDeletedBySender = Message::FIELD_IS_DELETED_BY_SENDER;
        $c_title = Message::FIELD_TITLE;
        $c_recipient = Message::FIELD_RECIPIENT;
        $c_isDeletedByRecipient = Message::FIELD_IS_DELETED_BY_RECIPIENT;
        $c_date = Message::FIELD_DATE;
        
        $q = "SELECT `$columnList` FROM `$t_mail` WHERE 1";

        // Extract query parameters to local variables
        list(
            'senderID' => $senderID,
            'keyword' => $keyword,
            'recipientID' => $recipientID,
            'date' => $date,
        ) = $filters;
        
        $params = array();
        $params[] = $encryptionKey;
        
        if (isset($senderID) && $senderID > 0) {
            $q .= " AND `$c_sender` = ? AND `$c_isDeletedBySender` = 'N'";
            $params[] = $senderID;
        }
        
        if (isset($keyword) && strlen($keyword) > 0) {
            $keywordWithWildcard = "%$keyword%";
            
            $q .= " AND (`$c_title` LIKE ? OR AES_DECRYPT(`$c_body`, ?) LIKE ?)";
            $params[] = $keywordWithWildcard;
            $params[] = $encryptionKey;
            $params[] = $keywordWithWildcard;
        }
        
        if (isset($recipientID) && $recipientID > 0) {
            $q .= " AND `$c_recipient` = ? AND `$c_isDeletedByRecipient` = 'N'";
            $params[] = $recipientID;
        }
        
        if (isset($c_date) && is_a($c_date, DateTime::class)) {
            $q.= " AND DATE(`$c_date`) = ?";
            $params[] = Util::dateToString($c_date);
        }
        
        $q .= " ORDER BY `$c_date` DESC LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
        
        return $this->db->queryObjectsWithParams($q, Message::class, $params);
    }
    
    private function setBooleanFlag(string $column, int $mailID) : bool
    {
        $t_mail = $this->getTableName();
        $c_id = Message::FIELD_ID;
        
        $q = "UPDATE `$t_mail` SET `$column` = 'Y' WHERE `$c_id` = ? LIMIT 1";
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
        $t_mail = $this->getTableName();
        $messageArray = $message->toArray();
        $insertColumns = array_keys($messageArray);
        $columnList = $this->toColumnList($insertColumns);

        // Body text should be encrypted; modify placeholder
        $placeholders = array_fill(0, count($columnList), '?');
        $placeholders[5] = 'AES_ENCRYPT(?, ?)';
        $placeholderList = implode(', ', $placeholders);
        
        $q = "INSERT INTO `$t_mail` (`$columnList`) VALUES ($placeholderList)";
        $values = array_values($messageArray);
        
        // Insert key to values after field 'body' (index 5)
        array_splice($values, 6, 0, $encryptionKey);
        
        // Type string is also extended with an extra 's' for the key
        return $this->db->execute($q, 'iiisssssssi', ...$values);
    }
}
