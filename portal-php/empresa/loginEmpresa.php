<?php
session_start();

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: inicioEmpresa.php");
    exit;
}

require_once '../classes/Painel.php';

$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $mensagemErro = 'Preencha o e-mail e a senha.';
    } else {
        $painel    = new Painel();
        $resultado = $painel->loginEmpresa($email, $senha);

        // API retorna: { token, empresa: {...} }
        if (!empty($resultado['token']) && !empty($resultado['empresa'])) {
            $_SESSION['logado']       = true;
            $_SESSION['usuario_tipo'] = 'empresa';
            $_SESSION['token']        = $resultado['token'];
            $_SESSION['usuario_id']   = $resultado['empresa']['id'];
            $_SESSION['usuario_nome'] = $resultado['empresa']['nome'];
            header("Location: inicioEmpresa.php");
            exit;
        } else {
            $mensagemErro = $resultado['message'] ?? 'E-mail ou senha incorretos.';
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
    <title>Login Empresa - UniALFA</title>
</head>
<body>
    <header class="d-flex justify-content-between align-items-center">
        <img src="../assets/imagens/logo-unialfa.png" style="width: 200px;" alt="logo unialfa">
        <a href="../index.php" class="mx-4 link-secondary text-decoration-none fw-bold">&#8678; Voltar</a>
    </header>

    <main class="container my-3">
        <div class="row align-items-center justify-content-center gap-5">

            <div class="col-12 col-lg-5 section-empresa">
                <img src="../assets/imagens/Ícone da empresa.png" alt="Ícone da empresa" class="mb-3">
                <p class="fs-1 mb-2">Login de <br><span class="fs-1 fw-bold" style="color: #0056b3;">Empresa</span></p>
                <p class="text-muted">Publique vagas, gerencie processos seletivos e encontre os melhores talentos.</p>

                <div class="d-flex align-items-center mb-4">
                    <img src="../assets/imagens/Icones vagas.png" alt="" class="me-3">
                    <div>
                        <h3 class="fs-5 mb-1 fw-bold">Publique vagas</h3>
                        <p class="mb-0 text-muted">Divulgue oportunidades e alcance candidatos.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <img src="../assets/imagens/Icones gerenciar.png" alt="" class="me-3">
                    <div>
                        <h3 class="fs-5 mb-1 fw-bold">Gerencie processos</h3>
                        <p class="mb-0 text-muted">Acompanhe candidaturas e etapas seletivas.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <img src="../assets/imagens/Icones talentos.png" alt="" class="me-3">
                    <div>
                        <h3 class="fs-5 mb-1 fw-bold">Encontre talentos</h3>
                        <p class="mb-0 text-muted">Conecte-se com os melhores profissionais.</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <form class="formulario text-start border shadow rounded-4 p-4"
                      style="max-width: 480px; margin: auto;" method="POST">

                    <h2 class="text-center mb-0 fw-bold">Acesse sua conta</h2>
                    <p class="text-center text-muted mb-4">Informe seus dados para entrar.</p>

                    <?php if (!empty($mensagemErro)): ?>
                        <div class="alert alert-danger text-center py-2">
                            <?= htmlspecialchars($mensagemErro) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="email">E-mail corporativo</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 bg-white">
                                <img src="../assets/imagens/Frame.png" alt="" style="width: 20px;">
                            </span>
                            <input type="email" class="form-control p-3 border-start-0"
                                   name="email" id="email" placeholder="seu@email.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="senha">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 bg-white">
                                <img src="../assets/imagens/Vector.png" alt="" style="width: 20px;">
                            </span>
                            <input type="password" class="form-control p-3 border-start-0"
                                   name="senha" id="senha" placeholder="Sua senha" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 p-3 mt-3 mb-4 fw-bold">Entrar</button>

                    <div class="text-center">
                        <p class="mb-2 text-muted">Ainda não possui conta?</p>
                        <a href="cadastroEmpresa.php" class="btn btn-outline-primary w-100 fw-bold p-3">
                            Criar conta da empresa
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
