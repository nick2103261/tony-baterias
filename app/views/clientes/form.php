<?php
require_once __DIR__ . '/../../helpers/icones.php'; 

$editando = $cliente !== null;
?>

<div class="cartao" style="max-width: 680px;">
    <div class="cartao__cabecalho">
        <h2><?= $editando ? 'Editar cliente' : 'Novo cliente' ?></h2>
    </div>

    <form method="POST" action="clientes.php?acao=salvar">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
        <?php endif; ?>

        <div class="campo">
            <label for="nome">Nome completo / Razão social *</label>
            <input type="text" id="nome" name="nome" required
                   value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>">
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="campo">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="Somente se pessoa física"
                           value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="campo">
                    <label for="cnpj">CNPJ</label>
                    <input type="text" id="cnpj" name="cnpj" placeholder="Somente se pessoa jurídica"
                           value="<?= htmlspecialchars($cliente['cnpj'] ?? '') ?>">
                </div>
            </div>
        </div>
        <p style="font-size: 12px; color: var(--tony-cinza-texto); margin-top: -10px; margin-bottom: 18px;">
            Preencha apenas um dos dois — pessoa física usa CPF, pessoa jurídica usa CNPJ.
        </p>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="campo">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone"
                           value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($cliente['email'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="campo">
            <label for="endereco">Endereço</label>
            <input type="text" id="endereco" name="endereco"
                   value="<?= htmlspecialchars($cliente['endereco'] ?? '') ?>">
        </div>

        <div class="d-flex gap-3 mt-2">
            <button type="submit" class="btn btn-primario" style="width: auto;">
                <?= icone('salvar', 16) ?> Salvar
            </button>
            <a href="clientes.php" class="btn" style="width: auto; background: #eee;">Cancelar</a>
        </div>
    </form>
</div>

<script>
(function () {
    const cpf = document.getElementById('cpf');
    const cnpj = document.getElementById('cnpj');

    function alternar() {
        cpf.disabled = cnpj.value.trim() !== '';
        cnpj.disabled = cpf.value.trim() !== '';
    }

    cpf.addEventListener('input', alternar);
    cnpj.addEventListener('input', alternar);
    alternar();
})();
</script>

<?php if ($editando): ?>
    <div class="cartao" style="max-width: 680px;">
        <div class="cartao__cabecalho">
            <h2>Histórico de compras</h2>
        </div>
        <?php if (empty($historico)): ?>
            <div class="estado-vazio">
                <p>Nenhuma compra concluída registrada para este cliente ainda.</p>
            </div>
        <?php else: ?>
            <table class="tabela">
                <thead>
                    <tr><th>#</th><th>Data</th><th>Valor</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($historico as $compra): ?>
                        <tr>
                            <td>#<?= (int) $compra['id'] ?></td>
                            <td><?= date('d/m/Y', strtotime($compra['criado_em'])) ?></td>
                            <td>R$ <?= number_format((float) $compra['valor_total'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>
