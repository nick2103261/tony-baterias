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
        $sql = 'SELECT * FROM clientes WHERE ativo = 1 ORDER BY nome';

        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE id = :id AND ativo = 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $cliente = $stmt->fetch();
        return $cliente ?: null;
    }

    public function criar(array $dados): int
    {
        $sql = 'INSERT INTO clientes (nome, cpf, cnpj, telefone, email, endereco)
                VALUES (:nome, :cpf, :cnpj, :telefone, :email, :endereco)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'     => $dados['nome'],
            ':cpf'      => $dados['cpf'],
            ':cnpj'     => $dados['cnpj'],
            ':telefone' => $dados['telefone'],
            ':email'    => $dados['email'],
            ':endereco' => $dados['endereco'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): void
    {
        $sql = 'UPDATE clientes
                SET nome = :nome,
                    cpf = :cpf,
                    cnpj = :cnpj,
                    telefone = :telefone,
                    email = :email,
                    endereco = :endereco
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'     => $dados['nome'],
            ':cpf'      => $dados['cpf'],
            ':cnpj'     => $dados['cnpj'],
            ':telefone' => $dados['telefone'],
            ':email'    => $dados['email'],
            ':endereco' => $dados['endereco'],
            ':id'       => $id,
        ]);
    }

    public function historicoCompras(int $id): array
    {
        $sql = "SELECT id, valor_total, status, criado_em
                FROM pedidos
                WHERE cliente_id = :cliente_id AND status = 'concluido'
                ORDER BY criado_em DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cliente_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function inativar(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE clientes SET ativo = 0 WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
