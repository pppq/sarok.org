<?php namespace Sarok\Models;

class MessagePartner
{
    private int $sender = 0;
    private int $messages = 0;

    public function getSender() : int
    {
        return $this->sender;
    }

    public function getMessages() : int
    {
        return $this->messages;
    }
}
