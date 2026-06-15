<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header("Location: loginEstudante.php");
    exit;
}
if (!empty($_SESSION['primeiro_acesso'])) {
    header("Location: trocarSenha.php");
    exit;
}

require_once '../classes/Painel.php';
require_once '../classes/Candidatura.php';

$painel    = new Painel();
$token     = $_SESSION['token'];
$nomeAluno = $_SESSION['usuario_nome'] ?? 'Estudante';
$mensagem  = '';
$erro      = false;

// Cancelar candidatura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_id'])) {
    $id  = (int)$_POST['cancelar_id'];
    $res = $painel->cancelarCandidatura($id, $token);
    if (!empty($res['candidatura'])) {
        $mensagem = 'Candidatura cancelada.';
    } else {
        $erro     = true;
        $mensagem = $res['message'] ?? 'Erro ao cancelar.';
    }
}

// Hidrata objetos Candidatura
$resCands = $painel->minhasCandidaturas($token);
/** @var Candidatura[] $candidaturas */
$candidaturas = array_map(fn($c) => Candidatura::fromArray($c), $resCands['candidaturas'] ?? []);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Minhas Candidaturas - Portal de Estágios</title>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <?php include '../includes/header.php'; ?>

    <main class="d-flex flex-grow-1 align-items-stretch">
        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-hamburguer shadow-sm">
            <span class="linha"></span><span class="linha"></span><span class="linha"></span>
        </label>
        <?php include '../includes/menuEstudante.php'; ?>

        <div class="flex-grow-1 p-4 px-md-5">
            <h2 class="fw-bold mb-1">Minhas Candidaturas</h2>
            <p class="text-muted mb-4">Acompanhe o status de cada processo.</p>

            <?php if (!empty($mensagem)): ?>
                <div class="alert <?= $erro ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show">
                    <?= htmlspecialchars($mensagem) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($candidaturas)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($candidaturas as $cand):
                        $badge = $cand->getStatusBadge();
                    ?>
                        <div class="bg-white border rounded shadow-sm p-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1">
                                        <?= htmlspecialchars($cand->getVaga()?->getTitulo() ?? '-') ?>
                                    </h5>
                                    <p class="text-muted mb-1" style="font-size:.9rem;">
                                        <?= htmlspecialchars($cand->getVaga()?->getEmpresaNome() ?? 'Empresa') ?>
                                        <?php $local = $cand->getVaga()?->getLocal(); ?>
                                        <?php if ($local): ?>&bull; <?= htmlspecialchars($local) ?><?php endif; ?>
                                    </p>
                                    <small class="text-muted">Candidatura em: <?= $cand->getDataFormatada() ?></small>
                                    <?php if ($cand->getObservacao()): ?>
                                        <p class="mt-2 mb-0 text-muted" style="font-size:.85rem;">
                                            <strong>Obs:</strong> <?= htmlspecialchars($cand->getObservacao()) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background-color:<?= $badge['bg'] ?>;color:<?= $badge['cor'] ?>;">
                                        <?= $cand->getStatusLabel() ?>
                                    </span>
                                    <?php if ($cand->isPendente()): ?>
                                        <form method="POST"
                                              onsubmit="return confirm('Cancelar esta candidatura?')">
                                            <input type="hidden" name="cancelar_id"
                                                   value="<?= $cand->getId() ?>">
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger">Cancelar</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <p class="mb-3">Você ainda não se candidatou a nenhuma vaga.</p>
                    <a href="vagasEstudante.php" class="btn btn-primary fw-bold">Explorar vagas</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
