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
        if (!in_array($perfil, ['admin', 'usuario'])) {
            return ['sucesso' => false, 'mensagem' => 'Selecione um perfil válido.'];

        }
    }

}