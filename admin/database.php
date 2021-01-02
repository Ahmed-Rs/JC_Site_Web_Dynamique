<?php

class Database {                                // Connexion à la base de données
    private static $dbHost = "localhost";
    private static $dbName = "burger_code";
    private static $dbUser = "root";
    private static $dbUserPassword = "root";

    private static $connection = null;

    public static function connect() {         // Le 'public' permet d'utiliser la fct en dehors de son lieu de définition
        try {
            self::$connection = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName . ";charset=utf8", self::$dbUser, self::$dbUserPassword);        
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return self::$connection;
    }

    public static function disconnect () {
        $connection = null;
    }
}





?>
    
