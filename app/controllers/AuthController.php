<?php
class AuthController
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function login(string $email, string $senha): void
    {
        $email = trim($email);

        if ($email === '' || $senha === '') {
            $this->redirecionarComErro('Preencha e-mail e senha.');
        }

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if (!$usuario || $senha !== $usuario['senha']) {
            $this->redirecionarComErro('E-mail ou senha inválidos.');
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id']    = $usuario['id'];
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];

        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    private function redirecionarComErro(string $mensagem): void
    {
        $_SESSION['erro_login'] = $mensagem;
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}
