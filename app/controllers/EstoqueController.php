<?php

class EstoqueController
{
    private ProdutoController $produtoController;
    private MovimentacaoEstoque $movimentacaoModel;

    public function __construct()
    {
        $this->produtoController = new ProdutoController();
        $this->movimentacaoModel = new MovimentacaoEstoque();
    }

    public function listarComEstoque(): array
    {
        return $this->produtoController->listar();
    }

    public function listarMovimentacoesRecentes(int $limite = 20): array
    {
        return $this->movimentacaoModel->listarRecentes($limite);
    }
    
    public function registrarMovimentacao(array $post, int $usuarioId): array
    {
        $produtoId    = (int) ($post['produto_id'] ?? 0);
        $tipo         = $post['tipo'] ?? '';
        $quantidade   = (int) ($post['quantidade'] ?? 0);
        $fornecedorId = !empty($post['fornecedor_id']) ? (int) $post['fornecedor_id'] : null;
        $observacao   = trim($post['observacao'] ?? '') ?: null;

        if ($produtoId <= 0) {
            return ['sucesso' => false, 'mensagem' => 'Selecione um produto.'];
        }

        if (!in_array($tipo, ['entrada', 'saida'], true)) {
            return ['sucesso' => false, 'mensagem' => 'Tipo de movimentação inválido.'];
        }

        if ($quantidade <= 0) {
            return ['sucesso' => false, 'mensagem' => 'Informe uma quantidade maior que zero.'];
        }

        $produto = $this->produtoController->buscar($produtoId);
        if ($produto === null) {
            return ['sucesso' => false, 'mensagem' => 'Produto não encontrado.'];
        }

        if ($tipo === 'saida' && (int) $produto['estoque_atual'] < $quantidade) {
            return ['sucesso' => false, 'mensagem' => 'Estoque insuficiente para essa saída.'];
        }

        $this->movimentacaoModel->registrar($produtoId, $usuarioId, $tipo, $quantidade, $fornecedorId, $observacao);

        $mensagem = $tipo === 'entrada' ? 'Entrada registrada com sucesso.' : 'Saída registrada com sucesso.';
        return ['sucesso' => true, 'mensagem' => $mensagem];
    }
}
