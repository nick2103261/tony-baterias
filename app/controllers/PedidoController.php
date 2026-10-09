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

        $clienteId      = !empty($post['cliente_id']) ? (int) $post['cliente_id'] : null;
        $formaPagamento = !empty($post['forma_pagamento']) ? $post['forma_pagamento'] : null;
        $observacao     = trim($post['observacao'] ?? '') ?: null;

        $produtoIds  = $post['produto_id'] ?? [];
        $quantidades = $post['quantidade'] ?? [];

        $itens = [];
        foreach ($produtoIds as $indice => $produtoId) {
            $produtoId  = (int) $produtoId;
            $quantidade = (int) ($quantidades[$indice] ?? 0);

            if ($produtoId > 0 && $quantidade > 0) {
                $itens[] = ['produto_id' => $produtoId, 'quantidade' => $quantidade];
            }
        }

        if (empty($itens)) {
            return ['sucesso' => false, 'mensagem' => 'Adicione ao menos um item ao pedido.'];
        }

        $dados = [
            'cliente_id'      => $clienteId,
            'forma_pagamento' => $formaPagamento,
            'observacao'      => $observacao,
        ];

        if ($id === null) {
            $this->pedidoModel->criar($dados, $itens, $usuarioId);
            return ['sucesso' => true, 'mensagem' => 'Pedido criado com sucesso.'];
        }

        $this->pedidoModel->atualizar($id, $dados, $itens);
        return ['sucesso' => true, 'mensagem' => 'Pedido atualizado com sucesso.'];
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

        if (!in_array($novoStatus, $transicoesValidas[$statusAtual] ?? [], true)) {
            return ['sucesso' => false, 'mensagem' => "Não é possível mudar de \"{$statusAtual}\" para \"{$novoStatus}\"."];
        }

        if ($statusAtual === 'pendente' && $novoStatus === 'concluido') {
            $produtoController = new ProdutoController();
            foreach ($this->pedidoModel->buscarItens($id) as $item) {
                $produto = $produtoController->buscar((int) $item['produto_id']);
                if ($produto === null || (int) $produto['estoque_atual'] < (int) $item['quantidade']) {
                    $nomeProduto = $produto['nome'] ?? $item['produto_nome'];
                    return ['sucesso' => false, 'mensagem' => "Estoque insuficiente de \"{$nomeProduto}\" para concluir este pedido."];
                }
            }
        }

        $this->pedidoModel->atualizarStatus($id, $statusAtual, $novoStatus, $usuarioId);

        $mensagens = [
            'concluido' => 'Pedido concluído — estoque baixado.',
            'cancelado' => 'Pedido cancelado.',
        ];

        return ['sucesso' => true, 'mensagem' => $mensagens[$novoStatus] ?? 'Status atualizado.'];
    }
}
