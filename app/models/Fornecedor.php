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
        return $this->db->query('SELECT * FROM fornecedores WHERE ativo = 1 ORDER BY nome')->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM fornecedores WHERE id = ? AND ativo = 1');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function buscarPorCnpj(string $cnpj): ?array
    {
        $stmt = $this->db->prepare('SELECT id FROM fornecedores WHERE cnpj = ? AND ativo = 1');
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

    public function inativar(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE fornecedores SET ativo = 0 WHERE id = ? AND ativo = 1');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
