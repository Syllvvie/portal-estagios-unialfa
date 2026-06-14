<?php
session_start();

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: inicioEmpresa.php");
    exit;
}

require_once '../classes/Painel.php';

$mensagem = '';
$erro     = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $painel  = new Painel();

    // Campos que a API espera: nome, cnpj, email, senha, telefone?, endereco?, area_atuacao?
    $dados = [
        'nome'  => trim($_POST['nome']  ?? ''),
        'cnpj'  => trim($_POST['cnpj']  ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'senha' => $_POST['senha'] ?? '',
    ];
    if (!empty($_POST['telefone']))    $dados['telefone']    = trim($_POST['telefone']);
    if (!empty($_POST['area_atuacao'])) $dados['area_atuacao'] = trim($_POST['area_atuacao']);

    $resposta = $painel->cadastrarEmpresa($dados);

    // API retorna { empresa: {...} } com status 201
    if (($resposta['_httpCode'] ?? 0) === 201 || !empty($resposta['empresa'])) {
        $mensagem = 'Conta criada! Aguarde a aprovação da UniALFA para acessar o sistema.';
    } else {
        $erro     = true;
        $mensagem = $resposta['message'] ?? 'Erro ao cadastrar. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Criar Conta - UniALFA</title>
</head>
<body class="min-vh-100 d-flex flex-column">

    <header class="d-flex justify-content-between align-items-center p-3 px-5 bg-white border-bottom shadow-sm">
        <img src="../assets/imagens/logo-unialfa.png" style="width: 150px;" alt="Logo UniALFA">
        <div>
            <span class="text-muted">Já tem uma conta?</span>
            <a href="loginEmpresa.php" class="text-decoration-none fw-bold ms-1" style="color:#0056b3;">Entrar</a>
        </div>
    </header>

    <main class="flex-grow-1 d-flex align-items-center py-5">
        <div class="container" style="max-width: 600px;">

            <?php if (!empty($mensagem)): ?>
                <div class="alert <?= $erro ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show">
                    <?= htmlspecialchars($mensagem) ?>
                    <?php if (!$erro): ?>
                        <a href="loginEmpresa.php" class="alert-link ms-2">Ir para o login</a>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow rounded-3 p-4 p-md-5">
                <h4 class="fw-bold mb-1">Criar conta da empresa</h4>
                <p class="text-muted mb-4">Preencha os dados abaixo</p>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:.85rem;">
                            Nome da empresa <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nome" class="form-control"
                               placeholder="Nome da empresa"
                               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold" style="font-size:.85rem;">
                                CNPJ <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="cnpj" class="form-control"
                                   placeholder="00.000.000/0000-00"
                                   value="<?= htmlspecialchars($_POST['cnpj'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:.85rem;">Telefone</label>
                            <input type="text" name="telefone" class="form-control"
                                   placeholder="(00) 00000-0000"
                                   value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:.85rem;">Área de atuação</label>
                        <input type="text" name="area_atuacao" class="form-control"
                               placeholder="Ex: Tecnologia da Informação"
                               value="<?= htmlspecialchars($_POST['area_atuacao'] ?? '') ?>">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold" style="font-size:.85rem;">
                                E-mail <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="seuemail@empresa.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:.85rem;">
                                Senha <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="senha" class="form-control"
                                   placeholder="Mínimo 6 caracteres" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 fs-5">
                        Criar conta da empresa
                    </button>

                </form>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
