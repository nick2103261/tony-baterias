<?php

class ClienteController
{
    private Cliente $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    public function listar(): array
    {
        return $this->clienteModel->listarTodos();
    }

    public function buscar(int $id): ?array
    {
        return $this->clienteModel->buscarPorId($id);
    }

    public function historicoCompras(int $id): array
    {
        return $this->clienteModel->historicoCompras($id);
    }

    public function salvar(array $post, ?int $id): array
    {
        $nome     = trim($post['nome'] ?? '');
        $cpf      = trim($post['cpf'] ?? '');
        $cnpj     = trim($post['cnpj'] ?? '');
        $telefone = trim($post['telefone'] ?? '');
        $email    = trim($post['email'] ?? '');
        $endereco = trim($post['endereco'] ?? '');

        if ($nome === '') {
            return ['sucesso' => false, 'mensagem' => 'O nome do cliente é obrigatório.'];
        }

        if ($cpf !== '' && $cnpj !== '') {
            return ['sucesso' => false, 'mensagem' => 'Preencha apenas CPF ou CNPJ, não os dois.'];
        }

        $dados = [
            'nome'     => $nome,
            'cpf'      => $cpf !== '' ? $cpf : null,
            'cnpj'     => $cnpj !== '' ? $cnpj : null,
            'telefone' => $telefone !== '' ? $telefone : null,
            'email'    => $email !== '' ? $email : null,
            'endereco' => $endereco !== '' ? $endereco : null,
        ];

        try {
            if ($id === null) {
                $this->clienteModel->criar($dados);
                return ['sucesso' => true, 'mensagem' => 'Cliente cadastrado com sucesso.'];
            }

            $this->clienteModel->atualizar($id, $dados);
            return ['sucesso' => true, 'mensagem' => 'Cliente atualizado com sucesso.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['sucesso' => false, 'mensagem' => 'Já existe um cliente com esse CPF ou CNPJ.'];
            }
            throw $e;
        }
    }

    public function excluir(int $id): void
    {
        $this->clienteModel->inativar($id);
    }
}
