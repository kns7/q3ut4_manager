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
}