<?php

class FornecedorController
{
    private Fornecedor $fornecedorModel;

    public function __construct()
    {
        $this->fornecedorModel = new Fornecedor();
    }

    public function listar(): array
    {
        return $this->fornecedorModel->listarTodos();
    }

    public function buscar(int $id): ?array
    {
        return $this->fornecedorModel->buscarPorId($id);
    }


    public function salvar(array $post, ?int $id): array
    {
        $nome    = trim($post['nome'] ?? '');
        $contato = trim($post['contato'] ?? '');
        $cnpj    = trim($post['cnpj'] ?? '');

        if ($nome === '') {
            return ['sucesso' => false, 'mensagem' => 'O nome do fornecedor é obrigatório.'];
        }

        $dados = [
            'nome'    => $nome,
            'contato' => $contato !== '' ? $contato : null,
            'cnpj'    => $cnpj !== '' ? $cnpj : null,
        ];

        if ($id === null) {
            $this->fornecedorModel->criar($dados);
            return ['sucesso' => true, 'mensagem' => 'Fornecedor cadastrado com sucesso.'];
        }

        $this->fornecedorModel->atualizar($id, $dados);
        return ['sucesso' => true, 'mensagem' => 'Fornecedor atualizado com sucesso.'];
    }

    public function excluir(int $id): void
    {
        $this->fornecedorModel->inativar($id);
    }
}
