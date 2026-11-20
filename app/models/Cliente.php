<?php

class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function listarTodos(): array
    {
        return $this->db->query('SELECT * FROM clientes ORDER BY nome')->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    // $dados: nome, cpf, cnpj, telefone, email, endereco
    public function criar(array $dados): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clientes (nome, cpf, cnpj, telefone, email, endereco)
             VALUES (:nome, :cpf, :cnpj, :telefone, :email, :endereco)'
        );
        $stmt->execute($dados);
    }

    public function atualizar(int $id, array $dados): void
    {
        $stmt = $this->db->prepare(
            'UPDATE clientes
             SET nome = :nome, cpf = :cpf, cnpj = :cnpj, telefone = :telefone, email = :email, endereco = :endereco
             WHERE id = :id'
        );
        $stmt->execute($dados + ['id' => $id]);
    }

    public function historicoCompras(int $id): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, valor_total, criado_em
             FROM pedidos
             WHERE cliente_id = ? AND status = 'concluido'
             ORDER BY criado_em DESC"
        );
        $stmt->execute([$id]);

        return $stmt->fetchAll();
    }

    // Os pedidos do cliente continuam no sistema, só ficam sem cliente (ON DELETE SET NULL)
    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM clientes WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
