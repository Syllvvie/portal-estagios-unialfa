<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: loginEmpresa.php");
    exit;
}

require_once '../classes/Painel.php';

$painel      = new Painel();
$token       = $_SESSION['token'];
$empresaId   = (int)$_SESSION['usuario_id'];
$mensagem    = '';
$erro        = false;

// Salvar edição do perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvarPerfil'])) {
    $dados = [];
    if (!empty($_POST['nome']))        $dados['nome']        = trim($_POST['nome']);
    if (!empty($_POST['telefone']))    $dados['telefone']    = trim($_POST['telefone']);
    if (!empty($_POST['endereco']))    $dados['endereco']    = trim($_POST['endereco']);
    if (!empty($_POST['area_atuacao'])) $dados['area_atuacao'] = trim($_POST['area_atuacao']);

    $res = $painel->atualizarEmpresa($empresaId, $dados, $token);
    if (!empty($res['message']) && empty($res['status'])) {
        $mensagem = 'Perfil atualizado com sucesso!';
        // Atualiza nome na sessão se mudou
        if (!empty($dados['nome'])) $_SESSION['usuario_nome'] = $dados['nome'];
    } elseif (!empty($res['empresa'])) {
        $mensagem = 'Perfil atualizado com sucesso!';
        $_SESSION['usuario_nome'] = $res['empresa']['nome'] ?? $_SESSION['usuario_nome'];
    } else {
        $erro     = true;
        $mensagem = $res['message'] ?? 'Erro ao atualizar perfil.';
    }
}

// Busca dados atuais da empresa via API
$resEmpresa = $painel->perfilEmpresa($token);
$empresa    = $resEmpresa['empresa'] ?? [];

// Busca estatísticas
$resVagas   = $painel->minhasVagas($token);
$vagas      = $resVagas['vagas'] ?? [];
$totalVagas = count($vagas);
$vagasAtivas = count(array_filter($vagas, fn($v) => $v['ativa'] ?? true));

// Total de candidatos em todas as vagas
$totalCandidatos = 0;
foreach ($vagas as $v) {
    $rc = $painel->candidatosDaVaga((int)$v['id'], $token);
    $totalCandidatos += count($rc['candidaturas'] ?? []);
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
    <title>Perfil da Empresa - Portal de Estágios</title>
    <style>
        body { background-color: #f8f9fa; }
        .card-perfil {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            height: 100%;
        }
        .card-title-perfil {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 16px;
            color: #212529;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .logo-empresa {
            width: 100px; height: 100px;
            background-color: #0056A3;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 2rem;
            flex-shrink: 0;
        }
        .stat-box {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 16px 10px;
            text-align: center;
        }
        .stat-box i { font-size: 1.6rem; margin-bottom: 6px; }
        .stat-number { font-size: 1.6rem; font-weight: 800; color: #212529; line-height: 1; }
        .stat-label { font-size: 0.75rem; color: #6c757d; margin: 4px 0 0; }
        .info-label { font-size: .85rem; color: #495057; margin-bottom: 4px; }
        .info-value { font-size: .85rem; color: #212529; }
        .status-aprovada { background:#d4edda; color:#155724; }
        .status-pendente  { background:#fff3cd; color:#856404; }
        .status-bloqueada { background:#f8d7da; color:#721c24; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include '../includes/header.php'; ?>

    <main class="d-flex flex-grow-1 align-items-stretch">

        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-hamburguer shadow-sm">
            <span class="linha"></span><span class="linha"></span><span class="linha"></span>
        </label>

        <?php include '../includes/menuEmpresa.php'; ?>

        <div class="flex-grow-1 px-4 py-3" style="overflow-y:auto;">

            <?php if (!empty($mensagem)): ?>
                <div class="alert <?= $erro ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show mb-3">
                    <?= htmlspecialchars($mensagem) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($empresa)): ?>
                <div class="alert alert-warning">
                    Não foi possível carregar os dados da empresa. Verifique se a API está rodando.
                </div>
            <?php else: ?>

            <div class="mb-3">
                <h3 class="fw-bold mb-1">Perfil da Empresa</h3>
                <p class="text-muted small mb-0">Visualize e edite as informações da sua empresa.</p>
            </div>

            <div class="row g-3">

                <!-- COLUNA ESQUERDA -->
                <div class="col-lg-7">
                    <div class="card-perfil">
                        <h5 class="card-title-perfil">Informações da Empresa</h5>

                        <div class="d-flex align-items-start mb-4 gap-3">
                            <!-- Iniciais do nome como logo -->
                            <div class="logo-empresa">
                                <?= mb_strtoupper(mb_substr($empresa['nome'] ?? 'E', 0, 1)) ?>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <strong style="font-size:.95rem;">
                                        <?= htmlspecialchars($empresa['nome'] ?? '-') ?>
                                    </strong>
                                    <?php $status = $empresa['status'] ?? 'pendente'; ?>
                                    <span class="badge rounded-pill px-2 status-<?= $status ?>"
                                          style="font-size:.7rem;">
                                        <?= $status === 'aprovada' ? 'Aprovada' : ($status === 'bloqueada' ? 'Bloqueada' : 'Pendente') ?>
                                    </span>
                                </div>
                                <div class="info-label">CNPJ: <span class="info-value"><?= htmlspecialchars($empresa['cnpj'] ?? '-') ?></span></div>
                                <div class="info-label">Área: <span class="info-value"><?= htmlspecialchars($empresa['area_atuacao'] ?? 'Não informada') ?></span></div>
                                <div class="info-label">E-mail: <span class="info-value"><?= htmlspecialchars($empresa['email'] ?? '-') ?></span></div>
                            </div>
                        </div>

                        <hr style="border-color:#f0f0f0;margin:1rem 0;">

                        <!-- Formulário de edição -->
                        <h5 class="card-title-perfil">Editar Informações</h5>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" style="font-size:.85rem;">Nome da empresa</label>
                                    <input type="text" name="nome" class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($empresa['nome'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" style="font-size:.85rem;">Área de atuação</label>
                                    <input type="text" name="area_atuacao" class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($empresa['area_atuacao'] ?? '') ?>"
                                           placeholder="Ex: Tecnologia da Informação">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" style="font-size:.85rem;">Telefone</label>
                                    <input type="text" name="telefone" class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($empresa['telefone'] ?? '') ?>"
                                           placeholder="(44) 99999-0000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" style="font-size:.85rem;">Endereço</label>
                                    <input type="text" name="endereco" class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($empresa['endereco'] ?? '') ?>"
                                           placeholder="Rua, número - Cidade/UF">
                                </div>
                            </div>
                            <button type="submit" name="salvarPerfil" class="btn btn-primary btn-sm fw-bold px-4">
                                Salvar alterações
                            </button>
                        </form>
                    </div>
                </div>

                <!-- COLUNA DIREITA -->
                <div class="col-lg-5 d-flex flex-column gap-3">

                    <!-- Estatísticas -->
                    <div class="card-perfil">
                        <h5 class="card-title-perfil">Estatísticas</h5>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="stat-box">
                                    <i class="bi bi-briefcase" style="color:#0056A3;"></i>
                                    <div class="stat-number"><?= $totalVagas ?></div>
                                    <p class="stat-label">Vagas<br>Total</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <i class="bi bi-check-circle" style="color:#28a745;"></i>
                                    <div class="stat-number"><?= $vagasAtivas ?></div>
                                    <p class="stat-label">Vagas<br>Ativas</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <i class="bi bi-people" style="color:#ffc107;"></i>
                                    <div class="stat-number"><?= $totalCandidatos ?></div>
                                    <p class="stat-label">Candidatos<br>Total</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="card-perfil">
                        <h5 class="card-title-perfil">Contato</h5>

                        <div class="d-flex mb-3">
                            <i class="bi bi-envelope me-3" style="color:#6c757d;font-size:1.1rem;margin-top:2px;"></i>
                            <div>
                                <strong style="font-size:.85rem;">E-mail</strong>
                                <p class="mb-0" style="font-size:.85rem;">
                                    <?= htmlspecialchars($empresa['email'] ?? '-') ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($empresa['telefone'])): ?>
                        <div class="d-flex mb-3">
                            <i class="bi bi-telephone me-3" style="color:#6c757d;font-size:1.1rem;margin-top:2px;"></i>
                            <div>
                                <strong style="font-size:.85rem;">Telefone</strong>
                                <p class="mb-0" style="font-size:.85rem;">
                                    <?= htmlspecialchars($empresa['telefone']) ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($empresa['endereco'])): ?>
                        <div class="d-flex">
                            <i class="bi bi-geo-alt me-3" style="color:#6c757d;font-size:1.1rem;margin-top:2px;"></i>
                            <div>
                                <strong style="font-size:.85rem;">Endereço</strong>
                                <p class="mb-0" style="font-size:.85rem;">
                                    <?= htmlspecialchars($empresa['endereco']) ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (empty($empresa['telefone']) && empty($empresa['endereco'])): ?>
                            <p class="text-muted" style="font-size:.85rem;">
                                Nenhuma informação de contato cadastrada. Edite seu perfil para adicionar.
                            </p>
                        <?php endif; ?>
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
