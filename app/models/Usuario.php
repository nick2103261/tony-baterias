<?php

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function buscarPorEmail(string $email): ?array
    {
        $sql = 'SELECT id, nome, email, senha, perfil
                FROM usuarios
                WHERE email = :email AND ativo = 1
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }
}

public function listarTodos(): array
{
    $sql = 'SELECT id, nome, email, perfil, ativo
            FROM usuarios
            WHERE ativo = 1
            ORDER BY nome';
    
    return $this->db->query($sql)->fetchAll();
}
