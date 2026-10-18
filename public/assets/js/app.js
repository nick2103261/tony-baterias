// Abre e fecha o menu lateral no celular
const alternarMenu = () => document.body.classList.toggle('menu-aberto');

document.getElementById('abrir-menu').addEventListener('click', alternarMenu);
document.getElementById('lateral-fundo').addEventListener('click', alternarMenu);

// Rótulo de cada coluna nas tabelas que viram "cartões" no celular
document.querySelectorAll('.tabela').forEach(tabela => {
    const colunas = [...tabela.querySelectorAll('thead th')].map(th => th.textContent.trim());

    tabela.querySelectorAll('tbody tr').forEach(linha => {
        linha.querySelectorAll('td').forEach((celula, i) => celula.dataset.rotulo = colunas[i] || '');
    });
});
