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

        $itens = [];
        foreach ($post['produto_id'] ?? [] as $indice => $produtoId) {
            $produtoId  = (int) $produtoId;
            $quantidade = (int) ($post['quantidade'][$indice] ?? 0);

            if ($produtoId > 0 && $quantidade > 0) {
                $itens[] = ['produto_id' => $produtoId, 'quantidade' => $quantidade];
            }
        }

        if (empty($itens)) {
            return ['sucesso' => false, 'mensagem' => 'Adicione ao menos um item ao pedido.'];
        }

        $dados = [
            'cliente_id'      => (int) ($post['cliente_id'] ?? 0) ?: null,
            'forma_pagamento' => isset(FORMAS_PAGAMENTO[$post['forma_pagamento'] ?? '']) ? $post['forma_pagamento'] : null,
            'observacao'      => trim($post['observacao'] ?? '') ?: null,
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

        if (!in_array($novoStatus, $transicoesValidas[$statusAtual], true)) {
            return ['sucesso' => false, 'mensagem' => "Não é possível mudar de \"{$statusAtual}\" para \"{$novoStatus}\"."];
        }

        if ($novoStatus === 'concluido') {
            $produtoController = new ProdutoController();

            foreach ($this->pedidoModel->buscarItens($id) as $item) {
                $produto = $produtoController->buscar((int) $item['produto_id']);

                if ($produto === null || (int) $produto['estoque_atual'] < (int) $item['quantidade']) {
                    return ['sucesso' => false, 'mensagem' => "Estoque insuficiente de \"{$item['produto_nome']}\" para concluir este pedido."];
                }
            }
        }

        $this->pedidoModel->atualizarStatus($id, $statusAtual, $novoStatus, $usuarioId);

        return ['sucesso' => true, 'mensagem' => $novoStatus === 'concluido' ? 'Pedido concluído — estoque baixado.' : 'Pedido cancelado.'];
    }
}
