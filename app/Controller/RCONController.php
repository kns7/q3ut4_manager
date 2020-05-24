<?php

namespace App\Controller;

class RCONController extends Controller
{
    public $last_server_status;
    private $socket_connection;
    private $last_socket_err_num;
    private $last_socket_err_str;
    public $cvars;
    private $rconCmd;

    public function __construct($app)
    {
        parent::__construct($app);
        $port = (isset($this->config->rconport) && !empty($this->config->rconport))? $this->config->rconport:27960;
        $host = (isset($this->config->rconhost) and !empty($this->config->rconhost))? $this->config->rconhost:"127.0.0.1";
        // Open up the connection wih the given address and port
        $this->socket_connection = fsockopen("udp://" . $host, $port, $this->last_socket_err_num, $this->last_socket_err_str, 30);
        if (!$this->socket_connection) {
            die("Could not connect with given ip:port\n<br>errno: $this->last_socket_err_num\n<br>errstr: $this->last_socket_err_str");
        }
    }

    /**
     * @param \Maps $map
     * @return string|null
     */
    public function setMap(\Maps $map)
    {
        $this->rconCmd = "map ". $map->getFile();
        $this->sendMessage("*** Changement de carte *** | ".$map->getName());
        sleep(3);
        $this->send_command($this->rconCmd);
    }

    /**
     * @param $timelimit
     */
    public function setTimelimit($timelimit)
    {
        $this->rconCmd = "set timelimit $timelimit";
        $this->sendMessage("*** Changement de durée de partie *** | Nouvelle durée: ".$timelimit." min");
        sleep(2);
        $this->send_command($this->rconCmd);
    }

    /**
     * @param $roundtime
     */
    public function setRoundTime($roundtime)
    {
        $this->rconCmd = "set g_roundtime $roundtime";
        $this->sendMessage("*** Changement de durée d'un round *** | Nouvelle durée: ".$roundtime." min");
        sleep(2);
        $this->send_command($this->rconCmd);
    }


    /**
     * @param \Gametypes $type
     * @return string|null
     */
    public function setGametype(\Gametypes $type)
    {
        $this->rconCmd = "set g_gametype ". $type->getCode();
        $this->sendMessage("*** Changement de mode de jeu *** | Prochain mode: ".$type->getName());
        sleep(2);
        $this->send_command($this->rconCmd);
    }

    /**
     * Apply parameters to server and return the actions done through an array of booleans
     * @param array $post
     * @return array
     */
    public function saveParams(array $post)
    {
        $return = [
            'gametype' => false,
            'timelimit' => false,
            'roundtime' => false,
            'map' => false,
            'reload' => false
        ];

        if(isset($post['gametype']) && !empty($post['gametype'])){
            try{
                $this->setGametype($this->app->Ctrl->Gametypes->get($post['gametype']));
                $return['gametype'] = true;
            }catch(\Exception $e){
                $return['map'] = $e->getMessage();
            }
        }
        if(isset($post['timelimit']) && !empty($post['timelimit'])){
            try {
                $this->setTimelimit($post['timelimit']);
                $return['gametype'] = true;
            }catch(\Exception $e){
                $return['map'] = $e->getMessage();
            }
        }
        if(isset($post['roundtime']) && !empty($post['roundtime'])){
            try{
                $this->setRoundTime($post['roundtime']);
                $return['roundtime'] = true;
            }catch(\Exception $e){
                $return['map'] = $e->getMessage();
            }
        }
        if(isset($post['map']) && !empty($post['map'])){
            try{
                $this->setMap($this->app->Ctrl->Maps->get($post['map']));
                $return['map'] = true;
            }catch(\Exception $e){
                $return['map'] = $e->getMessage();
            }

        }
        if(isset($post['reload']) && isset($post['reload']) === true){
            try {
                $this->serverReload();
                $return['reload'] = true;
            }catch(\Exception $e){
                $return['map'] = $e->getMessage();
            }
        }
        return $return;
    }


    /**
     * Reload Server
     */
    public function serverReload()
    {
        $this->sendMessage("*** Rechargement du Serveur, Redémarrage de la partie ***");
        sleep(1);
        $this->send_command("reload");
    }


    public function getStatus()
    {
        return (object)[
            'map' => $this->getMap(),
            'gametype' => $this->getGametype(),
            'cvars' => $this->cvars
        ];
    }

    /**
     * Get the actual Map over RCON command
     * @return Maps
     */
    public function getMap()
    {
        if(!isset($this->cvars['mapname'])){
            $this->getCvarList();
        }
        return $this->app->Ctrl->Maps->getByFile($this->cvars['mapname']);
    }

    public function sendMessage($msg)
    {
        $this->send_command($msg);
    }


    /**
     *
     * @return mixed
     */
    public function getGametype()
    {
        if(!isset($this->cvars['g_gametype'])){
            $this->getCvarList();
        }
        return $this->app->Ctrl->Gametypes->getByCode($this->cvars['g_gametype']);
    }

    /**
     * Get the list of all Players connected to the Server
     * @return array|bool
     */
    public function getPlayers()
    {
        $this->send_command("status");
        $status = $this->get_response();
        //echo $status;
        if (!$status || trim($status) == "Bad rconpassword.") {
            return false;
        }

        $playerlines = explode("\n", $status); // Break the status into indvidual lines
        $players = array();
        for($i = 3; $i < count($playerlines); $i++) { // Create a new array with player's status in an array
            $line = trim(preg_replace('/\s\s+/', ' ', $playerlines[$i]));
            $player_status = explode(" ", $line); // Split the player status into an array "num score ping name lastmsg address qport rate"
            $status_size = count($player_status);

            if ($status_size < 8) { // Skip this line if it doesnt have enough fields.
                continue;
            }

            // It is possible for names to have spaces. There are ordinarily 9 pieces of info in the array, more mean there are spaces
            $num_name_chunks = $status_size - 8;
            $name = $player_status[3];
            for($j = 0; $j < $num_name_chunks; $j++) { // Concatenate all of the name chunks that exist in a name with spaces
                $name .= " " . $player_status[4 + $j];
            }

            $name = substr($name, 0, strlen($name) - 2); // Remove the "^7" that rcon puts at the end of the name
            $stripped_name = $this->strip_colors($name); // Rename colors

            if ($name == "") { // Make sure the name is a real person
                $name = "UnnamedPlayer";
            }

            $player['num'] = ($player_status[0]);
            $player['score'] = ($player_status[1]);
            $player['ping'] = ($player_status[2]);

            $player['name'] = $name;
            $player['stripped_name'] = $stripped_name;

            $player['lastmsg'] = ($player_status[4+$num_name_chunks]);
            $player['address'] = $player_status[5+$num_name_chunks];
            $player['qport'] = ($player_status[6+$num_name_chunks]);
            $player['rate'] = ($player_status[7+$num_name_chunks]);
            $player['guid'] = $this->dump_guid($player_status[0]);
            $players[] = $player;
        }
        return $players;
    }

    public function getCvarList()
    {
        $this->send_command("serverinfo");
        $response = trim(str_replace("Server info settings:","",$this->get_response()));
        $this->cvars = array();
        $cvarexplode = explode("\n", $response);
        foreach($cvarexplode as $c) {
            $cvar = explode(" ",$c);
            $value = "";
            for($i = 1;$i<count($cvar);$i++){
                if($cvar[$i] != ""){ $value .= " ".$cvar[$i]; }
            }
            $this->cvars[$cvar[0]] = trim($value); // Load each the array into a cvarname=>cvarvalue array
        }
        return $this->cvars;
    }

    private function send_command($cmd) {
        fwrite($this->socket_connection, str_repeat(chr(255), 4) . "rcon " . $this->config->rconpassword . " " . $cmd . "\n");
    }

    private function get_response() {
        stream_set_timeout($this->socket_connection, 0, 500000);
        $buffer = "";
        while ($buff = fread($this->socket_connection, 9999)) {
            list($header, $contents) = explode("\n", $buff, 2); // Trim off the header of each packet we receive.
            $buffer .= $contents;
        }
        return $buffer;
    }

    private function strip_quotes($str1) {
        return preg_replace("/\"/","", $str1);
    }
    private function strip_space($str3) {
        return preg_replace("/\ /", "", $str3);
    }

    private function strip_colon($str2) {
        return preg_replace("/\:/"," ", $str2);
    }
    // Remove ^# colors
    private function strip_colors($str) {
        return preg_replace("/\^./","", $str);
    }

    private function dump_guid($clientid) {
        $this->send_command("dumpuser ". $clientid);
        $duinfo = $this->get_response();
        if (!$duinfo) {
            return false;
        }
        $duinfo = $this->strip_colors($duinfo);
        $duinfo1 = explode("cl_guid", $duinfo);
        $duinfoguid = $duinfo1[1];
        $duinfoguid2 = explode("\n", $duinfoguid);
        $duinfoguid3 = $duinfoguid2[0];
        $duinfoguid = $this->strip_space($duinfoguid3);

        return $duinfoguid;
    }

    private function close() {
        fclose($this->socket_connection);
    }
}