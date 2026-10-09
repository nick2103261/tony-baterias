<?php
require_once __DIR__ . '/../../helpers/icones.php'; 
?>

<p style="font-size: 15px; color: var(--tony-cinza-texto); margin-bottom: 20px;">
    Bem-vindo, <strong style="color: var(--tony-preto);"><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong>
</p>

<div class="grade-resumo">
    <div class="cartao-resumo">
        <div class="cartao-resumo__icone"><?= icone('produtos', 18) ?></div>
        <span class="cartao-resumo__rotulo">Produtos cadastrados</span>
        <span class="cartao-resumo__valor"><?= (int) $resumo['total_produtos'] ?></span>
    </div>
    <div class="cartao-resumo">
        <div class="cartao-resumo__icone"><?= icone('estoque', 18) ?></div>
        <span class="cartao-resumo__rotulo">Valor total em estoque</span>
        <span class="cartao-resumo__valor">R$ <?= number_format($resumo['valor_estoque'], 2, ',', '.') ?></span>
    </div>
    <?php if (($_SESSION['usuario_perfil'] ?? null) === 'admin'): ?>
    <div class="cartao-resumo">
        <div class="cartao-resumo__icone"><?= icone('cifrao', 18) ?></div>
        <span class="cartao-resumo__rotulo">Vendido este mês</span>
        <span class="cartao-resumo__valor">R$ <?= number_format($resumo['total_vendas_mes'], 2, ',', '.') ?></span>
    </div>
    <div class="cartao-resumo">
        <div class="cartao-resumo__icone"><?= icone('grafico', 18) ?></div>
        <span class="cartao-resumo__rotulo">Ticket médio do mês</span>
        <span class="cartao-resumo__valor">R$ <?= number_format($resumo['ticket_medio_mes'], 2, ',', '.') ?></span>
    </div>
    <?php endif; ?>
</div>