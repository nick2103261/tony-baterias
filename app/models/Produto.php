<?php

class Produto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function listarTodos(): array
    {
        return $this->db->query(
            'SELECT p.*, c.nome AS categoria_nome
             FROM produtos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.ativo = 1
             ORDER BY p.nome'
        )->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM produtos WHERE id = ? AND ativo = 1');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function buscarPorSku(string $sku): ?array
    {
        $stmt = $this->db->prepare('SELECT id FROM produtos WHERE sku = ? AND ativo = 1');
        $stmt->execute([$sku]);

        return $stmt->fetch() ?: null;
    }

    // $dados: categoria_id, nome, descricao, marca, sku, preco, garantia_meses, estoque_minimo
    public function criar(array $dados, int $estoqueInicial, int $usuarioId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO produtos (categoria_id, nome, descricao, marca, sku, preco, garantia_meses, estoque_minimo)
             VALUES (:categoria_id, :nome, :descricao, :marca, :sku, :preco, :garantia_meses, :estoque_minimo)'
        );
        $stmt->execute($dados);

        if ($estoqueInicial > 0) {
            $movimentacao = new MovimentacaoEstoque();
            $movimentacao->registrar((int) $this->db->lastInsertId(), $usuarioId, 'entrada', $estoqueInicial, null, 'Estoque inicial do produto');
        }
    }

    public function atualizar(int $id, array $dados): void
    {
        $stmt = $this->db->prepare(
            'UPDATE produtos
             SET categoria_id = :categoria_id, nome = :nome, descricao = :descricao, marca = :marca, sku = :sku,
                 preco = :preco, garantia_meses = :garantia_meses, estoque_minimo = :estoque_minimo
             WHERE id = :id'
        );
        $stmt->execute($dados + ['id' => $id]);
    }

    public function inativar(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE produtos SET ativo = 0 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
