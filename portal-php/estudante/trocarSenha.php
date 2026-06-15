<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header("Location: loginEstudante.php");
    exit;
}

$voluntario = isset($_GET['voluntario']);

if (empty($_SESSION['primeiro_acesso']) && !$voluntario) {
    header("Location: inicioEstudante.php");
    exit;
}

require_once '../classes/Painel.php';

$painel  = new Painel();
$token   = $_SESSION['token'];
$nome    = $_SESSION['usuario_nome'] ?? 'Estudante';
$erro    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaSenha = $_POST['nova_senha']      ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    if (empty($novaSenha) || empty($confirmar)) {
        $erro = 'Preencha todos os campos.';
    } elseif ($novaSenha !== $confirmar) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($novaSenha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        $res = $painel->trocarSenha($novaSenha, $token);
        if (!empty($res['aluno'])) {
            $_SESSION['primeiro_acesso'] = false;
            header("Location: " . ($voluntario ? 'perfilEstudante.php' : 'inicioEstudante.php?bemvindo=1'));
            exit;
        } else {
            $erro = $res['message'] ?? 'Erro ao alterar senha.';
        }
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
    <title><?= $voluntario ? 'Alterar Senha' : 'Primeiro Acesso' ?> - UniALFA</title>
</head>
<body class="d-flex flex-column min-vh-100" style="background-color:#f0f4fb;">
    <header class="d-flex align-items-center bg-white border-bottom px-4 py-2">
        <img src="../assets/imagens/logo-unialfa.png" style="width:180px;" alt="logo unialfa">
    </header>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div style="max-width:440px;width:100%;padding:0 16px;">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">

                <div class="text-center mb-4">
                    <h4 class="fw-bold mb-1">
                        <?= $voluntario ? 'Alterar Senha' : 'Bem-vindo, ' . htmlspecialchars($nome) . '!' ?>
                    </h4>
                    <p class="text-muted" style="font-size:.9rem;">
                        <?= $voluntario
                            ? 'Digite sua nova senha abaixo.'
                            : 'Este é seu primeiro acesso. Crie uma senha para continuar.' ?>
                    </p>
                </div>

                <?php if ($erro): ?>
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.9rem;">
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:.9rem;">
                            Nova senha <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="nova_senha" class="form-control p-3"
                               placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size:.9rem;">
                            Confirmar nova senha <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="confirmar_senha" class="form-control p-3"
                               placeholder="Repita a nova senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold p-3">
                        <?= $voluntario ? 'Salvar nova senha' : 'Criar senha e entrar' ?>
                    </button>
                </form>

            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
