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

    public function listarMovimentacoesRecentes(int $limite = 20): array
    {
        return $this->movimentacaoModel->listarRecentes($limite);
    }

    public function registrarMovimentacao(array $post, int $usuarioId): array
    {
        $produtoId    = (int) ($post['produto_id'] ?? 0);
        $tipo         = $post['tipo'] ?? '';
        $quantidade   = (int) ($post['quantidade'] ?? 0);
        $fornecedorId = (int) ($post['fornecedor_id'] ?? 0) ?: null;
        $observacao   = trim($post['observacao'] ?? '') ?: null;

        $produto = $this->produtoController->buscar($produtoId);

        if ($produto === null) {
            return ['sucesso' => false, 'mensagem' => 'Selecione um produto.'];
        }

        if (!in_array($tipo, ['entrada', 'saida'], true)) {
            return ['sucesso' => false, 'mensagem' => 'Tipo de movimentação inválido.'];
        }

        if ($quantidade <= 0) {
            return ['sucesso' => false, 'mensagem' => 'Informe uma quantidade maior que zero.'];
        }

        if ($tipo === 'saida' && (int) $produto['estoque_atual'] < $quantidade) {
            return ['sucesso' => false, 'mensagem' => 'Estoque insuficiente para essa saída.'];
        }

        $this->movimentacaoModel->registrar($produtoId, $usuarioId, $tipo, $quantidade, $fornecedorId, $observacao);

        return ['sucesso' => true, 'mensagem' => $tipo === 'entrada' ? 'Entrada registrada com sucesso.' : 'Saída registrada com sucesso.'];
    }
}
