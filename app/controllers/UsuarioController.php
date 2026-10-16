<?php

class UsuarioController
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function listar(): array
    {
        return $this->usuarioModel->listarTodos();
    }

    public function buscar(int $id): ?array
    {
        return $this->usuarioModel->buscarPorId($id);
    }

    public function salvar(array $post, ?int $id): array
    {
        $nome   = trim($post['nome'] ?? '');
        $email  = trim($post['email'] ?? '');
        $senha  = $post['senha'] ?? '';
        $perfil = $post['perfil'] ?? '';

        if ($nome === '') {
            return ['sucesso' => false, 'mensagem' => 'O nome do usuário é obrigatório.'];
        }

        if (!emailValido($email)) {
            return ['sucesso' => false, 'mensagem' => 'E-mail inválido.'];
        }

        if (!in_array($perfil, ['admin', 'vendedor'], true)) {
            return ['sucesso' => false, 'mensagem' => 'Selecione um perfil válido.'];
        }

        if ($senha !== '' && strlen($senha) < 6) {
            return ['sucesso' => false, 'mensagem' => 'A senha deve ter pelo menos 6 caracteres.'];
        }

        if ($id === null && $senha === '') {
            return ['sucesso' => false, 'mensagem' => 'A senha é obrigatória para novos usuários.'];
        }

        $dados = ['nome' => $nome, 'email' => $email, 'perfil' => $perfil];

        try {
            if ($id === null) {
                $this->usuarioModel->criar($dados + ['senha' => password_hash($senha, PASSWORD_DEFAULT)]);
                return ['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso.'];
            }

            $this->usuarioModel->atualizar($id, $dados);

            // Na edição a senha é opcional: só muda se algo foi digitado
            if ($senha !== '') {
                $this->usuarioModel->atualizarSenha($id, password_hash($senha, PASSWORD_DEFAULT));
            }

            return ['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['sucesso' => false, 'mensagem' => 'Já existe um usuário com esse e-mail.'];
            }
            throw $e;
        }
    }

    public function excluir(int $id): void
    {
        $this->usuarioModel->inativar($id);
    }
}
