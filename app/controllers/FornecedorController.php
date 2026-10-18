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
        $dados = [];
        foreach (['nome', 'contato', 'cnpj'] as $campo) {
            $dados[$campo] = textoOuNulo(campoTexto($post, $campo));
        }

        if ($dados['nome'] === null) {
            return ['sucesso' => false, 'mensagem' => 'O nome do fornecedor é obrigatório.'];
        }

        $erroTamanho = erroDeTamanho($dados, [
            'nome'    => [150, 'nome'],
            'contato' => [100, 'contato'],
        ]);

        if ($erroTamanho !== null) {
            return ['sucesso' => false, 'mensagem' => $erroTamanho];
        }

        if ($dados['cnpj'] !== null) {
            if (!cnpjValido($dados['cnpj'])) {
                return ['sucesso' => false, 'mensagem' => 'CNPJ inválido.'];
            }

            $dados['cnpj'] = formatarCnpj($dados['cnpj']);
            $existente     = $this->fornecedorModel->buscarPorCnpj($dados['cnpj']);

            if ($existente !== null && (int) $existente['id'] !== $id) {
                return ['sucesso' => false, 'mensagem' => 'Já existe um fornecedor com esse CNPJ.'];
            }
        }

        if ($id !== null && $this->fornecedorModel->buscarPorId($id) === null) {
            return ['sucesso' => false, 'mensagem' => 'Fornecedor não encontrado.'];
        }

        if ($id === null) {
            $this->fornecedorModel->criar($dados);
            return ['sucesso' => true, 'mensagem' => 'Fornecedor cadastrado com sucesso.'];
        }

        $this->fornecedorModel->atualizar($id, $dados);
        return ['sucesso' => true, 'mensagem' => 'Fornecedor atualizado com sucesso.'];
    }

    public function excluir(int $id): array
    {
        if (!$this->fornecedorModel->inativar($id)) {
            return ['sucesso' => false, 'mensagem' => 'Fornecedor não encontrado.'];
        }

        return ['sucesso' => true, 'mensagem' => 'Fornecedor excluído com sucesso.'];
    }
}
