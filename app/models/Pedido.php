<?php

class Pedido
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function listarTodos(): array
    {
        return $this->db->query(
            'SELECT p.*, c.nome AS cliente_nome
             FROM pedidos p
             LEFT JOIN clientes c ON c.id = p.cliente_id
             ORDER BY p.criado_em DESC'
        )->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.nome AS cliente_nome, u.nome AS usuario_nome
             FROM pedidos p
             LEFT JOIN clientes c ON c.id = p.cliente_id
             LEFT JOIN usuarios u ON u.id = p.usuario_id
             WHERE p.id = ?'
        );
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function buscarItens(int $pedidoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, pr.nome AS produto_nome
             FROM itens_pedido i
             JOIN produtos pr ON pr.id = i.produto_id
             WHERE i.pedido_id = ?'
        );
        $stmt->execute([$pedidoId]);

        return $stmt->fetchAll();
    }

    public function criar(array $dados, array $itens, int $usuarioId): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO pedidos (cliente_id, usuario_id, status, forma_pagamento, observacao)
                 VALUES (?, ?, 'pendente', ?, ?)"
            );
            $stmt->execute([$dados['cliente_id'], $usuarioId, $dados['forma_pagamento'], $dados['observacao']]);

            $this->inserirItens((int) $this->db->lastInsertId(), $itens);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function atualizar(int $id, array $dados, array $itens): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'UPDATE pedidos
                 SET cliente_id = ?, forma_pagamento = ?, observacao = ?
                 WHERE id = ?'
            );
            $stmt->execute([$dados['cliente_id'], $dados['forma_pagamento'], $dados['observacao'], $id]);

            $stmt = $this->db->prepare('DELETE FROM itens_pedido WHERE pedido_id = ?');
            $stmt->execute([$id]);

            $this->inserirItens($id, $itens);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Concluir baixa o estoque dos itens; cancelar uma venda concluída devolve
    public function atualizarStatus(int $id, string $statusAtual, string $novoStatus, int $usuarioId): void
    {
        $stmt = $this->db->prepare('UPDATE pedidos SET status = ? WHERE id = ?');
        $stmt->execute([$novoStatus, $id]);

        if ($statusAtual === 'pendente' && $novoStatus === 'concluido') {
            $this->moverEstoqueDosItens($id, $usuarioId, 'saida', "Venda - Pedido #{$id}");
        }

        if ($statusAtual === 'concluido' && $novoStatus === 'cancelado') {
            $this->moverEstoqueDosItens($id, $usuarioId, 'entrada', "Cancelamento - Pedido #{$id}");
        }
    }

    // O preço de cada item é o preço do produto no momento em que o pedido é salvo
    private function inserirItens(int $pedidoId, array $itens): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
             SELECT ?, id, ?, preco FROM produtos WHERE id = ?'
        );

        foreach ($itens as $item) {
            $stmt->execute([$pedidoId, $item['quantidade'], $item['produto_id']]);
        }

        $stmt = $this->db->prepare(
            'UPDATE pedidos
             SET valor_total = (SELECT COALESCE(SUM(quantidade * preco_unitario), 0) FROM itens_pedido WHERE pedido_id = ?)
             WHERE id = ?'
        );
        $stmt->execute([$pedidoId, $pedidoId]);
    }

    private function moverEstoqueDosItens(int $pedidoId, int $usuarioId, string $tipo, string $observacao): void
    {
        $movimentacao = new MovimentacaoEstoque();

        foreach ($this->buscarItens($pedidoId) as $item) {
            $movimentacao->registrar((int) $item['produto_id'], $usuarioId, $tipo, (int) $item['quantidade'], null, $observacao);
        }
    }
}
