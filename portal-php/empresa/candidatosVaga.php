<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: loginEmpresa.php");
    exit;
}

$vagaId = isset($_GET['vaga_id']) ? (int)$_GET['vaga_id'] : 0;
if (!$vagaId) {
    header("Location: vagasEmpresa.php");
    exit;
}

require_once '../classes/Painel.php';
require_once '../classes/Vaga.php';
require_once '../classes/Aluno.php';
require_once '../classes/Candidatura.php';

$painel      = new Painel();
$token       = $_SESSION['token'];
$mensagem    = '';
$erro        = false;

// Atualizar status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidatura_id'], $_POST['status'])) {
    $res = $painel->atualizarStatusCandidatura((int)$_POST['candidatura_id'], $_POST['status'], $token);
    if (!empty($res['candidatura'])) {
        $mensagem = 'Status atualizado!';
    } else {
        $erro     = true;
        $mensagem = $res['message'] ?? 'Erro ao atualizar.';
    }
}

// Dados da vaga
$resVaga = $painel->buscarVaga($vagaId);
$vaga    = !empty($resVaga['vaga']) ? Vaga::fromArray($resVaga['vaga']) : null;

// Candidatos desta vaga
$resCands     = $painel->candidatosDaVaga($vagaId, $token);
/** @var Candidatura[] $candidaturas */
$candidaturas = array_map(fn($c) => Candidatura::fromArray($c), $resCands['candidaturas'] ?? []);

$statusOpcoes = [
    Candidatura::STATUS_PENDENTE   => 'Pendente',
    Candidatura::STATUS_EM_ANALISE => 'Em Análise',
    Candidatura::STATUS_APROVADA   => 'Aprovada',
    Candidatura::STATUS_REPROVADA  => 'Não selecionado',
    Candidatura::STATUS_CANCELADA  => 'Cancelada',
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Candidatos - Portal de Estágios</title>
    <style>
        .mini-perfil { display:none; background:#f8f9fa; border-top:1px solid #dee2e6; padding:12px 16px; }
        .mini-perfil.aberto { display:block; }
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

            <!-- Breadcrumb -->
            <a href="vagasEmpresa.php" class="text-muted text-decoration-none" style="font-size:.85rem;">
                &#8592; Voltar para Vagas
            </a>

            <?php if ($vaga): ?>
                <h2 class="fw-bold mt-2 mb-0"><?= htmlspecialchars($vaga->getTitulo()) ?></h2>
                <p class="text-muted mb-3" style="font-size:.9rem;">
                    <?= htmlspecialchars($vaga->getLocal() ?? '') ?>
                    <?php if ($vaga->getCargaHoraria()): ?>
                        &bull; <?= $vaga->getCargaHorariaLabel() ?>
                    <?php endif; ?>
                    <?php if ($vaga->getBolsa()): ?>
                        &bull; <?= $vaga->getBolsaFormatada() ?>/mês
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($mensagem)): ?>
                <div class="alert <?= $erro ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show mb-3">
                    <?= htmlspecialchars($mensagem) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="bg-white border rounded shadow-sm">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0">
                        Candidatos <span class="text-muted fw-normal">(<?= count($candidaturas) ?>)</span>
                    </h5>
                </div>

                <?php if (!empty($candidaturas)): ?>
                    <?php foreach ($candidaturas as $cand):
                        $badge = $cand->getStatusBadge();
                        $aluno = $cand->getAluno();
                    ?>
                        <!-- Linha do candidato -->
                        <div class="border-bottom">
                            <div class="row mx-0 p-3 px-4 align-items-center">
                                <div class="col-4">
                                    <h6 class="fw-bold mb-0" style="font-size:.9rem;">
                                        <?= htmlspecialchars($aluno?->getNome() ?? 'N/A') ?>
                                    </h6>
                                    <small class="text-muted">
                                        RA: <?= htmlspecialchars($aluno?->getRa() ?? 'N/A') ?>
                                        <?php if ($aluno?->getCurso()): ?>
                                            &bull; <?= htmlspecialchars($aluno->getCurso()) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="col-3">
                                    <span class="badge rounded-pill px-2 py-1"
                                          style="background-color:<?= $badge['bg'] ?>;color:<?= $badge['cor'] ?>;">
                                        <?= $cand->getStatusLabel() ?>
                                    </span>
                                </div>
                                <div class="col-2 text-muted" style="font-size:.85rem;">
                                    <?= $cand->getDataFormatada() ?>
                                </div>
                                <div class="col-3 d-flex gap-2 justify-content-end">
                                    <!-- Ver mini perfil do aluno -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="togglePerfil('perfil-<?= $cand->getId() ?>')">
                                        <i class="bi bi-person"></i> Ver perfil
                                    </button>
                                    <!-- Atualizar status -->
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" data-bs-target="#modalStatus"
                                            data-id="<?= $cand->getId() ?>"
                                            data-status="<?= htmlspecialchars($cand->getStatus()) ?>">
                                        Atualizar
                                    </button>
                                </div>
                            </div>

                            <!-- Mini perfil do aluno (expande ao clicar) -->
                            <div class="mini-perfil" id="perfil-<?= $cand->getId() ?>">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Nome</small>
                                        <span style="font-size:.9rem;"><?= htmlspecialchars($aluno?->getNome() ?? '-') ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">E-mail</small>
                                        <span style="font-size:.9rem;"><?= htmlspecialchars($aluno?->getEmail() ?? '-') ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Telefone</small>
                                        <span style="font-size:.9rem;">
                                            <?= htmlspecialchars($aluno?->getTelefone() ?? 'Não informado') ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Curso / Período</small>
                                        <span style="font-size:.9rem;">
                                            <?= htmlspecialchars($aluno?->getCurso() ?? '-') ?>
                                            <?php if ($aluno?->getPeriodo()): ?>
                                                &bull; <?= $aluno->getPeriodo() ?>º
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-5 text-center text-muted">
                        <p class="mb-0">Nenhum candidato ainda para esta vaga.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal status -->
    <div class="modal fade" id="modalStatus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Atualizar Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="candidatura_id" id="inputCandId">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Novo Status</label>
                            <select name="status" id="selectStatus" class="form-select">
                                <?php foreach ($statusOpcoes as $val => $label): ?>
                                    <option value="<?= $val ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePerfil(id) {
            var el = document.getElementById(id);
            el.classList.toggle('aberto');
        }
        document.getElementById('modalStatus').addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            document.getElementById('inputCandId').value  = btn.getAttribute('data-id');
            document.getElementById('selectStatus').value = btn.getAttribute('data-status');
        });
    </script>
</body>
</html>
