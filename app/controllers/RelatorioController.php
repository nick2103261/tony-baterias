<?php

class RelatorioController
{
    public function gerar(string $dataInicio, string $dataFim): array
    {
        [$dataInicio, $dataFim] = $this->normalizarPeriodo($dataInicio, $dataFim);

        $inicioCompleto = $dataInicio . ' 00:00:00';
        $fimCompleto    = $dataFim . ' 23:59:59';

        $relatorioModel = new Relatorio();

        return [
            'data_inicio'   => $dataInicio,
            'data_fim'      => $dataFim,
            'total_periodo' => $relatorioModel->totalVendidoNoPeriodo($inicioCompleto, $fimCompleto),
            'produtos'      => $relatorioModel->produtoMaisVendido($inicioCompleto, $fimCompleto),
            'clientes'      => $relatorioModel->clientesQueMaisCompram($inicioCompleto, $fimCompleto),
        ];
    }

    public function financeiro(string $dataInicio, string $dataFim): array
    {
        [$dataInicio, $dataFim] = $this->normalizarPeriodo($dataInicio, $dataFim);

        $inicioCompleto = $dataInicio . ' 00:00:00';
        $fimCompleto    = $dataFim . ' 23:59:59';

        $relatorioModel = new Relatorio();

        return [
            'data_inicio'       => $dataInicio,
            'data_fim'          => $dataFim,
            'total_periodo'     => $relatorioModel->totalVendidoNoPeriodo($inicioCompleto, $fimCompleto),
            'ticket_medio'      => $relatorioModel->ticketMedio($inicioCompleto, $fimCompleto),
            'formas_pagamento'  => $relatorioModel->totalPorFormaPagamento($inicioCompleto, $fimCompleto),
        ];
    }

    private function normalizarPeriodo(string $dataInicio, string $dataFim): array
    {
        // Sem filtro (ou data inválida na URL): mês atual inteiro até hoje
        if (!$this->dataValida($dataInicio)) {
            $dataInicio = date('Y-m-01');
        }
        if (!$this->dataValida($dataFim)) {
            $dataFim = date('Y-m-d');
        }

        // Se o admin inverteu as datas, troca de lugar em vez de mostrar tudo vazio
        if ($dataInicio > $dataFim) {
            [$dataInicio, $dataFim] = [$dataFim, $dataInicio];
        }

        return [$dataInicio, $dataFim];
    }

    private function dataValida(string $data): bool
    {
        $objeto = DateTime::createFromFormat('Y-m-d', $data);

        return $objeto !== false && $objeto->format('Y-m-d') === $data;
    }
}