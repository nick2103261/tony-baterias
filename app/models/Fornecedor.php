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
        $stmt = $this->db->prepare('SELECT * FROM fornecedores WHERE id = :id AND ativo = 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $fornecedor = $stmt->fetch();
        return $fornecedor ?: null;
    }

    public function criar(array $dados): int
    {
        $sql = 'INSERT INTO fornecedores (nome, contato, cnpj) VALUES (:nome, :contato, :cnpj)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'    => $dados['nome'],
            ':contato' => $dados['contato'],
            ':cnpj'    => $dados['cnpj'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): void
    {
        $sql = 'UPDATE fornecedores
                SET nome = :nome, contato = :contato, cnpj = :cnpj
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'    => $dados['nome'],
            ':contato' => $dados['contato'],
            ':cnpj'    => $dados['cnpj'],
            ':id'      => $id,
        ]);
    }

    public function inativar(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE fornecedores SET ativo = 0 WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
