<?php
class Executor {
    private static $connection = null;

    private static function getConnection() {
        if (self::$connection === null) {
            require_once __DIR__ . "/../config/database.php";
            self::$connection = new mysqli($db_host, $db_user, $db_pass, $db_name);
            if (self::$connection->connect_error) {
                die("Error de conexión: " . self::$connection->connect_error);
            }
            self::$connection->set_charset("utf8");
        }
        return self::$connection;
    }

    public static function doit($sql) {
        $connection = self::getConnection();
        $result = $connection->query($sql);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $connection->error);
        }
        return array($result, $connection->insert_id);
    }
}
?> 