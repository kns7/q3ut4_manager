<?php

namespace App\Controller;

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

    public function getConfigKey($key)
    {
        return $this->config->$key;
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

    /**
     * Set Dark Theme or Light Theme (1: Dark, 0: Light)
     * @param $status
     * @return mixed
     */
    public function setDarkmode($status)
    {
        $_SESSION['darkmode'] = $status;

        return $status;
    }
}