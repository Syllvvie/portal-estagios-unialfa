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
require_once '../classes/Vaga.php';
require_once '../classes/Candidatura.php';

$painel    = new Painel();
$token     = $_SESSION['token'];
$nomeAluno = $_SESSION['usuario_nome'] ?? 'Estudante';
$mensagem  = '';
$erro      = false;

// Candidatar-se — sem carta de apresentação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vaga_id'])) {
    $res = $painel->candidatar((int)$_POST['vaga_id'], $token);
    if (!empty($res['candidatura'])) {
        $mensagem = 'Candidatura realizada com sucesso!';
    } else {
        $erro     = true;
        $mensagem = $res['message'] ?? 'Erro ao se candidatar.';
    }
}

/** @var Vaga[] $vagas */
$resVagas = $painel->listarVagas(true);
$vagas    = array_map(fn($v) => Vaga::fromArray($v), $resVagas['vagas'] ?? []);

$resCands        = $painel->minhasCandidaturas($token);
$vagasCandidatas = array_map(
    fn($c) => Candidatura::fromArray($c)->getVagaId(),
    $resCands['candidaturas'] ?? []
);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Vagas - Portal de Estágios</title>
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
            <h2 class="fw-bold mb-1">Vagas Disponíveis</h2>
            <p class="text-muted mb-4">Encontre a oportunidade certa para você.</p>

            <?php if (!empty($mensagem)): ?>
                <div class="alert <?= $erro ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show">
                    <?= htmlspecialchars($mensagem) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex flex-column gap-3">
                <?php if (!empty($vagas)): ?>
                    <?php foreach ($vagas as $vaga):
                        $jaCandidatou = in_array($vaga->getId(), $vagasCandidatas);
                    ?>
                        <div class="bg-white border rounded shadow-sm p-4">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($vaga->getTitulo()) ?></h5>
                                    <p class="text-muted mb-2" style="font-size:.9rem;">
                                        <strong><?= htmlspecialchars($vaga->getEmpresaNome()) ?></strong>
                                        <?php if ($vaga->getLocal()): ?>
                                            &bull; <?= htmlspecialchars($vaga->getLocal()) ?>
                                        <?php endif; ?>
                                        <?php if ($vaga->getCargaHoraria()): ?>
                                            &bull; <?= $vaga->getCargaHorariaLabel() ?>
                                        <?php endif; ?>
                                        <?php if ($vaga->getBolsa()): ?>
                                            &bull; <?= $vaga->getBolsaFormatada() ?>/mês
                                        <?php endif; ?>
                                    </p>
                                    <p class="mb-2" style="font-size:.9rem;">
                                        <?= htmlspecialchars(mb_substr($vaga->getDescricao(), 0, 200)) ?>
                                        <?= mb_strlen($vaga->getDescricao()) > 200 ? '...' : '' ?>
                                    </p>
                                    <?php if ($vaga->getArea()): ?>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($vaga->getArea()) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-shrink-0">
                                    <?php if ($jaCandidatou): ?>
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background-color:#dcfce7;color:#15803d;">
                                            ✓ Candidatado
                                        </span>
                                    <?php else: ?>
                                        <!-- Candidatura direta, sem modal nem carta -->
                                        <form method="POST"
                                              onsubmit="return confirm('Confirmar candidatura para: <?= htmlspecialchars(addslashes($vaga->getTitulo())) ?>?')">
                                            <input type="hidden" name="vaga_id" value="<?= $vaga->getId() ?>">
                                            <button type="submit" class="btn btn-primary fw-bold">
                                                Candidatar-se
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <p>Nenhuma vaga disponível no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
