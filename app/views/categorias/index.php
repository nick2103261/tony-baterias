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

<div class="cartao" style="max-width: 560px;">
    <div class="cartao__cabecalho">
        <h2>Nova categoria</h2>
    </div>
    <form method="POST" action="categorias.php?acao=salvar" class="d-flex gap-3 align-items-end flex-wrap">
        <div class="campo" style="flex: 1; min-width: 200px; margin-bottom: 0;">
            <label for="nome">Nome da categoria</label>
            <input type="text" id="nome" name="nome" required placeholder="Ex: Baterias moto">
        </div>
        <button type="submit" class="btn btn-primario" style="width: auto;">
            <?= icone('mais', 16) ?> Adicionar
        </button>
    </form>
</div>

<div class="cartao" style="max-width: 560px;">
    <div class="cartao__cabecalho">
        <h2>Categorias cadastradas</h2>
        <a href="produtos.php" class="cartao__acao-secundaria">Voltar para produtos</a>
    </div>

    <?php if (empty($categorias)): ?>
        <div class="estado-vazio">
            <p>Nenhuma categoria cadastrada ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr><th>Nome</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= htmlspecialchars($categoria['nome']) ?></td>
                        <td class="acoes">
                            <a href="categorias.php?acao=excluir&id=<?= $categoria['id'] ?>"
                               title="Excluir"
                               class="acao-excluir"
                               onclick="return confirm('Excluir esta categoria? Produtos que a usam ficam sem categoria.');">
                                <?= icone('excluir', 16) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
