<?php

function getConnection() {
    $host = "localhost";
    $dbname = "estore";
    $username = "root";
    $password = "";

    try {
        $conn = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $username,
            $password
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;

    } catch(PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}
?>
