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
        $stmt = $this->db->prepare('SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }

    public function listarTodos(): array
    {
        return $this->db->query('SELECT id, nome, email, perfil FROM usuarios ORDER BY nome')->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nome, email, perfil FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function contarAdmins(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'admin'")->fetchColumn();
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

    // Usuário que já fez pedido ou movimentou estoque não pode ser apagado (ON DELETE RESTRICT): o erro sobe para o controller
    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
