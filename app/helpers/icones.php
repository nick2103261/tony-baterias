<?php

function icone(string $nome, int $tamanho = 18): string
{
    $tracos = [
        'dashboard' => '
            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
        ',
        'produtos' => '
            <path d="M12 3 4 7v10l8 4 8-4V7z"/>
            <path d="M4 7l8 4 8-4"/>
            <path d="M12 11v10"/>
        ',
        'clientes' => '
            <circle cx="9" cy="8" r="3"/>
            <path d="M3.5 20a5.5 5.5 0 0 1 11 0"/>
            <circle cx="17" cy="9" r="2.4"/>
            <path d="M15 20a4.3 4.3 0 0 1 6.5-3.7"/>
        ',
        'estoque' => '
            <rect x="5" y="4" width="14" height="17" rx="2"/>
            <rect x="9" y="2" width="6" height="3" rx="1"/>
            <path d="M8.5 11h7"/>
            <path d="M8.5 14.5h7"/>
            <path d="M8.5 18h4.5"/>
        ',
        'sair' => '
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <path d="m16 17 5-5-5-5"/>
            <path d="M21 12H9"/>
        ',
        'pessoa' => '
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 20a8 8 0 0 1 16 0"/>
        ',
        'alerta' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7.5v6"/>
            <circle cx="12" cy="16.5" r="0.9" fill="currentColor" stroke="none"/>
        ',
        'sucesso' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m7.5 12.5 3 3 6-6.5"/>
        ',
        'mais' => '
            <path d="M12 5v14M5 12h14"/>
        ',
        'editar' => '
            <path d="M4 20h4.2L18.5 9.7a2.2 2.2 0 0 0-3.1-3.1L5.1 16.9 4 20Z"/>
            <path d="M13.8 8 16 10.2"/>
        ',
        'excluir' => '
            <path d="M5 7h14"/>
            <path d="M9.5 7V5.2a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1V7"/>
            <path d="M7.2 7 8 19a1.4 1.4 0 0 0 1.4 1.3h5.2A1.4 1.4 0 0 0 16 19l.8-12"/>
            <path d="M10.3 10.5v6M13.7 10.5v6"/>
        ',
        'cancelar' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="m9 9 6 6M15 9l-6 6"/>
        ',
        'pedidos' => '
            <path d="M6 3h9l3 3v15H6z"/>
            <path d="M15 3v3h3"/>
            <path d="M9 11h6M9 14.5h6M9 18h3"/>
        ',
        'cifrao' => '
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 6.5v11M9.3 9.3c0-1.3 1.2-2.3 2.7-2.3s2.7.9 2.7 2c0 2.8-5.4 1.5-5.4 4.3 0 1.1 1.2 2 2.7 2s2.7-1 2.7-2.3"/>
        ',
        'fornecedores' => '
            <rect x="2" y="7" width="12" height="9" rx="1"/>
            <path d="M14 10h4l4 3.5V16h-8"/>
            <circle cx="7" cy="18" r="1.7"/>
            <circle cx="17.5" cy="18" r="1.7"/>
        ',
        'relatorios' => '
            <path d="M6 3h9l3 3v15H6z"/>
            <path d="M15 3v3h3"/>
            <path d="M9 12h6M9 15.5h6M9 9h3"/>
        ',
        'imprimir' => '
            <path d="M6 9V3h12v6"/>
            <rect x="4" y="9" width="16" height="8" rx="1"/>
            <path d="M6 14h12v7H6z"/>
        ',
        'menu' => '
            <path d="M4 7h16M4 12h16M4 17h10"/>
        ',
    ];

    return sprintf(
        '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%2$s</svg>',
        $tamanho,
        trim($tracos[$nome])
    );
}

// Aviso que as páginas deixam em $_SESSION['mensagem'] (aparece uma vez só)
function alertaFlash(): string
{
    $mensagem = $_SESSION['mensagem'] ?? null;
    unset($_SESSION['mensagem']);

    if (!$mensagem) {
        return '';
    }

    $tipo = $mensagem['sucesso'] ? 'sucesso' : 'erro';

    return '<div class="alerta alerta-' . $tipo . '">'
        . icone($mensagem['sucesso'] ? 'sucesso' : 'alerta')
        . '<span>' . htmlspecialchars($mensagem['mensagem']) . '</span></div>';
}

function moeda($valor): string
{
    return number_format((float) $valor, 2, ',', '.');
}

const FORMAS_PAGAMENTO = [
    'dinheiro'       => 'Dinheiro',
    'pix'            => 'Pix',
    'cartao_credito' => 'Cartão de crédito',
    'cartao_debito'  => 'Cartão de débito',
    'boleto'         => 'Boleto',
];

function badgeStatus(string $status): string
{
    $badges = [
        'pendente'  => ['badge-andamento', 'pendente'],
        'concluido' => ['badge-ok', 'concluído'],
        'cancelado' => ['badge-alerta', 'cancelado'],
    ];

    return '<span class="badge ' . $badges[$status][0] . '">' . $badges[$status][1] . '</span>';
}
