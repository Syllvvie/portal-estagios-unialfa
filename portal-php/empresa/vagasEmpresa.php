<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: loginEmpresa.php");
    exit;
}

require_once '../classes/Painel.php';
require_once '../classes/Vaga.php';

$painel      = new Painel();
$token       = $_SESSION['token'];
$nomeEmpresa = $_SESSION['usuario_nome'] ?? 'Empresa';
$mensagem    = '';
$erro        = false;

// ── Criar vaga ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criarVaga'])) {
    $dados = [
        'titulo'    => trim($_POST['titulo']    ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''),
    ];
    if (!empty($_POST['area']))          $dados['area']          = trim($_POST['area']);
    if (!empty($_POST['requisitos']))    $dados['requisitos']    = trim($_POST['requisitos']);
    if (!empty($_POST['local']))         $dados['local']         = trim($_POST['local']);
    if (!empty($_POST['carga_horaria'])) $dados['carga_horaria'] = (int)$_POST['carga_horaria'];
    if (!empty($_POST['bolsa']))         $dados['bolsa']         = (float)$_POST['bolsa'];

    $res = $painel->criarVaga($dados, $token);
    if (!empty($res['vaga'])) {
        $mensagem = 'Vaga publicada com sucesso!';
    } else {
        $erro = true;
        $mensagem = $res['message'] ?? 'Erro ao publicar vaga.';
    }
}

// ── Editar vaga ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editarVaga'])) {
    $id    = (int)$_POST['vaga_id'];
    $dados = [
        'titulo'    => trim($_POST['titulo']    ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''),
    ];
    if (isset($_POST['area']))          $dados['area']          = trim($_POST['area']);
    if (isset($_POST['requisitos']))    $dados['requisitos']    = trim($_POST['requisitos']);
    if (isset($_POST['local']))         $dados['local']         = trim($_POST['local']);
    if (!empty($_POST['carga_horaria'])) $dados['carga_horaria'] = (int)$_POST['carga_horaria'];
    if (!empty($_POST['bolsa']))         $dados['bolsa']         = (float)$_POST['bolsa'];

    $res = $painel->atualizarVaga($id, $dados, $token);
    if (!empty($res['vaga']) || !empty($res['message']) && ($res['_httpCode'] ?? 0) === 200) {
        $mensagem = 'Vaga atualizada com sucesso!';
    } else {
        $erro = true;
        $mensagem = $res['message'] ?? 'Erro ao atualizar vaga.';
    }
}

// ── Encerrar vaga (ativa = false) ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['encerrarVaga'])) {
    $id  = (int)$_POST['vaga_id'];
    $res = $painel->atualizarVaga($id, ['ativa' => false], $token);
    if (($res['_httpCode'] ?? 0) < 400) {
        $mensagem = 'Vaga encerrada.';
    } else {
        $erro = true;
        $mensagem = $res['message'] ?? 'Erro ao encerrar vaga.';
    }
}

// ── Reativar vaga ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reativarVaga'])) {
    $id  = (int)$_POST['vaga_id'];
    $res = $painel->atualizarVaga($id, ['ativa' => true], $token);
    if (($res['_httpCode'] ?? 0) < 400) {
        $mensagem = 'Vaga reativada.';
    } else {
        $erro = true;
        $mensagem = $res['message'] ?? 'Erro ao reativar vaga.';
    }
}

// ── Excluir vaga ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluirVaga'])) {
    $id  = (int)$_POST['vaga_id'];
    $res = $painel->removerVaga($id, $token);
    if (($res['_httpCode'] ?? 0) < 400) {
        $mensagem = 'Vaga excluída.';
    } else {
        $erro = true;
        $mensagem = $res['message'] ?? 'Erro ao excluir vaga.';
    }
}

// ── Carregar vagas ────────────────────────────────────────────────────────────
$resVagas = $painel->minhasVagas($token);
/** @var Vaga[] $vagas */
$vagas = array_map(fn($v) => Vaga::fromArray($v), $resVagas['vagas'] ?? []);

// Contagem de candidatos por vaga
$candidatosPorVaga = [];
foreach ($vagas as $vaga) {
    $res = $painel->candidatosDaVaga($vaga->getId(), $token);
    $candidatosPorVaga[$vaga->getId()] = count($res['candidaturas'] ?? []);
}
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
        <?php include '../includes/menuEmpresa.php'; ?>

        <div class="section flex-grow-1 p-4 px-md-5 w-100">
            <h2 class="fw-bold">Minhas Vagas</h2>
            <p class="text-muted">Gerencie suas vagas publicadas.</p>

            <?php if (!empty($mensagem)): ?>
                <div class="alert <?= $erro ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show">
                    <?= htmlspecialchars($mensagem) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="bg-white border rounded shadow-sm mt-4">
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1">Vagas publicadas</h4>
                        <p class="text-muted mb-0" style="font-size:.9rem;"><?= count($vagas) ?> vaga(s)</p>
                    </div>
                    <button class="btn btn-primary fw-bold px-4"
                            data-bs-toggle="modal" data-bs-target="#modalNovaVaga">
                        + Nova Vaga
                    </button>
                </div>

                <!-- Cabeçalho tabela -->
                <div class="row mx-0 p-3 px-4 border-bottom text-muted fw-bold" style="font-size:.82rem;">
                    <div class="col-4">VAGA</div>
                    <div class="col-2 text-center">STATUS</div>
                    <div class="col-2 text-center">CANDIDATOS</div>
                    <div class="col-2 text-center">PUBLICADA EM</div>
                    <div class="col-2 text-center">AÇÕES</div>
                </div>

                <?php if (!empty($vagas)): ?>
                    <?php foreach ($vagas as $vaga): ?>
                        <div class="row mx-0 p-3 px-4 align-items-center border-bottom">
                            <div class="col-4">
                                <h6 class="fw-bold mb-1" style="font-size:.9rem;">
                                    <?= htmlspecialchars($vaga->getTitulo()) ?>
                                </h6>
                                <p class="text-muted mb-0" style="font-size:.82rem;">
                                    <?= htmlspecialchars($vaga->getLocal() ?? 'Local não informado') ?>
                                    <?php if ($vaga->getCargaHoraria()): ?>
                                        &bull; <?= $vaga->getCargaHorariaLabel() ?>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="col-2 text-center">
                                <?php if ($vaga->isAtiva()): ?>
                                    <span class="badge rounded-pill px-2 py-1"
                                          style="background:#bdf6c8;color:#28a745;">Ativa</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill px-2 py-1"
                                          style="background:#e2e3e5;color:#495057;">Encerrada</span>
                                <?php endif; ?>
                            </div>

                            <div class="col-2 text-center">
                                <span class="fw-bold" style="color:#0056A3;">
                                    <?= $candidatosPorVaga[$vaga->getId()] ?? 0 ?>
                                </span>
                                <br><small class="text-muted">candidatos</small>
                            </div>

                            <div class="col-2 text-center text-muted" style="font-size:.85rem;">
                                <?= !empty($vaga->getCreatedAt())
                                    ? date('d/m/Y', strtotime($vaga->getCreatedAt())) : 'N/A' ?>
                            </div>

                            <!-- Ações -->
                            <div class="col-2 text-center d-flex flex-wrap gap-1 justify-content-center">

                                <!-- Ver candidatos -->
                                <a href="candidatosVaga.php?vaga_id=<?= $vaga->getId() ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    Candidatos
                                </a>

                                <!-- Editar -->
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarVaga"
                                        data-id="<?= $vaga->getId() ?>"
                                        data-titulo="<?= htmlspecialchars($vaga->getTitulo()) ?>"
                                        data-descricao="<?= htmlspecialchars($vaga->getDescricao()) ?>"
                                        data-area="<?= htmlspecialchars($vaga->getArea() ?? '') ?>"
                                        data-requisitos="<?= htmlspecialchars($vaga->getRequisitos() ?? '') ?>"
                                        data-local="<?= htmlspecialchars($vaga->getLocal() ?? '') ?>"
                                        data-carga="<?= $vaga->getCargaHoraria() ?? '' ?>"
                                        data-bolsa="<?= $vaga->getBolsa() ?? '' ?>">
                                    Editar
                                </button>

                                <!-- Encerrar / Reativar -->
                                <?php if ($vaga->isAtiva()): ?>
                                    <form method="POST" onsubmit="return confirm('Encerrar esta vaga?')">
                                        <input type="hidden" name="vaga_id" value="<?= $vaga->getId() ?>">
                                        <button type="submit" name="encerrarVaga"
                                                class="btn btn-sm btn-outline-warning">
                                            Encerrar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST">
                                        <input type="hidden" name="vaga_id" value="<?= $vaga->getId() ?>">
                                        <button type="submit" name="reativarVaga"
                                                class="btn btn-sm btn-outline-success">
                                            Reativar
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Excluir -->
                                <form method="POST"
                                      onsubmit="return confirm('Excluir permanentemente esta vaga?')">
                                    <input type="hidden" name="vaga_id" value="<?= $vaga->getId() ?>">
                                    <button type="submit" name="excluirVaga"
                                            class="btn btn-sm btn-outline-danger">
                                        Excluir
                                    </button>
                                </form>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <p>Nenhuma vaga ainda. Clique em "+ Nova Vaga" para começar.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal: Nova Vaga -->
    <div class="modal fade" id="modalNovaVaga" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Publicar Nova Vaga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <?php include '_form_vaga.php'; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="criarVaga" class="btn btn-primary fw-bold">Publicar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Vaga -->
    <div class="modal fade" id="modalEditarVaga" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Editar Vaga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="vaga_id" id="editVagaId">
                        <?php include '_form_vaga.php'; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="editarVaga" class="btn btn-primary fw-bold">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preenche o modal de edição com os dados da vaga clicada
        document.getElementById('modalEditarVaga').addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            document.getElementById('editVagaId').value             = btn.dataset.id;
            document.querySelector('#modalEditarVaga [name="titulo"]').value      = btn.dataset.titulo;
            document.querySelector('#modalEditarVaga [name="descricao"]').value   = btn.dataset.descricao;
            document.querySelector('#modalEditarVaga [name="area"]').value        = btn.dataset.area;
            document.querySelector('#modalEditarVaga [name="requisitos"]').value  = btn.dataset.requisitos;
            document.querySelector('#modalEditarVaga [name="local"]').value       = btn.dataset.local;
            document.querySelector('#modalEditarVaga [name="carga_horaria"]').value = btn.dataset.carga;
            document.querySelector('#modalEditarVaga [name="bolsa"]').value       = btn.dataset.bolsa;
        });
    </script>
</body>
</html>
