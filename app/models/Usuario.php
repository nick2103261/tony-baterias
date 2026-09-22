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

public function buscarPorId(int $id): ?array
{

    $sql = 'SELECT id, nome, email, perfil, ativo
            FROM usuarios
            WHERE id = :id AND ativo = 1';

    $stmt = $this->db->prepare($sql);
    $stmt->bindvalue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $usuarios = $stmt->fetch();
    return $usuario ?: null;
}

public function criar(array $dados): int 
{
    $sql = 'INSERT INTO usuarios (nome, email, senha, perfil)
            VALUES (:nome, :email, :senha, :perfil)';
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':nome' => $dados['nome'],
        ':email' => $dados['email'],
        ':senha' => $dados['senha']
        ':perfil' => $dados['perfil']
    ])
    return (int) $lastInsertId();
}
