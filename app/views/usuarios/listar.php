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
        <h2>Usuários cadastrados</h2>
        <a href="usuarios.php?acao=form" class="btn btn-primario">
            <?= icone('mais', 16) ?> Novo usuário
        </a>
    </div>

    <?php if (empty($usuarios)): ?>
        <div class="estado-vazio">
            <?= icone('pessoa', 32) ?>
            <p>Nenhum usuário cadastrado ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= $usuario['perfil'] === 'admin' ? 'Administrador' : 'Vendedor' ?></td>
                        <td class="acoes">
                            <a href="usuarios.php?acao=form&id=<?= $usuario['id'] ?>" title="Editar">
                                <?= icone('editar', 16) ?>
                            </a>
                            <a href="usuarios.php?acao=excluir&id=<?= $usuario['id'] ?>"
                               title="Excluir"
                               class="acao-excluir"
                               onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                                <?= icone('excluir', 16) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>