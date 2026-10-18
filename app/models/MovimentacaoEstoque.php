<?php

class MovimentacaoEstoque
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    // $tipo: 'entrada' soma no estoque do produto, 'saida' subtrai
    public function registrar(int $produtoId, int $usuarioId, string $tipo, int $quantidade, ?int $fornecedorId, ?string $observacao = null): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO movimentacoes_estoque (produto_id, usuario_id, fornecedor_id, tipo, quantidade, observacao)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$produtoId, $usuarioId, $tipo === 'entrada' ? $fornecedorId : null, $tipo, $quantidade, $observacao]);

            $operador = $tipo === 'entrada' ? '+' : '-';
            $stmt = $this->db->prepare("UPDATE produtos SET estoque_atual = estoque_atual {$operador} ? WHERE id = ?");
            $stmt->execute([$quantidade, $produtoId]);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function listarRecentes(int $limite = 20): array
    {
        return $this->db->query(
            "SELECT m.*, p.nome AS produto_nome, u.nome AS usuario_nome, f.nome AS fornecedor_nome
             FROM movimentacoes_estoque m
             JOIN produtos p ON p.id = m.produto_id
             JOIN usuarios u ON u.id = m.usuario_id
             LEFT JOIN fornecedores f ON f.id = m.fornecedor_id
             ORDER BY m.criado_em DESC, m.id DESC
             LIMIT {$limite}"
        )->fetchAll();
    }
}
