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
        $produtoId    = (int) campoTexto($post, 'produto_id');
        $tipo         = campoTexto($post, 'tipo');
        $quantidade   = (int) campoTexto($post, 'quantidade');
        $fornecedorId = (int) campoTexto($post, 'fornecedor_id') ?: null;
        $observacao   = campoTexto($post, 'observacao');

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

        if ($quantidade > LIMITE_QUANTIDADE) {
            return ['sucesso' => false, 'mensagem' => 'A quantidade aceita no máximo ' . number_format(LIMITE_QUANTIDADE, 0, ',', '.') . ' unidades por movimentação.'];
        }

        if (mb_strlen($observacao) > 255) {
            return ['sucesso' => false, 'mensagem' => 'A observação aceita no máximo 255 caracteres.'];
        }

        if ($tipo === 'entrada' && $fornecedorId !== null && (new FornecedorController())->buscar($fornecedorId) === null) {
            return ['sucesso' => false, 'mensagem' => 'Fornecedor inválido.'];
        }

        try {
            $this->movimentacaoModel->registrar($produtoId, $usuarioId, $tipo, $quantidade, $fornecedorId, textoOuNulo($observacao));
        } catch (EstoqueInsuficienteException $e) {
            return ['sucesso' => false, 'mensagem' => 'Estoque insuficiente para essa saída.'];
        }

        return ['sucesso' => true, 'mensagem' => $tipo === 'entrada' ? 'Entrada registrada com sucesso.' : 'Saída registrada com sucesso.'];
    }
}
