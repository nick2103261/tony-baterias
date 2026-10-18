<?php

class MovimentacaoEstoque
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    // $tipo: 'entrada' soma no estoque do produto, 'saida' subtrai.
    // Se já houver uma transação aberta (ex.: concluir um pedido), participa dela em vez de abrir outra.
    public function registrar(int $produtoId, int $usuarioId, string $tipo, int $quantidade, ?int $fornecedorId, ?string $observacao = null): void
    {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException('A quantidade da movimentação deve ser maior que zero.');
        }

        $abriuTransacao = !$this->db->inTransaction();

        if ($abriuTransacao) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO movimentacoes_estoque (produto_id, usuario_id, fornecedor_id, tipo, quantidade, observacao)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$produtoId, $usuarioId, $tipo === 'entrada' ? $fornecedorId : null, $tipo, $quantidade, $observacao]);

            if ($tipo === 'entrada') {
                $stmt = $this->db->prepare('UPDATE produtos SET estoque_atual = estoque_atual + ? WHERE id = ?');
                $stmt->execute([$quantidade, $produtoId]);
            } else {
                // A própria condição do UPDATE impede o estoque de ficar negativo, mesmo com duas vendas ao mesmo tempo
                $stmt = $this->db->prepare('UPDATE produtos SET estoque_atual = estoque_atual - ? WHERE id = ? AND estoque_atual >= ?');
                $stmt->execute([$quantidade, $produtoId, $quantidade]);

                if ($stmt->rowCount() === 0) {
                    throw new EstoqueInsuficienteException('Estoque insuficiente para essa saída.');
                }
            }

            if ($abriuTransacao) {
                $this->db->commit();
            }
        } catch (Throwable $e) {
            if ($abriuTransacao && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
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
