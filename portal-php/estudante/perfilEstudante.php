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
require_once '../classes/Aluno.php';
require_once '../classes/Candidatura.php';

$painel = new Painel();
$token  = $_SESSION['token'];

// Busca perfil e candidaturas
$resPerfil    = $painel->perfilAluno($token);
$aluno        = !empty($resPerfil['aluno']) ? Aluno::fromArray($resPerfil['aluno']) : null;
$resCands     = $painel->minhasCandidaturas($token);
/** @var Candidatura[] $candidaturas */
$candidaturas = array_map(fn($c) => Candidatura::fromArray($c), $resCands['candidaturas'] ?? []);
$aprovadas    = count(array_filter($candidaturas, fn($c) => $c->isAprovada()));
$emAndamento  = count(array_filter($candidaturas, fn($c) => in_array($c->getStatus(), ['pendente','em_analise'])));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Meu Perfil - Portal de Estágios</title>
    <style>
        body { background-color:#f8f9fa; }
        .card-perfil { background:#fff; border:1px solid #dee2e6; border-radius:12px; padding:20px; height:100%; }
        .card-title-perfil { font-size:1rem; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #f0f0f0; }
        .avatar { width:80px; height:80px; background:#17A2B8; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:1.8rem; flex-shrink:0; }
        .stat-box { border:1px solid #dee2e6; border-radius:10px; padding:14px 8px; text-align:center; }
        .stat-number { font-size:1.5rem; font-weight:800; line-height:1; }
        .stat-label  { font-size:.72rem; color:#6c757d; margin:4px 0 0; }
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

        <div class="flex-grow-1 px-4 py-3">
            <h3 class="fw-bold mb-1">Meu Perfil</h3>
            <p class="text-muted small mb-3">Suas informações acadêmicas.</p>

            <?php if (!$aluno): ?>
                <div class="alert alert-warning">Não foi possível carregar os dados. Verifique a API.</div>
            <?php else: ?>

            <div class="row g-3">
                <!-- Esquerda: dados -->
                <div class="col-lg-7">
                    <div class="card-perfil">
                        <h5 class="card-title-perfil">Informações Pessoais</h5>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="avatar"><?= mb_strtoupper(mb_substr($aluno->getNome(), 0, 1)) ?></div>
                            <div>
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($aluno->getNome()) ?></h5>
                                <p class="text-muted mb-1" style="font-size:.9rem;">
                                    RA: <strong><?= htmlspecialchars($aluno->getRa()) ?></strong>
                                </p>
                                <p class="text-muted mb-0" style="font-size:.85rem;">
                                    <?= htmlspecialchars($aluno->getCurso() ?? 'Curso não informado') ?>
                                    <?php if ($aluno->getPeriodo()): ?>
                                        &bull; <?= $aluno->getPeriodo() ?>º período
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Dados somente leitura -->
                        <div class="row g-2 mb-3" style="font-size:.85rem;">
                            <div class="col-md-6">
                                <span class="text-muted">E-mail</span>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($aluno->getEmail()) ?></p>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">Telefone</span>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($aluno->getTelefone() ?? 'Não informado') ?></p>
                            </div>
                        </div>

                        <!-- Aviso institucional -->
                        <div class="alert alert-info py-2 mb-3" style="font-size:.82rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Para alterar seus dados cadastrais (nome, e-mail, curso, telefone), entre em contato com a instituição UniALFA.
                        </div>

                        <a href="trocarSenha.php?voluntario=1" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-key me-1"></i> Alterar senha
                        </a>
                    </div>
                </div>

                <!-- Direita: stats + status -->
                <div class="col-lg-5 d-flex flex-column gap-3">
                    <div class="card-perfil">
                        <h5 class="card-title-perfil">Minhas Estatísticas</h5>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="stat-box">
                                    <i class="bi bi-send d-block mb-1" style="color:#17A2B8;font-size:1.4rem;"></i>
                                    <div class="stat-number"><?= count($candidaturas) ?></div>
                                    <p class="stat-label">Candidaturas</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <i class="bi bi-hourglass-split d-block mb-1" style="color:#ffc107;font-size:1.4rem;"></i>
                                    <div class="stat-number"><?= $emAndamento ?></div>
                                    <p class="stat-label">Em andamento</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <i class="bi bi-check2-circle d-block mb-1" style="color:#28a745;font-size:1.4rem;"></i>
                                    <div class="stat-number"><?= $aprovadas ?></div>
                                    <p class="stat-label">Aprovadas</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-perfil">
                        <h5 class="card-title-perfil">Status Acadêmico</h5>
                        <div class="d-flex align-items-center justify-content-between p-3 rounded"
                             style="background:<?= $aluno->isApto() ? '#d4edda' : '#f8d7da' ?>;">
                            <div>
                                <strong style="font-size:.9rem;">
                                    <?= $aluno->isApto() ? 'Apto para estágio' : 'Não apto para estágio' ?>
                                </strong>
                                <p class="mb-0 text-muted" style="font-size:.8rem;">
                                    <?= $aluno->isApto()
                                        ? 'Você pode se candidatar às vagas.'
                                        : 'Consulte a coordenação da UniALFA.' ?>
                                </p>
                            </div>
                            <i class="bi bi-<?= $aluno->isApto() ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' ?>"
                               style="font-size:1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <?php endif; ?>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
