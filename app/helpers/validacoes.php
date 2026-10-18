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

// Maior quantidade aceita em estoque, itens de pedido e movimentações
const LIMITE_QUANTIDADE = 1000000;

// Grava sempre no mesmo formato, para "52998224725" e "529.982.247-25" não virarem dois cadastros
function formatarCpf(string $cpf): string
{
    $d = preg_replace('/\D/', '', $cpf);

    return substr($d, 0, 3) . '.' . substr($d, 3, 3) . '.' . substr($d, 6, 3) . '-' . substr($d, 9, 2);
}

function formatarCnpj(string $cnpj): string
{
    $d = preg_replace('/\D/', '', $cnpj);

    return substr($d, 0, 2) . '.' . substr($d, 2, 3) . '.' . substr($d, 5, 3) . '/' . substr($d, 8, 4) . '-' . substr($d, 12, 2);
}

// Lê um campo de texto do formulário; ignora o que não for texto (ex.: nome[]=x) e, por padrão, apara os espaços
function campoTexto(array $dados, string $campo, bool $aparar = true): string
{
    $valor = $dados[$campo] ?? '';

    if (!is_string($valor)) {
        return '';
    }

    return $aparar ? trim($valor) : $valor;
}

// Texto vazio vira NULL no banco ("0" continua sendo um valor válido)
function textoOuNulo(string $texto): ?string
{
    return $texto === '' ? null : $texto;
}

// $limites: campo => [tamanho máximo, nome mostrado na mensagem]. Devolve a mensagem do primeiro campo grande demais.
function erroDeTamanho(array $dados, array $limites): ?string
{
    foreach ($limites as $campo => [$maximo, $rotulo]) {
        if (mb_strlen((string) ($dados[$campo] ?? '')) > $maximo) {
            return "O campo {$rotulo} aceita no máximo {$maximo} caracteres.";
        }
    }

    return null;
}
