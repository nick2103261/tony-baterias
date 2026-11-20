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
             ORDER BY p.nome'
        )->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM produtos WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function buscarPorSku(string $sku): ?array
    {
        $stmt = $this->db->prepare('SELECT id FROM produtos WHERE sku = ?');
        $stmt->execute([$sku]);

        return $stmt->fetch() ?: null;
    }

    // $dados: categoria_id, nome, descricao, marca, sku, preco, garantia_meses, estoque_minimo
    // O produto e o estoque inicial são gravados juntos: ou entram os dois, ou nenhum.
    public function criar(array $dados, int $estoqueInicial, int $usuarioId): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO produtos (categoria_id, nome, descricao, marca, sku, preco, garantia_meses, estoque_minimo)
                 VALUES (:categoria_id, :nome, :descricao, :marca, :sku, :preco, :garantia_meses, :estoque_minimo)'
            );
            $stmt->execute($dados);

            if ($estoqueInicial > 0) {
                $movimentacao = new MovimentacaoEstoque();
                $movimentacao->registrar((int) $this->db->lastInsertId(), $usuarioId, 'entrada', $estoqueInicial, null, 'Estoque inicial do produto');
            }

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
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

    // Apaga o produto e o histórico de estoque dele (ON DELETE CASCADE).
    // Produto que já foi vendido não pode ser apagado (ON DELETE RESTRICT): o banco recusa e o erro sobe para o controller
    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM produtos WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
