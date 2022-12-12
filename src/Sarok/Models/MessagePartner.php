<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Lists senders/recipients and the number of messages they have sent to/received from a user.
 */
class MessagePartner
{
    private int $sender   = 0;
    private int $messages = 0;

    public function __construct(int $sender, int $messages)
    {
        $this->sender = $sender;
        $this->messages = $messages;
    }

    public function getSender() : int
    {
        return $this->sender;
    }

    public function getMessages() : int
    {
        return $this->messages;
    }
}
