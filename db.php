<?php
if (!function_exists('connectDatabase')) {
    function connectDatabase() {
        $servername = "localhost"; 
        $username = "root"; 
        $password = ""; 
        $dbname = "mydb"; 

        // Maak verbinding met de MySQL-database via MySQLi
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Controleer of de verbinding is gelukt
        if ($conn->connect_error) {
            die(json_encode(array('error' => $conn->connect_error)));
        }

        return $conn;
    }
}
?>
