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
        $stmt = $this->db->prepare('SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ? AND ativo = 1');
        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }

    public function listarTodos(): array
    {
        return $this->db->query('SELECT id, nome, email, perfil FROM usuarios WHERE ativo = 1 ORDER BY nome')->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nome, email, perfil FROM usuarios WHERE id = ? AND ativo = 1');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    // $dados: nome, email, senha (já em hash), perfil
    public function criar(array $dados): void
    {
        $stmt = $this->db->prepare('INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)');
        $stmt->execute($dados);
    }

    public function atualizar(int $id, array $dados): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id');
        $stmt->execute($dados + ['id' => $id]);
    }

    public function atualizarSenha(int $id, string $senhaHash): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
        $stmt->execute([$senhaHash, $id]);
    }

    public function inativar(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET ativo = 0 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
