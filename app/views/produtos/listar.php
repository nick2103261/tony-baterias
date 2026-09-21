<?php
require_once __DIR__ . '/../../helpers/icones.php';

$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
?>

<?php if ($mensagem): ?>
    <div class="alerta alerta-<?= $mensagem['tipo'] ?>">
        <?= icone($mensagem['tipo'] === 'sucesso' ? 'sucesso' : 'alerta') ?>
        <?= htmlspecialchars($mensagem['texto']) ?>
    </div>
<?php endif; ?>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Produtos cadastrados</h2>
        <div class="d-flex gap-3 align-items-center">
            <a href="categorias.php" class="cartao__acao-secundaria">Gerenciar categorias</a>
            <a href="produtos.php?acao=form" class="btn btn-primario">
                <?= icone('mais', 16) ?> Novo produto
            </a>
        </div>
    </div>

    <?php if (empty($produtos)): ?>
        <div class="estado-vazio">
            <?= icone('produtos', 32) ?>
            <p>Nenhum produto cadastrado ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Marca</th>
                    <th>SKU</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Garantia</th>
                    <th>Estoque</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= htmlspecialchars($produto['marca'] ?? '—') ?></td>
                        <td class="valor-mono"><?= htmlspecialchars($produto['sku'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($produto['categoria_nome'] ?? '—') ?></td>
                        <td>R$ <?= number_format((float) $produto['preco'], 2, ',', '.') ?></td>
                        <td><?= (int) $produto['garantia_meses'] > 0 ? (int) $produto['garantia_meses'] . ' meses' : '—' ?></td>
                        <td>
                            <?= (int) $produto['estoque_atual'] ?>
                            <?php if ($produto['estoque_atual'] <= $produto['estoque_minimo']): ?>
                                <span class="badge badge-alerta">baixo</span>
                            <?php else: ?>
                                <span class="badge badge-ok">ok</span>
                            <?php endif; ?>
                        </td>
                        <td class="acoes">
                            <a href="produtos.php?acao=form&id=<?= $produto['id'] ?>" title="Editar">
                                <?= icone('editar', 16) ?>
                            </a>
                            <a href="produtos.php?acao=excluir&id=<?= $produto['id'] ?>"
                               title="Excluir"
                               class="acao-excluir"
                               onclick="return confirm('Tem certeza que deseja excluir este produto?');">
                                <?= icone('excluir', 16) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
