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

$resVagas     = $painel->listarVagas(true);
/** @var Vaga[] $vagas */
$vagas        = array_map(fn($v) => Vaga::fromArray($v), $resVagas['vagas'] ?? []);

$resCands     = $painel->minhasCandidaturas($token);
/** @var Candidatura[] $candidaturas */
$candidaturas = array_map(fn($c) => Candidatura::fromArray($c), $resCands['candidaturas'] ?? []);

$resNotif     = $painel->minhasNotificacoes($token);
$notificacoes = $resNotif['notificacoes'] ?? [];
$naoLidas     = array_filter($notificacoes, fn($n) => !$n['lida']);

$erroApi = !empty($resVagas['_erro']);

// Marcar notificação como lida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_lida'])) {
    $painel->marcarNotificacaoLida((int)$_POST['notif_id'], $token);
    header("Location: inicioEstudante.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Início - Portal de Estágios</title>
    <style>
        .notif-item { padding:12px 16px; border-bottom:1px solid #f0f0f0; }
        .notif-item.nao-lida { border-left:3px solid #17A2B8; background:#f0fbfd; }
        .notif-item.lida { opacity:.7; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include '../includes/header.php'; ?>
    <main class="d-flex flex-grow-1 align-items-stretch">
        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-hamburguer shadow-sm">
            <span class="linha"></span><span class="linha"></span><span class="linha"></span>
        </label>
        <?php include '../includes/menuEstudante.php'; ?>

        <div class="flex-grow-1 p-4 px-md-5">
            <h2 class="fw-bold mb-1">Olá, <?= htmlspecialchars($nomeAluno) ?>!</h2>
            <p class="text-muted mb-4">Bem-vindo ao seu portal de oportunidades.</p>

            <?php if (isset($_GET['bemvindo'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    <strong>Senha criada com sucesso!</strong> Bem-vindo ao Portal de Estágios UniALFA.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($erroApi): ?>
                <div class="alert alert-warning mb-4">
                    Não foi possível conectar à API. Verifique se o Node.js está rodando na porta 3000.
                </div>
            <?php endif; ?>

            <div class="row g-4">

                <!-- Vagas disponíveis -->
                <div class="col-12 col-lg-6">
                    <div class="box-painel h-100">
                        <div class="box-painel-header">
                            <h6 class="fw-bold mb-0">Vagas disponíveis</h6>
                            <a href="vagasEstudante.php" class="text-decoration-none fw-semibold"
                               style="font-size:.85rem;color:#0056A3;">Ver Todas</a>
                        </div>
                        <div class="p-3 d-flex flex-column gap-3">
                            <?php if (!empty($vagas)): ?>
                                <?php foreach (array_slice($vagas, 0, 5) as $vaga): ?>
                                    <div class="card-vaga">
                                        <div class="icone-vaga-box">
                                            <img src="../assets/imagens/portal-estagio/vagas/icone-ti.png"
                                                 style="width:30px;" alt="">
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="fw-bold mb-0 text-truncate" style="font-size:.92rem;">
                                                <?= htmlspecialchars($vaga->getTitulo()) ?>
                                            </h6>
                                            <small class="text-muted d-block text-truncate">
                                                <?= htmlspecialchars($vaga->getEmpresaNome()) ?>
                                            </small>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($vaga->getLocal() ?? 'Local não informado') ?>
                                                <?php if ($vaga->getCargaHoraria()): ?>
                                                    &bull; <?= $vaga->getCargaHorariaLabel() ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <p class="mb-0">Nenhuma vaga disponível no momento.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Notificações -->
                <div class="col-12 col-lg-6">
                    <div class="box-painel h-100">
                        <div class="box-painel-header">
                            <h6 class="fw-bold mb-0">
                                Notificações
                                <?php if (count($naoLidas) > 0): ?>
                                    <span class="badge rounded-pill ms-1"
                                          style="background:#17A2B8;font-size:.65rem;">
                                        <?= count($naoLidas) ?>
                                    </span>
                                <?php endif; ?>
                            </h6>
                            <a href="candidaturasEstudante.php" class="text-decoration-none fw-semibold"
                               style="font-size:.85rem;color:#0056A3;">Minhas candidaturas</a>
                        </div>

                        <?php if (!empty($notificacoes)): ?>
                            <?php foreach (array_slice($notificacoes, 0, 6) as $n): ?>
                                <div class="notif-item <?= $n['lida'] ? 'lida' : 'nao-lida' ?>">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div>
                                            <p class="fw-bold mb-0" style="font-size:.85rem;">
                                                <?= htmlspecialchars($n['titulo'] ?? '') ?>
                                            </p>
                                            <p class="text-muted mb-0" style="font-size:.8rem;">
                                                <?= htmlspecialchars($n['mensagem'] ?? '') ?>
                                            </p>
                                        </div>
                                        <?php if (!$n['lida']): ?>
                                            <form method="POST" class="flex-shrink-0">
                                                <input type="hidden" name="marcar_lida" value="1">
                                                <input type="hidden" name="notif_id" value="<?= (int)$n['id'] ?>">
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        style="font-size:.75rem;padding:2px 8px;">
                                                    Lida
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-bell-slash fs-3 d-block mb-2 opacity-50"></i>
                                <p class="mb-0" style="font-size:.85rem;">
                                    Nenhuma notificação ainda. Quando uma empresa atualizar o status
                                    da sua candidatura, você verá aqui.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
