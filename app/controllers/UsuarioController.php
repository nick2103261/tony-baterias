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

    public function salvar(array $post, ?int $id, int $usuarioId): array
    {
        $nome = trim($post['nome'] ?? '');
        $email = trim($post['email'] ?? '');
        $senha = $post['senha'] ?? '';
        $perfil = trim($post['perfil'] ?? '');

        if ($nome ===''){
            return ['sucesso' => false, 'mensagem' => 'O nome do usuário é obrigatório.'];

        }

        if ($email ===''){
            return ['sucesso' => false, 'mensagem' => 'O email do usuário é obrigatório.'];

        }

        if (!emailValido($email)) {
            return ['sucesso' => false, 'mensagem' => 'E-mail inválido.'];
        }

        if (!in_array($perfil, ['admin', 'vendedor'])) {
            return ['sucesso' => false, 'mensagem' => 'Selecione um perfil válido.'];

        }

        if ($senha !== '' && strlen($senha) < 6) {
            return ['sucesso' => false, 'mensagem' => 'A senha deve ter pelo menos 6 caracteres.'];
        }

        try {
            if ($id === null) {
                if ($senha === '') {
                    return ['sucesso' => false, 'mensagem' => 'A senha é obrigatória para novos usuários.'];

                }

                $dados = [
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => password_hash($senha, PASSWORD_DEFAULT),
                    'perfil' => $perfil,
                ];

                $novoId = $this->usuarioModel->criar($dados);

                $logModel = new LogAtividade();
                $logModel->registrar($usuarioId, 'Cadastrou usuário', 'usuario', $novoId);

                return ['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso.'];
            }
            
            $dados = [
                'nome'   => $nome,
                'email'  => $email,
                'perfil' => $perfil,
            ];

            $this->usuarioModel->atualizar($id, $dados);

            // Senha é opcional na edição: só altera se algo foi digitado
            if ($senha !== '') {
                $this->usuarioModel->atualizarSenha($id, password_hash($senha, PASSWORD_DEFAULT));

                $logModel = new LogAtividade();
                $logModel->registrar($usuarioId, 'Alterou a senha do usuário', 'usuario', $id);

                return ['sucesso' => true, 'mensagem' => 'Usuário atualizado e senha alterada com sucesso.'];
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