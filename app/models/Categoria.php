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

    public function criar(string $nome): int
    {
        $stmt = $this->db->prepare('INSERT INTO categorias (nome) VALUES (:nome)');
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function excluir(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM categorias WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
