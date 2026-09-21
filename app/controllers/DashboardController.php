<?php

class DashboardController
{
    private Produto $produtoModel;

    public function __construct()
    {
        $this->produtoModel = new Produto();
    }

    public function resumo(): array
    {
        return [
            'total_produtos' => $this->produtoModel->contarAtivos(),
            'valor_estoque'  => $this->produtoModel->valorTotalEstoque(),
        ];
    }
}
