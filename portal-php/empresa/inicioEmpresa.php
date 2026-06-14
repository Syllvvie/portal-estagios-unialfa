<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: loginEmpresa.php");
    exit;
}

require_once '../classes/Painel.php';
require_once '../classes/Vaga.php';
require_once '../classes/Candidatura.php';

$painel      = new Painel();
$token       = $_SESSION['token'];
$nomeEmpresa = $_SESSION['usuario_nome'] ?? 'Empresa';

// Vagas e notificações
$resVagas     = $painel->minhasVagas($token);
/** @var Vaga[] $vagas */
$vagas        = array_map(fn($v) => Vaga::fromArray($v), $resVagas['vagas'] ?? []);

$resNotif     = $painel->minhasNotificacoes($token);
$notificacoes = $resNotif['notificacoes'] ?? [];
$naoLidas     = array_filter($notificacoes, fn($n) => !$n['lida']);

// Estatísticas rápidas
$totalVagas  = count($vagas);
$vagasAtivas = count(array_filter($vagas, fn($v) => $v->isAtiva()));
$totalCands  = 0;
foreach ($vagas as $v) {
    $r = $painel->candidatosDaVaga($v->getId(), $token);
    $totalCands += count($r['candidaturas'] ?? []);
}

$erroApi = !empty($resVagas['_erro']);

// Marcar notificação como lida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_lida'])) {
    $painel->marcarNotificacaoLida((int)$_POST['notif_id'], $token);
    header("Location: inicioEmpresa.php");
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
        .stat-card { background:#fff; border:1px solid #dee2e6; border-radius:10px; padding:16px 20px; }
        .stat-card .num { font-size:2rem; font-weight:800; line-height:1; }
        .notif-item { padding:12px 16px; border-bottom:1px solid #f0f0f0; }
        .notif-item.nao-lida { border-left:3px solid #0056A3; background:#f8f9ff; }
        .notif-item.lida { opacity:.7; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <?php include '../includes/header.php'; ?>
    <main class="d-flex flex-grow-1 align-items-stretch">
        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-hamburguer shadow-sm">
            <span class="linha"></span><span class="linha"></span><span class="linha"></span>
        </label>
        <?php include '../includes/menuEmpresa.php'; ?>

        <div class="section flex-grow-1 p-4 px-md-5 w-100">
            <h2 class="fw-bold mb-1">Olá, <?= htmlspecialchars($nomeEmpresa) ?>!</h2>
            <p class="text-muted mb-4">Bem-vindo ao seu painel de recrutamento.</p>

            <?php if ($erroApi): ?>
                <div class="alert alert-warning mb-4">
                    Não foi possível conectar à API. Verifique se o Node.js está rodando na porta 3000.
                </div>
            <?php endif; ?>

            <!-- Cards de estatísticas -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <p class="text-muted mb-1" style="font-size:.85rem;">Vagas publicadas</p>
                        <div class="num" style="color:#0056A3;"><?= $totalVagas ?></div>
                        <small class="text-muted"><?= $vagasAtivas ?> ativa(s)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <p class="text-muted mb-1" style="font-size:.85rem;">Total de candidatos</p>
                        <div class="num" style="color:#28a745;"><?= $totalCands ?></div>
                        <small class="text-muted">em todas as vagas</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <p class="text-muted mb-1" style="font-size:.85rem;">Notificações não lidas</p>
                        <div class="num" style="color:#ffc107;"><?= count($naoLidas) ?></div>
                        <small class="text-muted">novas notificações</small>
                    </div>
                </div>
            </div>

            <!-- Notificações recentes -->
            <div class="bg-white border rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <h5 class="fw-bold mb-0">
                        Notificações
                        <?php if (count($naoLidas) > 0): ?>
                            <span class="badge rounded-pill ms-1"
                                  style="background:#0056A3;font-size:.7rem;">
                                <?= count($naoLidas) ?> nova(s)
                            </span>
                        <?php endif; ?>
                    </h5>
                    <a href="vagasEmpresa.php" class="btn btn-sm btn-primary fw-bold">
                        Gerenciar Vagas
                    </a>
                </div>

                <?php if (!empty($notificacoes)): ?>
                    <?php foreach (array_slice($notificacoes, 0, 8) as $n): ?>
                        <div class="notif-item <?= $n['lida'] ? 'lida' : 'nao-lida' ?>">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <p class="fw-bold mb-1" style="font-size:.9rem;">
                                        <?= htmlspecialchars($n['titulo'] ?? '') ?>
                                    </p>
                                    <p class="text-muted mb-0" style="font-size:.85rem;">
                                        <?= htmlspecialchars($n['mensagem'] ?? '') ?>
                                    </p>
                                    <small class="text-muted">
                                        <?= !empty($n['created_at'])
                                            ? date('d/m/Y H:i', strtotime($n['created_at'])) : '' ?>
                                    </small>
                                </div>
                                <?php if (!$n['lida']): ?>
                                    <form method="POST" class="flex-shrink-0">
                                        <input type="hidden" name="marcar_lida" value="1">
                                        <input type="hidden" name="notif_id" value="<?= (int)$n['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            Marcar lida
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <small class="text-muted flex-shrink-0">Lida</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-bell-slash fs-3 d-block mb-2 opacity-50"></i>
                        <p class="mb-0">Nenhuma notificação ainda.</p>
                        <p class="mb-0" style="font-size:.85rem;">
                            Quando um aluno se candidatar às suas vagas, você será notificado aqui.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
