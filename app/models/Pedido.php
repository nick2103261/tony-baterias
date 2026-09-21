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
        $sql = 'SELECT p.*, c.nome AS cliente_nome
                FROM pedidos p
                LEFT JOIN clientes c ON c.id = p.cliente_id
                ORDER BY p.criado_em DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT p.*, c.nome AS cliente_nome
                FROM pedidos p
                LEFT JOIN clientes c ON c.id = p.cliente_id
                WHERE p.id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pedido = $stmt->fetch();
        return $pedido ?: null;
    }

    public function buscarItens(int $pedidoId): array
    {
        $sql = 'SELECT i.*, pr.nome AS produto_nome
                FROM itens_pedido i
                JOIN produtos pr ON pr.id = i.produto_id
                WHERE i.pedido_id = :pedido_id';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function criar(array $dados, array $itens, int $usuarioId): int
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO pedidos (cliente_id, usuario_id, status, forma_pagamento, observacao, valor_total)
                 VALUES (:cliente_id, :usuario_id, 'pendente', :forma_pagamento, :observacao, 0)"
            );
            $stmt->execute([
                ':cliente_id'      => $dados['cliente_id'],
                ':usuario_id'      => $usuarioId,
                ':forma_pagamento' => $dados['forma_pagamento'],
                ':observacao'      => $dados['observacao'],
            ]);

            $pedidoId = (int) $this->db->lastInsertId();

            $valorTotal = $this->inserirItens($pedidoId, $itens);
            $this->atualizarValorTotal($pedidoId, $valorTotal);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $pedidoId;
    }


    public function atualizar(int $id, array $dados, array $itens): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'UPDATE pedidos
                 SET cliente_id = :cliente_id, forma_pagamento = :forma_pagamento, observacao = :observacao
                 WHERE id = :id'
            );
            $stmt->execute([
                ':cliente_id'      => $dados['cliente_id'],
                ':forma_pagamento' => $dados['forma_pagamento'],
                ':observacao'      => $dados['observacao'],
                ':id'              => $id,
            ]);

            $stmtDel = $this->db->prepare('DELETE FROM itens_pedido WHERE pedido_id = :id');
            $stmtDel->execute([':id' => $id]);

            $valorTotal = $this->inserirItens($id, $itens);
            $this->atualizarValorTotal($id, $valorTotal);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    public function atualizarStatus(int $id, string $statusAtual, string $novoStatus, int $usuarioId): void
    {
        $stmt = $this->db->prepare('UPDATE pedidos SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $novoStatus, ':id' => $id]);

        if ($statusAtual === 'pendente' && $novoStatus === 'concluido') {
            $this->moverEstoqueDosItens($id, $usuarioId, 'saida', "Venda - Pedido #{$id}");
        }

        if ($statusAtual === 'concluido' && $novoStatus === 'cancelado') {
            $this->moverEstoqueDosItens($id, $usuarioId, 'entrada', "Cancelamento - Pedido #{$id}");
        }
    }

    private function inserirItens(int $pedidoId, array $itens): float
    {
        $stmtPreco = $this->db->prepare('SELECT preco FROM produtos WHERE id = :id');
        $stmtItem  = $this->db->prepare(
            'INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
             VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)'
        );

        $valorTotal = 0.0;

        foreach ($itens as $item) {
            $stmtPreco->execute([':id' => $item['produto_id']]);
            $produto = $stmtPreco->fetch();
            $preco = $produto ? (float) $produto['preco'] : 0.0;

            $stmtItem->execute([
                ':pedido_id'      => $pedidoId,
                ':produto_id'     => $item['produto_id'],
                ':quantidade'     => $item['quantidade'],
                ':preco_unitario' => $preco,
            ]);

            $valorTotal += $preco * $item['quantidade'];
        }

        return $valorTotal;
    }

    private function atualizarValorTotal(int $pedidoId, float $valorTotal): void
    {
        $stmt = $this->db->prepare('UPDATE pedidos SET valor_total = :valor_total WHERE id = :id');
        $stmt->execute([':valor_total' => $valorTotal, ':id' => $pedidoId]);
    }

    private function moverEstoqueDosItens(int $pedidoId, int $usuarioId, string $tipo, string $observacao): void
    {
        $movimentacao = new MovimentacaoEstoque();

        foreach ($this->buscarItens($pedidoId) as $item) {
            $movimentacao->registrar((int) $item['produto_id'], $usuarioId, $tipo, (int) $item['quantidade'], null, $observacao);
        }
    }
}
