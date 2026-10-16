<?php

class AuthController
{
    public function login(string $email, string $senha): void
    {
        $usuario = (new Usuario())->buscarPorEmail(trim($email));

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            $_SESSION['erro_login'] = 'E-mail ou senha inválidos.';
            redirecionar('login.php');
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nome']   = $usuario['nome'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];

        redirecionar('dashboard.php');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirecionar('login.php');
    }
}
