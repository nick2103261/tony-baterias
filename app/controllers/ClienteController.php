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
        // Campos em branco viram NULL no banco
        $dados = [];
        foreach (['nome', 'cpf', 'cnpj', 'telefone', 'email', 'endereco'] as $campo) {
            $dados[$campo] = trim($post[$campo] ?? '') ?: null;
        }

        if ($dados['nome'] === null) {
            return ['sucesso' => false, 'mensagem' => 'O nome do cliente é obrigatório.'];
        }

        if ($dados['cpf'] !== null && $dados['cnpj'] !== null) {
            return ['sucesso' => false, 'mensagem' => 'Preencha apenas CPF ou CNPJ, não os dois.'];
        }

        if ($dados['cpf'] !== null && !cpfValido($dados['cpf'])) {
            return ['sucesso' => false, 'mensagem' => 'CPF inválido.'];
        }

        if ($dados['cnpj'] !== null && !cnpjValido($dados['cnpj'])) {
            return ['sucesso' => false, 'mensagem' => 'CNPJ inválido.'];
        }

        if ($dados['email'] !== null && !emailValido($dados['email'])) {
            return ['sucesso' => false, 'mensagem' => 'E-mail inválido.'];
        }

        if ($dados['cpf'] === null && $dados['cnpj'] === null) {
            return ['sucesso' => false, 'mensagem' => 'Informe o CPF ou o CNPJ do cliente — esse dado é obrigatório para emissão de nota fiscal.'];
        }

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
