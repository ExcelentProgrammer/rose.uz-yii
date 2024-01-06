<?php

namespace app\models;

/**
 * This is the model class for Telegram Message.
 *
 * @property integer $message_id
 * @property integer $date
 * @property string $text
 * @property array $contact
 * @property integer $type
 *
 * @property TelegramChat $chat
 * @property TelegramSender $from
 */

class TelegramMessage
{
    public $message_id;
    public $from;
    public $chat;
    public $date;
    public $text;
    public $contact;
    public $type;

    public $entities_type;
    public $entities_offset;
    public $entities_length;

    public function __construct($data)
    {
        if (!empty($data)) {
            $this->message_id = $data['message_id'];
            $this->from = new TelegramSender($data['from']);
            $this->chat = new TelegramChat($data['chat']);
            $this->date = $data['date'];
            if (isset($data['text'])) {
                $this->type = TelegramBot::RESPONSE_MESSAGE;
                $this->text = trim($data['text']);
            }

            if (isset($data['contact']) && !empty($data['contact'])) {
                $this->type = TelegramBot::RESPONSE_CONTACT;
                $this->contact = $data['contact'];
            }
            $this->entities_type = $data['entities']['type'];
            $this->entities_offset = $data['entities']['offset'];
            $this->entities_length = $data['entities']['length'];
        }
    }

}