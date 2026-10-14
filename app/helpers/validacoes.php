<?php

/**
 * Funções de validação usadas pelos controllers (Semana 5).
 * Funções soltas, no mesmo estilo de app/helpers/icones.php.
 * São carregadas por config/bootstrap.php, depois de auth.php.
 */

/**
 * Confere o formato do e-mail (ex.: nome@dominio.com).
 */
function emailValido(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida um CPF.
 * - aceita com ou sem pontuação (123.456.789-09 ou 12345678909);
 * - exige 11 dígitos;
 * - rejeita sequências repetidas (111.111.111-11, 000.000.000-00...);
 * - confere os dois dígitos verificadores.
 */
function cpfValido(string $cpf): bool
{
    $cpf = trim($cpf);

    // Formato aceito: 11 dígitos, com pontuação opcional. Rejeita letras e lixo.
    if (!preg_match('/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/', $cpf)) {
        return false;
    }

    $cpf = preg_replace('/\D/', '', $cpf);

    // Sequência de um mesmo dígito repetido (passaria na conta, mas não é CPF válido)
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // Dígitos verificadores: o 10º usa pesos 10..2; o 11º usa pesos 11..2
    for ($posicao = 9; $posicao < 11; $posicao++) {
        $soma = 0;
        for ($i = 0; $i < $posicao; $i++) {
            $soma += (int) $cpf[$i] * (($posicao + 1) - $i);
        }

        $digito = ((10 * $soma) % 11) % 10;

        if ((int) $cpf[$posicao] !== $digito) {
            return false;
        }
    }

    return true;
}

/**
 * Valida um CNPJ.
 * - aceita com ou sem pontuação (12.345.678/0001-95 ou 12345678000195);
 * - exige 14 dígitos;
 * - rejeita sequências repetidas;
 * - confere os dois dígitos verificadores.
 */
function cnpjValido(string $cnpj): bool
{
    $cnpj = trim($cnpj);

    // Formato aceito: 14 dígitos, com pontuação opcional. Rejeita letras e lixo.
    if (!preg_match('/^\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2}$/', $cnpj)) {
        return false;
    }

    $cnpj = preg_replace('/\D/', '', $cnpj);

    if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
        return false;
    }

    // Pesos oficiais do cálculo: primeiro dígito (12 pesos) e segundo dígito (13 pesos)
    $pesosPorPosicao = [
        12 => [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
        13 => [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
    ];

    foreach ($pesosPorPosicao as $posicao => $pesos) {
        $soma = 0;
        foreach ($pesos as $i => $peso) {
            $soma += (int) $cnpj[$i] * $peso;
        }

        $resto  = $soma % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;

        if ((int) $cnpj[$posicao] !== $digito) {
            return false;
        }
    }

    return true;
}
