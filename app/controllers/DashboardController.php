<?php

class DashboardController
{
    public function resumo(string $perfil): array
    {
        $produtos         = (new ProdutoController())->listar();
        $pedidoController = new PedidoController();

        $estoqueBaixo = array_values(array_filter(
            $produtos,
            fn($produto) => (int) $produto['estoque_atual'] <= (int) $produto['estoque_minimo']
        ));
        usort($estoqueBaixo, fn($a, $b) => (int) $a['estoque_atual'] <=> (int) $b['estoque_atual']);

        $resumo = [
            'total_produtos'      => count($produtos),
            'valor_estoque'       => array_sum(array_map(fn($produto) => $produto['preco'] * $produto['estoque_atual'], $produtos)),
            'pedidos_pendentes'   => $pedidoController->contarPendentes(),
            'pedidos_recentes'    => $pedidoController->listarRecentes(6),
            'estoque_baixo_total' => count($estoqueBaixo),
            'estoque_baixo'       => array_slice($estoqueBaixo, 0, 6),
        ];

        if ($perfil === 'admin') {
            $relatorio = new Relatorio();
            $inicio    = date('Y-m-01') . ' 00:00:00';
            $fim       = date('Y-m-d') . ' 23:59:59';

            $resumo['total_vendas_mes'] = $relatorio->totalVendidoNoPeriodo($inicio, $fim);
            $resumo['mais_vendidos']    = $relatorio->produtoMaisVendido($inicio, $fim, 5);
        }

        return $resumo;
    }
}
