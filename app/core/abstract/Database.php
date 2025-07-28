<?php

namespace App\Core\Abstract;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            // Utilise getenv() pour compatibilité Docker et variables d'environnement
            $driver = getenv('DB_DRIVER') ?: 'pgsql';
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'pgdbDaf';
            $port = getenv('DB_PORT') ?: 34111;
            $user = getenv('DB_USER') ?: 'pguserDaf';
            $pass = getenv('DB_PASSWORD') ?: 'pgpassword';

            $dsn = "$driver:host=$host;port=$port;dbname=$dbname;sslmode=require";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }

        return self::$pdo;
    
    }
}
