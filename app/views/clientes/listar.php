<div class="pagina-topo">
    <div>
        <h1>Clientes <span class="pagina-topo__contagem"><?= count($clientes) ?></span></h1>
    </div>
    <div class="pagina-topo__acoes">
        <a href="clientes.php?acao=form" class="btn btn-primario"><?= icone('mais', 16) ?> Novo cliente</a>
    </div>
</div>

<?= alertaFlash() ?>

<?php if (empty($clientes)): ?>
    <div class="estado-vazio">Nenhum cliente cadastrado.</div>
<?php else: ?>
    <section class="painel">
        <div class="tabela-wrap">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF / CNPJ</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td class="cel-principal"><?= htmlspecialchars($cliente['nome']) ?></td>
                            <td><?= htmlspecialchars($cliente['cpf'] ?? $cliente['cnpj'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($cliente['telefone'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($cliente['email'] ?? '—') ?></td>
                            <td class="cel-acoes">
                                <div class="acoes">
                                    <a href="clientes.php?acao=form&id=<?= $cliente['id'] ?>" class="acao" title="Editar">
                                        <?= icone('editar', 16) ?><span>Editar</span>
                                    </a>
                                    <?php if (ehAdmin()): ?>
                                        <?= botaoAcao('clientes.php?acao=excluir', ['id' => $cliente['id']], 'excluir', 'Excluir', 'acao--perigo', 'Excluir este cliente?') ?>
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
