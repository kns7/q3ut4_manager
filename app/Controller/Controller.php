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
        foreach (\ConfigQuery::create()->orderByCategory()->find() as $c) {
            $key = explode("_",$c->getKey());
            if(!isset($this->config->{$key[0]})){ $this->config->{$key[0]} = (object)[]; }
            $this->config->{$key[0]}->{$key[1]} = $c->getValue();
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