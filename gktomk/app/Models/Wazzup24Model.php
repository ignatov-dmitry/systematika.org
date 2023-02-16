<?php


namespace GKTOMK\Models;

use GKTOMK\Models\WhatsappApi\WAZZUPAPI;

class Wazzup24Model
{
    private string $channelId = 'a7d9355f-4d4b-452e-ad7d-d1348f64ea5f';

    private string $chatType = 'whatsapp';

    private string $chatId = '375336540877';

    private string $text = 'Test message';

    /**
     * @param string $channelId
     */
    public function setChannelId(string $channelId): Wazzup24Model
    {
        $this->channelId = $channelId;
        return $this;
    }

    /**
     * @param string $chatType
     */
    public function setChatType(string $chatType): Wazzup24Model
    {
        $this->chatType = $chatType;
        return $this;
    }

    /**
     * @param string $chatId
     */
    public function setChatId(string $chatId): Wazzup24Model
    {
        $this->chatId = $chatId;
        return $this;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): Wazzup24Model
    {
        $this->text = $text;
        return $this;
    }



    public function sendMessage()
    {
        $data = array(
            'channelId' => $this->channelId,
            'chatType' => $this->chatType,
            'chatId' => $this->chatId,
            'text' => $this->text
        );

        WAZZUPAPI::sendMessage($data);
    }
}