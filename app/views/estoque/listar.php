<div class="pagina-topo">
    <div>
        <h1>Estoque</h1>
    </div>
</div>

<?= alertaFlash() ?>

<section class="painel">
    <div class="painel__topo">
        <h2>Registrar movimentação</h2>
    </div>
    <form method="POST" action="estoque.php" id="form-movimentacao" class="painel__corpo">
        <div class="campos" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));">
            <div class="campo">
                <label for="produto_id">Produto</label>
                <select id="produto_id" name="produto_id" required>
                    <option value="">Selecione…</option>
                    <?php foreach ($produtos as $produto): ?>
                        <option value="<?= $produto['id'] ?>"><?= htmlspecialchars($produto['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" required>
                    <option value="entrada">Entrada</option>
                    <option value="saida">Saída</option>
                </select>
            </div>
            <div class="campo">
                <label for="quantidade">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" min="1" required>
            </div>
            <div class="campo" id="campo-fornecedor">
                <label for="fornecedor_id">Fornecedor</label>
                <select id="fornecedor_id" name="fornecedor_id">
                    <option value="">Não informado</option>
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?= $fornecedor['id'] ?>"><?= htmlspecialchars($fornecedor['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="observacao">Observação</label>
                <input type="text" id="observacao" name="observacao" placeholder="Compra, ajuste, perda…">
            </div>
        </div>
        <button type="submit" class="btn btn-primario">Registrar</button>
    </form>
</section>

<div class="colunas colunas--5-7" style="margin-top: 28px;">
    <section class="painel">
        <div class="painel__topo">
            <h2>Estoque atual</h2>
            <span class="dica"><?= count($produtos) ?> produtos</span>
        </div>

        <?php if (empty($produtos)): ?>
            <div class="estado-vazio">Nenhum produto cadastrado.</div>
        <?php else: ?>
            <div class="tabela-wrap">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="num">Atual</th>
                            <th class="num">Mínimo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td class="cel-principal"><?= htmlspecialchars($produto['nome']) ?></td>
                                <td class="num"><?= (int) $produto['estoque_atual'] ?></td>
                                <td class="num suave"><?= (int) $produto['estoque_minimo'] ?></td>
                                <td>
                                    <?php if ($produto['estoque_atual'] <= $produto['estoque_minimo']): ?>
                                        <span class="badge badge-alerta">baixo</span>
                                    <?php else: ?>
                                        <span class="badge badge-ok">ok</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="painel">
        <div class="painel__topo">
            <h2>Últimas movimentações</h2>
        </div>

        <?php if (empty($movimentacoes)): ?>
            <div class="estado-vazio">Nada movimentado ainda.</div>
        <?php else: ?>
            <div class="tabela-wrap">
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Quando</th>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th class="num">Qtd.</th>
                            <th>Fornecedor</th>
                            <th>Quem</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimentacoes as $mov): ?>
                            <tr>
                                <td class="suave"><?= date('d/m H:i', strtotime($mov['criado_em'])) ?></td>
                                <td class="cel-principal"><?= htmlspecialchars($mov['produto_nome']) ?></td>
                                <td>
                                    <?php if ($mov['tipo'] === 'entrada'): ?>
                                        <span class="badge badge-ok">entrada</span>
                                    <?php else: ?>
                                        <span class="badge badge-neutro">saída</span>
                                    <?php endif; ?>
                                </td>
                                <td class="num"><?= (int) $mov['quantidade'] ?></td>
                                <td><?= htmlspecialchars($mov['fornecedor_nome'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($mov['usuario_nome']) ?></td>
                                <td class="suave"><?= htmlspecialchars($mov['observacao'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
