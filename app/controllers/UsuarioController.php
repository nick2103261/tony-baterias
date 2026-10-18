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
        $nome   = campoTexto($post, 'nome');
        $email  = campoTexto($post, 'email');
        $senha  = campoTexto($post, 'senha', false);
        $perfil = campoTexto($post, 'perfil');

        if ($nome === '') {
            return ['sucesso' => false, 'mensagem' => 'O nome do usuário é obrigatório.'];
        }

        $erroTamanho = erroDeTamanho(['nome' => $nome, 'email' => $email], [
            'nome'  => [100, 'nome'],
            'email' => [150, 'e-mail'],
        ]);

        if ($erroTamanho !== null) {
            return ['sucesso' => false, 'mensagem' => $erroTamanho];
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

        // O bcrypt ignora tudo depois de 72 bytes, então senhas maiores seriam "cortadas" sem aviso
        if (strlen($senha) > 72) {
            return ['sucesso' => false, 'mensagem' => 'A senha deve ter no máximo 72 caracteres.'];
        }

        if ($id === null && $senha === '') {
            return ['sucesso' => false, 'mensagem' => 'A senha é obrigatória para novos usuários.'];
        }

        if ($id !== null) {
            $atual = $this->usuarioModel->buscarPorId($id);

            if ($atual === null) {
                return ['sucesso' => false, 'mensagem' => 'Usuário não encontrado.'];
            }

            // Sempre precisa sobrar um administrador para gerenciar o sistema
            if ($atual['perfil'] === 'admin' && $perfil !== 'admin' && $this->usuarioModel->contarAdminsAtivos() <= 1) {
                return ['sucesso' => false, 'mensagem' => 'Não é possível rebaixar o único administrador.'];
            }
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

    public function excluir(int $id, int $usuarioLogadoId): array
    {
        $usuario = $this->usuarioModel->buscarPorId($id);

        if ($usuario === null) {
            return ['sucesso' => false, 'mensagem' => 'Usuário não encontrado.'];
        }

        if ($id === $usuarioLogadoId) {
            return ['sucesso' => false, 'mensagem' => 'Você não pode excluir o seu próprio usuário.'];
        }

        if ($usuario['perfil'] === 'admin' && $this->usuarioModel->contarAdminsAtivos() <= 1) {
            return ['sucesso' => false, 'mensagem' => 'Não é possível excluir o único administrador.'];
        }

        $this->usuarioModel->inativar($id);

        return ['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso.'];
    }
}
