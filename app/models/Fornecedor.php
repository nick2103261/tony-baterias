<?php

class Fornecedor
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function listarTodos(): array
    {
        return $this->db->query('SELECT * FROM fornecedores ORDER BY nome')->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM fornecedores WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function buscarPorCnpj(string $cnpj): ?array
    {
        $stmt = $this->db->prepare('SELECT id FROM fornecedores WHERE cnpj = ?');
        $stmt->execute([$cnpj]);

        return $stmt->fetch() ?: null;
    }

    // $dados: nome, contato, cnpj
    public function criar(array $dados): void
    {
        $stmt = $this->db->prepare('INSERT INTO fornecedores (nome, contato, cnpj) VALUES (:nome, :contato, :cnpj)');
        $stmt->execute($dados);
    }

    public function atualizar(int $id, array $dados): void
    {
        $stmt = $this->db->prepare('UPDATE fornecedores SET nome = :nome, contato = :contato, cnpj = :cnpj WHERE id = :id');
        $stmt->execute($dados + ['id' => $id]);
    }

    // O histórico de estoque continua, só fica sem fornecedor (ON DELETE SET NULL)
    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM fornecedores WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
