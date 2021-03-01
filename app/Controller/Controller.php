<?php

namespace App\Controller;

use Longman\TelegramBot\Telegram;

class Controller{
    protected $app;
    protected $config;


    /**
     * Controller constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->config = (object)[];
        foreach (\ConfigQuery::create()->find() as $c) {
            $this->config->{$c->getKey()} = $c->getValue();
        }
    }

    /**
     * Setup the Configuration from DB
     * @return object
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function sendNotificationTelegram($text)
    {
        $apiToken = $this->config->telegramtoken;
        $botName = "urt12s_bot";

        $data = [
            'chat_id' => $this->config->telegramchannel,
            'text' => $text,
        ];

        return file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
    }
}