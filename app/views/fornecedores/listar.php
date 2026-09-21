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
        <h2>Fornecedores cadastrados</h2>
        <a href="fornecedores.php?acao=form" class="btn btn-primario">
            <?= icone('mais', 16) ?> Novo fornecedor
        </a>
    </div>

    <?php if (empty($fornecedores)): ?>
        <div class="estado-vazio">
            <?= icone('fornecedores', 32) ?>
            <p>Nenhum fornecedor cadastrado ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Contato</th>
                    <th>CNPJ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores as $fornecedor): ?>
                    <tr>
                        <td><?= htmlspecialchars($fornecedor['nome']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['contato'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($fornecedor['cnpj'] ?? '—') ?></td>
                        <td class="acoes">
                            <a href="fornecedores.php?acao=form&id=<?= $fornecedor['id'] ?>" title="Editar">
                                <?= icone('editar', 16) ?>
                            </a>
                            <a href="fornecedores.php?acao=excluir&id=<?= $fornecedor['id'] ?>"
                               title="Excluir"
                               class="acao-excluir"
                               onclick="return confirm('Tem certeza que deseja excluir este fornecedor?');">
                                <?= icone('excluir', 16) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
