<?php

class PedidoController
{
    private Pedido $pedidoModel;

    public function __construct()
    {
        $this->pedidoModel = new Pedido();
    }

    public function listar(): array
    {
        return $this->pedidoModel->listarTodos();
    }

    public function listarRecentes(int $limite): array
    {
        return $this->pedidoModel->listarRecentes($limite);
    }

    public function contarPendentes(): int
    {
        return $this->pedidoModel->contarPendentes();
    }

    public function buscar(int $id): ?array
    {
        return $this->pedidoModel->buscarPorId($id);
    }

    public function buscarItens(int $id): array
    {
        return $this->pedidoModel->buscarItens($id);
    }

    public function salvar(array $post, ?int $id, int $usuarioId): array
    {
        if ($id !== null) {
            $pedidoAtual = $this->pedidoModel->buscarPorId($id);

            if ($pedidoAtual === null) {
                return ['sucesso' => false, 'mensagem' => 'Pedido não encontrado.'];
            }

            if ($pedidoAtual['status'] !== 'pendente') {
                return ['sucesso' => false, 'mensagem' => 'Só é possível editar um pedido enquanto ele está pendente.'];
            }
        }

        // Linhas repetidas do mesmo produto viram uma só, com a quantidade somada
        $produtosPost    = is_array($post['produto_id'] ?? null) ? $post['produto_id'] : [];
        $quantidadesPost = is_array($post['quantidade'] ?? null) ? $post['quantidade'] : [];
        $quantidades     = [];

        foreach ($produtosPost as $indice => $produtoId) {
            $quantidade = $quantidadesPost[$indice] ?? 0;

            if (!is_scalar($produtoId) || !is_scalar($quantidade)) {
                continue;
            }

            $produtoId  = (int) $produtoId;
            $quantidade = (int) $quantidade;

            if ($produtoId > 0 && $quantidade > 0) {
                $quantidades[$produtoId] = ($quantidades[$produtoId] ?? 0) + $quantidade;
            }
        }

        if (empty($quantidades)) {
            return ['sucesso' => false, 'mensagem' => 'Adicione ao menos um item ao pedido.'];
        }

        $produtoController = new ProdutoController();
        $itens = [];

        foreach ($quantidades as $produtoId => $quantidade) {
            if ($quantidade > LIMITE_QUANTIDADE) {
                return ['sucesso' => false, 'mensagem' => 'A quantidade de um item aceita no máximo ' . number_format(LIMITE_QUANTIDADE, 0, ',', '.') . ' unidades.'];
            }

            if ($produtoController->buscar($produtoId) === null) {
                return ['sucesso' => false, 'mensagem' => 'Um dos produtos escolhidos não existe mais. Atualize a página e refaça o pedido.'];
            }

            $itens[] = ['produto_id' => $produtoId, 'quantidade' => $quantidade];
        }

        $clienteId  = (int) campoTexto($post, 'cliente_id') ?: null;
        $pagamento  = campoTexto($post, 'forma_pagamento');
        $observacao = campoTexto($post, 'observacao');

        if ($clienteId !== null && (new ClienteController())->buscar($clienteId) === null) {
            return ['sucesso' => false, 'mensagem' => 'Cliente inválido.'];
        }

        if (mb_strlen($observacao) > 255) {
            return ['sucesso' => false, 'mensagem' => 'A observação aceita no máximo 255 caracteres.'];
        }

        $dados = [
            'cliente_id'      => $clienteId,
            'forma_pagamento' => isset(FORMAS_PAGAMENTO[$pagamento]) ? $pagamento : null,
            'observacao'      => textoOuNulo($observacao),
        ];

        try {
            if ($id === null) {
                $this->pedidoModel->criar($dados, $itens, $usuarioId);
                return ['sucesso' => true, 'mensagem' => 'Pedido criado com sucesso.'];
            }

            $this->pedidoModel->atualizar($id, $dados, $itens);
            return ['sucesso' => true, 'mensagem' => 'Pedido atualizado com sucesso.'];
        } catch (RegraDeNegocioException $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }

    public function atualizarStatus(int $id, string $novoStatus, int $usuarioId): array
    {
        $transicoesValidas = [
            'pendente'  => ['concluido', 'cancelado'],
            'concluido' => ['cancelado'],
            'cancelado' => [],
        ];

        $pedido = $this->pedidoModel->buscarPorId($id);

        if ($pedido === null) {
            return ['sucesso' => false, 'mensagem' => 'Pedido não encontrado.'];
        }

        $statusAtual = $pedido['status'];

        if (!in_array($novoStatus, $transicoesValidas[$statusAtual], true)) {
            return ['sucesso' => false, 'mensagem' => "Não é possível mudar de \"{$statusAtual}\" para \"{$novoStatus}\"."];
        }

        if ($novoStatus === 'concluido') {
            // Soma por produto antes de comparar com o estoque (o mesmo produto pode aparecer em mais de uma linha)
            $necessario = [];

            foreach ($this->pedidoModel->buscarItens($id) as $item) {
                $produtoId = (int) $item['produto_id'];

                $necessario[$produtoId]['nome']       = $item['produto_nome'];
                $necessario[$produtoId]['quantidade'] = ($necessario[$produtoId]['quantidade'] ?? 0) + (int) $item['quantidade'];
            }

            $produtoController = new ProdutoController();

            foreach ($necessario as $produtoId => $item) {
                $produto = $produtoController->buscar($produtoId);

                if ($produto === null) {
                    return ['sucesso' => false, 'mensagem' => "O produto \"{$item['nome']}\" foi excluído e não pode mais ser vendido. Cancele este pedido."];
                }

                if ((int) $produto['estoque_atual'] < $item['quantidade']) {
                    return ['sucesso' => false, 'mensagem' => "Estoque insuficiente de \"{$item['nome']}\" para concluir este pedido."];
                }
            }
        }

        try {
            $this->pedidoModel->atualizarStatus($id, $statusAtual, $novoStatus, $usuarioId);
        } catch (EstoqueInsuficienteException $e) {
            // Outra venda levou o estoque entre a conferência acima e a baixa; nada foi alterado
            return ['sucesso' => false, 'mensagem' => 'Estoque insuficiente para concluir este pedido. Outra venda pode ter usado o estoque agora há pouco.'];
        } catch (RegraDeNegocioException $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }

        return ['sucesso' => true, 'mensagem' => $novoStatus === 'concluido' ? 'Pedido concluído — estoque baixado.' : 'Pedido cancelado.'];
    }
}
