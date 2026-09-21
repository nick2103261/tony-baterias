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
        <h2>Registrar movimentação</h2>
    </div>
    <form method="POST" action="estoque.php" id="form-movimentacao">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <div class="campo" style="margin-bottom: 0;">
                    <label for="produto_id">Produto *</label>
                    <select id="produto_id" name="produto_id" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($produtos as $produto): ?>
                            <option value="<?= $produto['id'] ?>"><?= htmlspecialchars($produto['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="campo" style="margin-bottom: 0;">
                    <label for="tipo">Tipo *</label>
                    <select id="tipo" name="tipo" required>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="campo" style="margin-bottom: 0;">
                    <label for="quantidade">Quantidade *</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" required>
                </div>
            </div>
            <div class="col-md-2" id="campo-fornecedor">
                <div class="campo" style="margin-bottom: 0;">
                    <label for="fornecedor_id">Fornecedor</label>
                    <select id="fornecedor_id" name="fornecedor_id">
                        <option value="">Não informado</option>
                        <?php foreach ($fornecedores as $fornecedor): ?>
                            <option value="<?= $fornecedor['id'] ?>"><?= htmlspecialchars($fornecedor['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="campo" style="margin-bottom: 0;">
                    <label for="observacao">Observação</label>
                    <input type="text" id="observacao" name="observacao" placeholder="Ex: compra, ajuste">
                </div>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primario w-100">
                    <?= icone('mais', 16) ?>
                </button>
            </div>
        </div>
    </form>
</div>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Estoque atual</h2>
    </div>

    <?php if (empty($produtos)): ?>
        <div class="estado-vazio">
            <?= icone('estoque', 32) ?>
            <p>Nenhum produto cadastrado ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Estoque atual</th>
                    <th>Estoque mínimo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= (int) $produto['estoque_atual'] ?></td>
                        <td><?= (int) $produto['estoque_minimo'] ?></td>
                        <td>
                            <?php if ($produto['estoque_atual'] <= $produto['estoque_minimo']): ?>
                                <span class="badge badge-alerta"><?= icone('alerta', 12) ?> baixo</span>
                            <?php else: ?>
                                <span class="badge badge-ok">ok</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="cartao">
    <div class="cartao__cabecalho">
        <h2>Últimas movimentações</h2>
    </div>

    <?php if (empty($movimentacoes)): ?>
        <div class="estado-vazio">
            <p>Nenhuma movimentação registrada ainda.</p>
        </div>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Qtd.</th>
                    <th>Fornecedor</th>
                    <th>Usuário</th>
                    <th>Observação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimentacoes as $mov): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($mov['criado_em'])) ?></td>
                        <td><?= htmlspecialchars($mov['produto_nome']) ?></td>
                        <td>
                            <?php if ($mov['tipo'] === 'entrada'): ?>
                                <span class="badge badge-ok">entrada</span>
                            <?php else: ?>
                                <span class="badge badge-neutro">saída</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int) $mov['quantidade'] ?></td>
                        <td><?= htmlspecialchars($mov['fornecedor_nome'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($mov['usuario_nome']) ?></td>
                        <td><?= htmlspecialchars($mov['observacao'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
const selectTipo = document.getElementById('tipo');
const campoFornecedor = document.getElementById('campo-fornecedor');

function atualizarCampoFornecedor() {
    campoFornecedor.style.display = selectTipo.value === 'entrada' ? '' : 'none';
}

selectTipo.addEventListener('change', atualizarCampoFornecedor);
atualizarCampoFornecedor();
</script>
