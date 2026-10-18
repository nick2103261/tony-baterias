<div class="pagina-topo">
    <div>
        <p class="pagina-topo__eyebrow"><a href="produtos.php">&larr; Produtos</a></p>
        <h1>Categorias <span class="pagina-topo__contagem"><?= count($categorias) ?></span></h1>
    </div>
</div>

<?= alertaFlash() ?>

<div class="colunas colunas--4-6">
    <section class="painel">
        <div class="painel__topo">
            <h2>Nova categoria</h2>
        </div>
        <form method="POST" action="categorias.php?acao=salvar" class="painel__corpo">
            <?= campoCsrf() ?>
            <div class="campo">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required placeholder="Baterias de moto">
            </div>
            <button type="submit" class="btn btn-primario">Adicionar</button>
        </form>
    </section>

    <section>
        <?php if (empty($categorias)): ?>
            <div class="estado-vazio">Nenhuma categoria ainda. Crie ao lado a primeira, por exemplo "Baterias de carro".</div>
        <?php else: ?>
            <div class="tabela-wrap">
                <table class="tabela">
                    <thead>
                        <tr><th>Nome</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td class="cel-principal"><?= htmlspecialchars($categoria['nome']) ?></td>
                                <td class="cel-acoes">
                                    <div class="acoes">
                                        <?php if (ehAdmin()): ?>
                                            <?= botaoAcao('categorias.php?acao=excluir', ['id' => $categoria['id']], 'excluir', 'Excluir', 'acao--perigo', 'Excluir esta categoria? Produtos que a usam ficam sem categoria.') ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
