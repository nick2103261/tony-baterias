<?php

class Categoria
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function listarTodas(): array
    {
        return $this->db->query('SELECT id, nome FROM categorias ORDER BY nome')->fetchAll();
    }

    public function criar(string $nome): void
    {
        $stmt = $this->db->prepare('INSERT INTO categorias (nome) VALUES (?)');
        $stmt->execute([$nome]);
    }

    public function existe(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM categorias WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetchColumn() !== false;
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM categorias WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
