<?php $editando = $cliente !== null; ?>

<div class="pagina-topo">
    <div>
        <p class="pagina-topo__eyebrow"><a href="clientes.php">&larr; Clientes</a></p>
        <h1><?= $editando ? 'Editar cliente' : 'Novo cliente' ?></h1>
    </div>
</div>

<?= alertaFlash() ?>

<form method="POST" action="clientes.php?acao=salvar" class="form">
    <?= campoCsrf() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
    <?php endif; ?>

    <section class="bloco-form">
        <div class="campos campos--2">
            <div class="campo campo--cheio">
                <label for="nome">Nome completo ou razão social</label>
                <input type="text" id="nome" name="nome" required
                       value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" placeholder="Só pessoa física"
                       value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" placeholder="Só pessoa jurídica"
                       value="<?= htmlspecialchars($cliente['cnpj'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" inputmode="tel"
                       value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($cliente['email'] ?? '') ?>">
            </div>
            <div class="campo campo--cheio">
                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco"
                       value="<?= htmlspecialchars($cliente['endereco'] ?? '') ?>">
            </div>
        </div>
    </section>

    <div class="acoes-form">
        <button type="submit" class="btn btn-primario">Salvar cliente</button>
        <a href="clientes.php" class="btn btn-secundario">Cancelar</a>
    </div>
</form>

<?php if ($editando): ?>
    <section class="painel" style="margin-top: 36px;">
        <div class="painel__topo">
            <h2>Histórico de compras</h2>
        </div>

        <?php if (empty($historico)): ?>
            <div class="estado-vazio">Nenhuma compra concluída para este cliente ainda.</div>
        <?php else: ?>
            <div class="tabela-wrap">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Data</th>
                            <th class="num">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $compra): ?>
                            <tr>
                                <td class="cel-principal">#<?= (int) $compra['id'] ?></td>
                                <td><?= date('d/m/Y', strtotime($compra['criado_em'])) ?></td>
                                <td class="num">R$ <?= moeda($compra['valor_total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
