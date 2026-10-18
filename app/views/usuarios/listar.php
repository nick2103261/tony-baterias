<div class="pagina-topo">
    <div>
        <h1>Usuários <span class="pagina-topo__contagem"><?= count($usuarios) ?></span></h1>
    </div>
    <div class="pagina-topo__acoes">
        <a href="usuarios.php?acao=form" class="btn btn-primario"><?= icone('mais', 16) ?> Novo usuário</a>
    </div>
</div>

<?= alertaFlash() ?>

<?php if (empty($usuarios)): ?>
    <div class="estado-vazio">Nenhum usuário cadastrado.</div>
<?php else: ?>
    <section class="painel">
        <div class="tabela-wrap">
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
                            <td class="cel-principal"><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td>
                                <span class="badge <?= $usuario['perfil'] === 'admin' ? 'badge-andamento' : 'badge-neutro' ?>">
                                    <?= $usuario['perfil'] === 'admin' ? 'administrador' : 'vendedor' ?>
                                </span>
                            </td>
                            <td class="cel-acoes">
                                <div class="acoes">
                                    <a href="usuarios.php?acao=form&id=<?= $usuario['id'] ?>" class="acao" title="Editar e trocar senha">
                                        <?= icone('editar', 16) ?><span>Editar</span>
                                    </a>
                                    <a href="usuarios.php?acao=excluir&id=<?= $usuario['id'] ?>"
                                       class="acao acao--perigo"
                                       title="Excluir"
                                       onclick="return confirm('Excluir este usuário?');">
                                        <?= icone('excluir', 16) ?><span>Excluir</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
