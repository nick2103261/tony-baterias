<?php

class BancoDados
{
    private static ?PDO $conexao = null;

    public static function getConnection(): PDO
    {
        if (self::$conexao === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            try {
                self::$conexao = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die(APP_ENV === 'development'
                    ? 'Erro ao conectar ao banco de dados: ' . $e->getMessage()
                    : 'Não foi possível conectar ao banco de dados. Tente novamente mais tarde.');
            }
        }

        return self::$conexao;
    }
}
