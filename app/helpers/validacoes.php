<?php

function emailValido(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Aceita com ou sem pontuação; confere os dois dígitos verificadores
function cpfValido(string $cpf): bool
{
    if (!preg_match('/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/', $cpf)) {
        return false;
    }

    $cpf = preg_replace('/\D/', '', $cpf);

    // 111.111.111-11 e parecidos passam na conta, mas não são CPF válido
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($posicao = 9; $posicao < 11; $posicao++) {
        $soma = 0;

        for ($i = 0; $i < $posicao; $i++) {
            $soma += (int) $cpf[$i] * (($posicao + 1) - $i);
        }

        if ((int) $cpf[$posicao] !== ((10 * $soma) % 11) % 10) {
            return false;
        }
    }

    return true;
}

// Aceita com ou sem pontuação; confere os dois dígitos verificadores
function cnpjValido(string $cnpj): bool
{
    if (!preg_match('/^\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2}$/', $cnpj)) {
        return false;
    }

    $cnpj = preg_replace('/\D/', '', $cnpj);

    if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
        return false;
    }

    foreach ([12, 13] as $posicao) {
        $soma = 0;
        $peso = $posicao - 7;

        for ($i = 0; $i < $posicao; $i++) {
            $soma += (int) $cnpj[$i] * $peso;
            $peso = $peso > 2 ? $peso - 1 : 9;
        }

        $resto = $soma % 11;

        if ((int) $cnpj[$posicao] !== ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
    }

    return true;
}
