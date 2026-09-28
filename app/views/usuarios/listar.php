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