<div class="pagina-topo">
    <div>
        <h1>Fornecedores <span class="pagina-topo__contagem"><?= count($fornecedores) ?></span></h1>
    </div>
    <div class="pagina-topo__acoes">
        <a href="fornecedores.php?acao=form" class="btn btn-primario"><?= icone('mais', 16) ?> Novo fornecedor</a>
    </div>
</div>

<?= alertaFlash() ?>

<?php if (empty($fornecedores)): ?>
    <div class="estado-vazio">Nenhum fornecedor cadastrado.</div>
<?php else: ?>
    <section class="painel">
        <div class="tabela-wrap">
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
                            <td class="cel-principal"><?= htmlspecialchars($fornecedor['nome']) ?></td>
                            <td><?= htmlspecialchars($fornecedor['contato'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($fornecedor['cnpj'] ?? '—') ?></td>
                            <td class="cel-acoes">
                                <div class="acoes">
                                    <a href="fornecedores.php?acao=form&id=<?= $fornecedor['id'] ?>" class="acao" title="Editar">
                                        <?= icone('editar', 16) ?><span>Editar</span>
                                    </a>
                                    <?php if (ehAdmin()): ?>
                                        <?= botaoAcao('fornecedores.php?acao=excluir', ['id' => $fornecedor['id']], 'excluir', 'Excluir', 'acao--perigo', 'Excluir este fornecedor?') ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
