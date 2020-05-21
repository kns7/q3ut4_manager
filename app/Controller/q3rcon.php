<?php
/*
 * File: q3rcon.php
 * Description: Quake 3 Engine rcon class for PHP
 * Author: Sean Cline (MajinCline)
 * License: You may do whatever you please with this file and any code it contains. It is to be considered public domain.
 */

namespace App\Controller;

class q3rcon {

    // Some vars that will be used to hold players and cvars
    public $playerlist;
    public $cvarlist;
    public $last_server_status;

    // Some vars needed for rcon info
    private $password;

    // Some vars to store what server we will connect to
    private $address;
    private $port;
    private $socket_connection;

    // Misc. other vars
    private $last_socket_err_num;
    private $last_socket_err_str;

    // Constructor: takes the IP address (or hostname), port, and rcon password of
    // the server and opens a connection.
    public function __construct($serv_address, $serv_port, $serv_password, $timeout=30) {
        $this->address = $serv_address;
        $this->port = intval($serv_port);
        $this->password = $serv_password;

        $this->playerlist = array();
        $this->cvarlist = array();
        $this->last_server_status = "";

        $this->last_socket_err_num = -1;
        $this->last_socket_err_str = "";

        // Open up the connection wih the given address and port
        $this->socket_connection = fsockopen("udp://" . $this->address, $this->port, $this->last_socket_err_num, $this->last_socket_err_str, $timeout);
        if (!$this->socket_connection) {
            die("Could not connect with given ip:port\n<br>errno: $this->last_socket_err_num\n<br>errstr: $this->last_socket_err_str");
        }

    }

    // Precondition: A socket has been opened without error by the constructor
    // Postcondidtion: The given command will be sent and a response will be
    //      recoverable from the function get_response()
    public function send_command($cmd) {
        fwrite($this->socket_connection, str_repeat(chr(255), 4) . "rcon " . $this->password . " " . $cmd . "\n");
    }

    private function send_status(){
        fwrite($this->socket_connection, str_repeat(chr(255), 4) . "getstatus\n");
    }

    // Get the server's response to our previous query.
    // Precondition: A command should have already been sent with send_command($cmd).
    // Postcondidtion: The server's response string will be returned.
    public function get_response() {
        stream_set_timeout($this->socket_connection, 0, 500000);
        $buffer = "";
        while ($buff = fread($this->socket_connection, 9999)) {
            list($header, $contents) = explode("\n", $buff, 2); // Trim off the header of each packet we receive.
            $buffer .= $contents;
        }
        return $buffer;
    }

    public function get_players() { // Take the info from a "/rcon status" command and parse it for an array of player names.
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

    // Get the status and parse it into a player and cvar array.
    // Returns true on success, false on failure.
    public function update_status() {
        // Get the status string from the server
        $this->send_status();
        $this->last_server_status = $this->get_response();

        if($this->last_server_status == "") {
            return false;
        }

        // Break the status string into it's "paragraphs"
        $data = explode("\n", $this->last_server_status, 2);
        $data2 = explode("\n", $data[1], 2);
        $cvars = $data2[0];
        $players = $data2[1];
        // Load the cvars into an array for fast access later.
        $cvararray = array();
        $cvarexplode = explode("\\", $cvars);
        for($i = 1; $i<count($cvarexplode); $i++) { // start at 1 because the cvar string starts with a \
            $cvararray[$cvarexplode[$i]] = $this->strip_colors($cvarexplode[++$i]); // Load each the array into a cvarname=>cvarvalue array
        }

        // Load the players into an array.
        $playerarray = array();
        $playerexplode = explode("\n", $players);
        for($i = 0; $i<count($playerexplode); $i++) {
            $playerline = trim($playerexplode[$i]);
            if($playerline != "") { // Make sure this isn't an empty line.
                list($score, $ping, $name) = explode(" ", $playerline, 3); // Break the player into attributes
                $name = substr($name, 1, strlen($name) - 2);
                // Load each the array into a playernumber=>array('name'=>playername, 'strippedname'=>strippedplayername, 'ping'=>playerping, 'score'=>playerscore) array
                $playerarray[$i] = array('name' => $name, 'strippedname' => $this->strip_colors($name), 'ping' => intval($ping), 'score' => intval($score));
            }
        }

        $this->cvarlist = $cvararray;
        $this->playerlist = $playerarray;

        return true;
    }

    public function getCvarList()
    {
        return $this->cvarlist;
    }

    // Get the number of players in the server.
    public function get_num_players() {
        if (is_array($this->playerlist)) {
            return count($this->playerlist);
        } else {
            return 0;
        }
    }

    // Return the Host:Port of this server
    public function get_address() {
        return $this->address . ":" . $this->port;
    }

    // Get a cvar by name, empty string for not found.
    public function get_cvar($name) {
        if(array_key_exists($name, $this->cvarlist)) {
            return $this->cvarlist[$name];
        } else {
            return "";
        }
    }

    public function strip_quotes($str1) {
        return preg_replace("/\"/","", $str1);
    }
    public function strip_space($str3) {
        return preg_replace("/\ /", "", $str3);
    }

    public function strip_colon($str2) {
        return preg_replace("/\:/"," ", $str2);
    }
    // Remove ^# colors
    public function strip_colors($str) {
        return preg_replace("/\^./","", $str);

    }

    public function dump_guid($clientid) {
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

    public function close() {
        fclose($this->socket_connection);
    }
}