<?php

// Limita tentativas de login erradas: 5 por e-mail (a partir do mesmo IP) ou 20 por IP, a cada 15 minutos.
// Se a tabela ainda não existir (script de migração não rodou), o login continua funcionando, só sem o limite.
class TentativaLogin
{
    private const MAXIMO_POR_EMAIL = 5;
    private const MAXIMO_POR_IP    = 20;
    private const JANELA_MINUTOS   = 15;

    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function bloqueado(string $email, string $ip): bool
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT COALESCE(SUM(email = ?), 0) AS por_email, COUNT(*) AS por_ip
                 FROM tentativas_login
                 WHERE ip = ? AND criado_em > (NOW() - INTERVAL ' . self::JANELA_MINUTOS . ' MINUTE)'
            );
            $stmt->execute([$this->normalizar($email), $ip]);
            $contagem = $stmt->fetch();

            return (int) $contagem['por_email'] >= self::MAXIMO_POR_EMAIL || (int) $contagem['por_ip'] >= self::MAXIMO_POR_IP;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function registrarFalha(string $email, string $ip): void
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO tentativas_login (email, ip) VALUES (?, ?)');
            $stmt->execute([$this->normalizar($email), $ip]);

            $this->db->exec('DELETE FROM tentativas_login WHERE criado_em < (NOW() - INTERVAL 1 DAY)');
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function limpar(string $email, string $ip): void
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM tentativas_login WHERE email = ? AND ip = ?');
            $stmt->execute([$this->normalizar($email), $ip]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    private function normalizar(string $email): string
    {
        return mb_substr(mb_strtolower($email), 0, 150);
    }
}
