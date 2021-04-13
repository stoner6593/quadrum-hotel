<?php

class Configuraciones {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /* recentlyOnline */
    function recentlyOnline($minutes) {
        $time = time() - ($minutes * 60);
        $query = $this->db->query("SELECT username FROM users WHERE timestamp > $time");
        $usersonline = "";
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $usersonline .= $row['username']. ", ";
        }
        $results = rtrim($usersonline, ", ");        
        return $results;
    }

    function getConfig($data ) {
        $output = $data;
        $sqlRow = $this->db->query("SELECT config_value FROM configuration where config_name= '$output'");
        $outputRow = $sqlRow->fetch_row();

        return $outputRow[0];
    }

    function getConfigRol($data, $rol_id){
        $output = $data;
        $sqlRow = $this->db->query("SELECT config_value_".$rol_id." FROM configuration where config_name= '$output'");
        $outputRow = $sqlRow->fetch_row();

        return $outputRow[0];
    }

}
