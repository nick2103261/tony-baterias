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
        $nome           = trim($post['nome'] ?? '');
        $descricao      = trim($post['descricao'] ?? '');
        $marca          = trim($post['marca'] ?? '');
        $sku            = trim($post['sku'] ?? '');
        $preco          = str_replace(',', '.', $post['preco'] ?? '0');
        $garantiaMeses  = (int) ($post['garantia_meses'] ?? 0);
        $estoqueMinimo  = (int) ($post['estoque_minimo'] ?? 0);
        $categoriaId    = $post['categoria_id'] !== '' ? (int) $post['categoria_id'] : null;
        $estoqueInicial = (int) ($post['estoque_inicial'] ?? 0);

        if ($nome === '') {
            return ['sucesso' => false, 'mensagem' => 'O nome do produto é obrigatório.'];
        }

        if (!is_numeric($preco) || (float) $preco < 0) {
            return ['sucesso' => false, 'mensagem' => 'Informe um preço válido.'];
        }

        if ($garantiaMeses < 0 || $estoqueMinimo < 0 || $estoqueInicial < 0) {
            return ['sucesso' => false, 'mensagem' => 'Os valores numéricos não podem ser negativos.'];
        }

        if ($sku !== '') {
            $existente = $this->produtoModel->buscarPorSku($sku);
            if ($existente !== null && (int) $existente['id'] !== $id) {
                return ['sucesso' => false, 'mensagem' => 'Já existe um produto com esse SKU.'];
            }
        }

        $dados = [
            'categoria_id'   => $categoriaId,
            'nome'           => $nome,
            'descricao'      => $descricao !== '' ? $descricao : null,
            'marca'          => $marca !== '' ? $marca : null,
            'sku'            => $sku !== '' ? $sku : null,
            'preco'          => (float) $preco,
            'garantia_meses' => $garantiaMeses,
            'estoque_minimo' => $estoqueMinimo,
        ];

        if ($id === null) {
            $this->produtoModel->criar($dados, $estoqueInicial, $usuarioId);
            return ['sucesso' => true, 'mensagem' => 'Produto cadastrado com sucesso.'];
        }

        $this->produtoModel->atualizar($id, $dados);
        return ['sucesso' => true, 'mensagem' => 'Produto atualizado com sucesso.'];
    }

    public function excluir(int $id): void
    {
        $this->produtoModel->inativar($id);
    }
}
