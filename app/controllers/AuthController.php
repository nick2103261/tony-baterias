<?php

class AuthController
{
    // Hash sem dono, só para o tempo de resposta ser o mesmo com e-mail existente ou inexistente
    private const HASH_FALSO = '$2y$10$abcdefghijklmnopqrstuuJQnKfZJvZ4mG6rF1b0tJ8mX9y1Q2wQe';

    public function login(string $email, string $senha): void
    {
        $email      = trim($email);
        $ip         = $_SERVER['REMOTE_ADDR'] ?? '';
        $tentativas = new TentativaLogin();

        if ($tentativas->bloqueado($email, $ip)) {
            $_SESSION['erro_login'] = 'Muitas tentativas de login. Aguarde 15 minutos e tente de novo.';
            redirecionar('login.php');
        }

        $usuario = (new Usuario())->buscarPorEmail($email);

        if (!password_verify($senha, $usuario['senha'] ?? self::HASH_FALSO) || !$usuario) {
            $tentativas->registrarFalha($email, $ip);
            $_SESSION['erro_login'] = 'E-mail ou senha inválidos.';
            redirecionar('login.php');
        }

        $tentativas->limpar($email, $ip);
        session_regenerate_id(true);

        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nome']   = $usuario['nome'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];

        redirecionar('dashboard.php');
    }

    public function logout(): void
    {
        reiniciarSessao();
        redirecionar('login.php');
    }
}
