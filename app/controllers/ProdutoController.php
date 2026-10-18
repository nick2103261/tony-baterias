<?php

class ProdutoController
{
    private Produto $produtoModel;

    public function __construct()
    {
        $this->produtoModel = new Produto();
    }

    public function listar(): array
    {
        return $this->produtoModel->listarTodos();
    }

    public function buscar(int $id): ?array
    {
        return $this->produtoModel->buscarPorId($id);
    }

    public function salvar(array $post, ?int $id, int $usuarioId): array
    {
        $nome           = campoTexto($post, 'nome');
        $descricao      = campoTexto($post, 'descricao');
        $marca          = campoTexto($post, 'marca');
        $sku            = campoTexto($post, 'sku');
        $preco          = isset($post['preco']) ? campoTexto($post, 'preco') : '0';
        $garantiaMeses  = (int) campoTexto($post, 'garantia_meses');
        $estoqueMinimo  = (int) campoTexto($post, 'estoque_minimo');
        $estoqueInicial = (int) campoTexto($post, 'estoque_inicial');
        $categoriaId    = campoTexto($post, 'categoria_id');

        // Aceita "1.234,56" e "320,50": com vírgula, os pontos são separadores de milhar
        if (strpos($preco, ',') !== false) {
            $preco = str_replace('.', '', $preco);
            $preco = str_replace(',', '.', $preco);
        }

        if ($nome === '') {
            return ['sucesso' => false, 'mensagem' => 'O nome do produto é obrigatório.'];
        }

        $erroTamanho = erroDeTamanho(
            ['nome' => $nome, 'marca' => $marca, 'sku' => $sku, 'descricao' => $descricao],
            ['nome' => [150, 'nome'], 'marca' => [80, 'marca'], 'sku' => [40, 'SKU'], 'descricao' => [5000, 'descrição']]
        );

        if ($erroTamanho !== null) {
            return ['sucesso' => false, 'mensagem' => $erroTamanho];
        }

        if (!is_numeric($preco) || !is_finite((float) $preco) || (float) $preco < 0 || (float) $preco > 99999999.99) {
            return ['sucesso' => false, 'mensagem' => 'Informe um preço válido, de 0 até 99.999.999,99.'];
        }

        if ($garantiaMeses < 0 || $estoqueMinimo < 0 || $estoqueInicial < 0) {
            return ['sucesso' => false, 'mensagem' => 'Os valores numéricos não podem ser negativos.'];
        }

        if ($garantiaMeses > 65535) {
            return ['sucesso' => false, 'mensagem' => 'A garantia aceita no máximo 65.535 meses.'];
        }

        if ($estoqueMinimo > LIMITE_QUANTIDADE || $estoqueInicial > LIMITE_QUANTIDADE) {
            return ['sucesso' => false, 'mensagem' => 'O estoque aceita no máximo ' . number_format(LIMITE_QUANTIDADE, 0, ',', '.') . ' unidades.'];
        }

        if ($categoriaId !== '' && !(new Categoria())->existe((int) $categoriaId)) {
            return ['sucesso' => false, 'mensagem' => 'Categoria inválida.'];
        }

        if ($id !== null && $this->produtoModel->buscarPorId($id) === null) {
            return ['sucesso' => false, 'mensagem' => 'Produto não encontrado.'];
        }

        if ($sku !== '') {
            $existente = $this->produtoModel->buscarPorSku($sku);

            if ($existente !== null && (int) $existente['id'] !== $id) {
                return ['sucesso' => false, 'mensagem' => 'Já existe um produto com esse SKU.'];
            }
        }

        $dados = [
            'categoria_id'   => $categoriaId !== '' ? (int) $categoriaId : null,
            'nome'           => $nome,
            'descricao'      => textoOuNulo($descricao),
            'marca'          => textoOuNulo($marca),
            'sku'            => textoOuNulo($sku),
            'preco'          => round((float) $preco, 2),
            'garantia_meses' => $garantiaMeses,
            'estoque_minimo' => $estoqueMinimo,
        ];

        try {
            if ($id === null) {
                $this->produtoModel->criar($dados, $estoqueInicial, $usuarioId);
                return ['sucesso' => true, 'mensagem' => 'Produto cadastrado com sucesso.'];
            }

            $this->produtoModel->atualizar($id, $dados);
            return ['sucesso' => true, 'mensagem' => 'Produto atualizado com sucesso.'];
        } catch (PDOException $e) {
            // Duas pessoas salvando o mesmo SKU ao mesmo tempo
            if ($e->getCode() === '23000') {
                return ['sucesso' => false, 'mensagem' => 'Já existe um produto com esse SKU.'];
            }
            throw $e;
        }
    }

    public function excluir(int $id): array
    {
        if (!$this->produtoModel->inativar($id)) {
            return ['sucesso' => false, 'mensagem' => 'Produto não encontrado.'];
        }

        return ['sucesso' => true, 'mensagem' => 'Produto excluído com sucesso.'];
    }
}
