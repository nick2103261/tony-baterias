<?php

class BancoDados
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {

                if (APP_ENV === 'development') {
                    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
                }
                die('Não foi possível conectar ao banco de dados. Tente novamente mais tarde.');
            }
        }

        return self::$instance;
    }

    private function __clone()
    {
    }
}
