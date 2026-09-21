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
        $sql = 'SELECT p.*, c.nome AS categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.ativo = 1
                ORDER BY p.nome';

        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM produtos WHERE id = :id AND ativo = 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $produto = $stmt->fetch();
        return $produto ?: null;
    }

    public function buscarPorSku(string $sku): ?array
    {
        $stmt = $this->db->prepare('SELECT id, nome FROM produtos WHERE sku = :sku AND ativo = 1');
        $stmt->bindValue(':sku', $sku);
        $stmt->execute();

        $produto = $stmt->fetch();
        return $produto ?: null;
    }

    public function contarAtivos(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM produtos WHERE ativo = 1')->fetchColumn();
    }

    public function valorTotalEstoque(): float
    {
        $sql = 'SELECT COALESCE(SUM(preco * estoque_atual), 0) FROM produtos WHERE ativo = 1';

        return (float) $this->db->query($sql)->fetchColumn();
    }


    public function criar(array $dados, int $estoqueInicial, int $usuarioId): int
    {
        $sql = 'INSERT INTO produtos (categoria_id, nome, descricao, marca, sku, preco, garantia_meses, estoque_atual, estoque_minimo)
                VALUES (:categoria_id, :nome, :descricao, :marca, :sku, :preco, :garantia_meses, 0, :estoque_minimo)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':categoria_id'   => $dados['categoria_id'] ?: null,
            ':nome'           => $dados['nome'],
            ':descricao'      => $dados['descricao'],
            ':marca'          => $dados['marca'],
            ':sku'            => $dados['sku'],
            ':preco'          => $dados['preco'],
            ':garantia_meses' => $dados['garantia_meses'],
            ':estoque_minimo' => $dados['estoque_minimo'],
        ]);

        $produtoId = (int) $this->db->lastInsertId();

        if ($estoqueInicial > 0) {
            $movimentacao = new MovimentacaoEstoque();
            $movimentacao->registrar($produtoId, $usuarioId, 'entrada', $estoqueInicial, null, 'Estoque inicial do produto');
        }

        return $produtoId;
    }

    public function atualizar(int $id, array $dados): void
    {
        $sql = 'UPDATE produtos
                SET categoria_id = :categoria_id,
                    nome = :nome,
                    descricao = :descricao,
                    marca = :marca,
                    sku = :sku,
                    preco = :preco,
                    garantia_meses = :garantia_meses,
                    estoque_minimo = :estoque_minimo
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':categoria_id'   => $dados['categoria_id'] ?: null,
            ':nome'           => $dados['nome'],
            ':descricao'      => $dados['descricao'],
            ':marca'          => $dados['marca'],
            ':sku'            => $dados['sku'],
            ':preco'          => $dados['preco'],
            ':garantia_meses' => $dados['garantia_meses'],
            ':estoque_minimo' => $dados['estoque_minimo'],
            ':id'             => $id,
        ]);
    }

    public function inativar(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE produtos SET ativo = 0 WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
