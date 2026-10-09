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
        $sql = "SELECT pr.id, pr.nome, SUM(i.quantidade) AS total_vendido
                FROM itens_pedido i
                JOIN pedidos p ON p.id = i.pedido_id
                JOIN produtos pr ON pr.id = i.produto_id
                WHERE p.status = 'concluido'
                  AND p.criado_em BETWEEN :inicio AND :fim
                GROUP BY pr.id, pr.nome
                ORDER BY total_vendido DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':inicio', $dataInicio);
        $stmt->bindValue(':fim', $dataFim);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function clientesQueMaisCompram(string $dataInicio, string $dataFim, int $limite = 10): array
    {
        $sql = "SELECT c.id, c.nome, SUM(p.valor_total) AS total_comprado, COUNT(p.id) AS qtd_pedidos
                FROM pedidos p
                JOIN clientes c ON c.id = p.cliente_id
                WHERE p.status = 'concluido'
                  AND p.criado_em BETWEEN :inicio AND :fim
                GROUP BY c.id, c.nome
                ORDER BY total_comprado DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':inicio', $dataInicio);
        $stmt->bindValue(':fim', $dataFim);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function totalVendidoNoPeriodo(string $dataInicio, string $dataFim): float
    {
        $sql = "SELECT COALESCE(SUM(valor_total), 0) AS total
                FROM pedidos
                WHERE status = 'concluido'
                  AND criado_em BETWEEN :inicio AND :fim";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':inicio', $dataInicio);
        $stmt->bindValue(':fim', $dataFim);
        $stmt->execute();

        return (float) $stmt->fetch()['total'];
    }

    public function totalPorFormaPagamento(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT forma_pagamento, SUM(valor_total) AS total, COUNT(id) AS qtd_vendas
                FROM pedidos
                WHERE status = 'concluido'
                  AND criado_em BETWEEN :inicio AND :fim
                GROUP BY forma_pagamento
                ORDER BY total DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':inicio', $dataInicio);
        $stmt->bindValue(':fim', $dataFim);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function ticketMedio(string $dataInicio, string $dataFim): float
    {
        $sql = "SELECT COALESCE(AVG(valor_total), 0) AS media
                FROM pedidos
                WHERE status = 'concluido'
                  AND criado_em BETWEEN :inicio AND :fim";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':inicio', $dataInicio);
        $stmt->bindValue(':fim', $dataFim);
        $stmt->execute();

        return (float) $stmt->fetch()['media'];
    }
}