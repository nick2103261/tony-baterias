<?php

class Relatorio
{
    private PDO $db;

    public function __construct()
    {
        $this->db = BancoDados::getConnection();
    }

    public function produtoMaisVendido(string $dataInicio, string $dataFim, int $limite = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT pr.nome, SUM(i.quantidade) AS total_vendido
             FROM itens_pedido i
             JOIN pedidos p ON p.id = i.pedido_id
             JOIN produtos pr ON pr.id = i.produto_id
             WHERE p.status = 'concluido' AND p.criado_em BETWEEN ? AND ?
             GROUP BY pr.id, pr.nome
             ORDER BY total_vendido DESC
             LIMIT {$limite}"
        );
        $stmt->execute([$dataInicio, $dataFim]);

        return $stmt->fetchAll();
    }

    public function totalVendidoNoPeriodo(string $dataInicio, string $dataFim): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(valor_total), 0)
             FROM pedidos
             WHERE status = 'concluido' AND criado_em BETWEEN ? AND ?"
        );
        $stmt->execute([$dataInicio, $dataFim]);

        return (float) $stmt->fetchColumn();
    }

    public function totalPorFormaPagamento(string $dataInicio, string $dataFim): array
    {
        $stmt = $this->db->prepare(
            "SELECT forma_pagamento, SUM(valor_total) AS total, COUNT(id) AS qtd_vendas
             FROM pedidos
             WHERE status = 'concluido' AND criado_em BETWEEN ? AND ?
             GROUP BY forma_pagamento
             ORDER BY total DESC"
        );
        $stmt->execute([$dataInicio, $dataFim]);

        return $stmt->fetchAll();
    }
}
