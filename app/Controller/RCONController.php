<?php

namespace App\Controller;

class RCONController extends Controller
{

    protected $q3rcon;
    protected $rconCmd;

    public function __construct($app)
    {
        parent::__construct($app);
        $port = (isset($this->config->rconport) && !empty($this->config->rconport))? $this->config->rconport:27960;
        $host = (isset($this->config->rconhost) and !empty($this->config->rconhost))? $this->config->rconhost:"127.0.0.1";
        $this->q3rcon = new q3rcon($host,$port,$this->config->rconpassword);
    }

    /**
     * @param \Maps $map
     * @return string|null
     */
    public function setMap(\Maps $map)
    {
        $this->rconCmd = "map ". $map->getFile();
        $this->q3rcon->send_command($this->rconCmd);
    }


    /**
     * @param \Gametypes $type
     * @return string|null
     */
    public function setGametype(\Gametypes $type)
    {
        $this->rconCmd = "set g_gametype ". $type->getCode();
        $this->q3rcon->send_command($this->rconCmd);
    }


    /**
     * Get the actual Map over RCON command
     * @return Maps
     */
    public function getMap()
    {

    }



    /**
     * Get the actual Gametype over RCON command
     */
    public function getGametype()
    {

    }

    /**
     * Get the list of all Players connected to the Server
     */
    public function getPlayers()
    {
        return $this->q3rcon->get_players();
    }
}