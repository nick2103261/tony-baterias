<?php

class RelatorioController
{
    public function gerar(string $dataInicio, string $dataFim): array
    {
        [$dataInicio, $dataFim, $inicio, $fim] = $this->periodo($dataInicio, $dataFim);

        $relatorio = new Relatorio();

        return [
            'data_inicio'   => $dataInicio,
            'data_fim'      => $dataFim,
            'total_periodo' => $relatorio->totalVendidoNoPeriodo($inicio, $fim),
            'produtos'      => $relatorio->produtoMaisVendido($inicio, $fim),
        ];
    }

    public function financeiro(string $dataInicio, string $dataFim): array
    {
        [$dataInicio, $dataFim, $inicio, $fim] = $this->periodo($dataInicio, $dataFim);

        $relatorio = new Relatorio();

        return [
            'data_inicio'      => $dataInicio,
            'data_fim'         => $dataFim,
            'total_periodo'    => $relatorio->totalVendidoNoPeriodo($inicio, $fim),
            'formas_pagamento' => $relatorio->totalPorFormaPagamento($inicio, $fim),
        ];
    }

    // Sem filtro (ou data inválida): do dia 1 do mês até hoje. Datas invertidas são trocadas.
    private function periodo(string $dataInicio, string $dataFim): array
    {
        if (!$this->dataValida($dataInicio)) {
            $dataInicio = date('Y-m-01');
        }

        if (!$this->dataValida($dataFim)) {
            $dataFim = date('Y-m-d');
        }

        if ($dataInicio > $dataFim) {
            [$dataInicio, $dataFim] = [$dataFim, $dataInicio];
        }

        return [$dataInicio, $dataFim, $dataInicio . ' 00:00:00', $dataFim . ' 23:59:59'];
    }

    private function dataValida(string $data): bool
    {
        $objeto = DateTime::createFromFormat('Y-m-d', $data);

        return $objeto !== false && $objeto->format('Y-m-d') === $data;
    }
}
