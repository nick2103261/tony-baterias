<?php

class DashboardController
{
    private Produto $produtoModel;

    public function __construct()
    {
        $this->produtoModel = new Produto();
    }

    public function resumo(string $perfil): array
    {
        $resumo = [
            'total_produtos' => $this->produtoModel->contarAtivos(),
            'valor_estoque'  => $this->produtoModel->valorTotalEstoque(),
        ];

        if ($perfil === 'admin') {
            $relatorioController = new RelatorioController();
            $financeiro = $relatorioController->financeiro(date('Y-m-01'), date('Y-m-d'));

            $resumo['total_vendas_mes'] = $financeiro['total_periodo'];
            $resumo['ticket_medio_mes'] = $financeiro['ticket_medio'];
        }

        return $resumo;
    }
}