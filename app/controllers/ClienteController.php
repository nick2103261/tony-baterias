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
            $dados[$campo] = textoOuNulo(campoTexto($post, $campo));
        }

        if ($dados['nome'] === null) {
            return ['sucesso' => false, 'mensagem' => 'O nome do cliente é obrigatório.'];
        }

        $erroTamanho = erroDeTamanho($dados, [
            'nome'     => [150, 'nome'],
            'telefone' => [20, 'telefone'],
            'email'    => [150, 'e-mail'],
            'endereco' => [255, 'endereço'],
        ]);

        if ($erroTamanho !== null) {
            return ['sucesso' => false, 'mensagem' => $erroTamanho];
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

        // Grava sempre com a mesma pontuação, para o mesmo documento não ser cadastrado duas vezes
        if ($dados['cpf'] !== null) {
            $dados['cpf'] = formatarCpf($dados['cpf']);
        }

        if ($dados['cnpj'] !== null) {
            $dados['cnpj'] = formatarCnpj($dados['cnpj']);
        }

        if ($id !== null && $this->clienteModel->buscarPorId($id) === null) {
            return ['sucesso' => false, 'mensagem' => 'Cliente não encontrado.'];
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

    public function excluir(int $id): array
    {
        if (!$this->clienteModel->inativar($id)) {
            return ['sucesso' => false, 'mensagem' => 'Cliente não encontrado.'];
        }

        return ['sucesso' => true, 'mensagem' => 'Cliente excluído com sucesso.'];
    }
}
