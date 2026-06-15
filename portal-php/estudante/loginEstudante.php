<?php
session_start();

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: inicioEstudante.php");
    exit;
}

require_once '../classes/Painel.php';

$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ra    = trim($_POST['ra']    ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($ra) || empty($senha)) {
        $mensagemErro = 'Preencha o RA e a senha.';
    } else {
        $painel    = new Painel();
        $resultado = $painel->loginAluno($ra, $senha);

        // API retorna: { token, aluno: {...}, primeiro_acesso: bool }
        if (!empty($resultado['token']) && !empty($resultado['aluno'])) {
            $_SESSION['logado']          = true;
            $_SESSION['usuario_tipo']    = 'aluno';
            $_SESSION['token']           = $resultado['token'];
            $_SESSION['usuario_id']      = $resultado['aluno']['id'];
            $_SESSION['usuario_nome']    = $resultado['aluno']['nome'];
            $_SESSION['usuario_ra']      = $resultado['aluno']['ra'];
            $_SESSION['primeiro_acesso'] = $resultado['aluno']['primeiro_acesso'] ?? false;

            // Se for o primeiro acesso, obriga troca de senha
            if ($_SESSION['primeiro_acesso']) {
                header("Location: trocarSenha.php");
            } else {
                header("Location: inicioEstudante.php");
            }
            exit;
        } else {
            $mensagemErro = $resultado['message'] ?? 'RA ou senha incorretos.';
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
    <title>Login Estudante - UniALFA</title>
</head>
<body>
    <header class="d-flex justify-content-between align-items-center">
        <img src="../assets/imagens/logo-unialfa.png" style="width: 200px;" alt="logo unialfa">
        <a href="../index.php" class="mx-4 link-secondary text-decoration-none fw-bold">&#8678; Voltar</a>
    </header>

    <main class="container my-3">
        <div class="row align-items-center justify-content-center gap-5">

            <div class="col-12 col-lg-5 section-empresa">
                <img src="../assets/imagens/Ícone do estudante.png" alt="Ícone do estudante" class="mb-3">
                <p class="fs-1 mb-2">Login de <br><span class="fs-1 fw-bold" style="color: #17A2B8;">Estudante</span></p>
                <p class="text-muted">Acesse com seu RA e senha para encontrar vagas de estágio.</p>

                <div class="d-flex align-items-center mb-4">
                    <img src="../assets/imagens/Encontrar Vagas.png" alt="" class="me-3">
                    <div>
                        <h3 class="fs-5 mb-1 fw-bold">Encontre vagas</h3>
                        <p class="mb-0 text-muted">Busque oportunidades perto de você.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <img src="../assets/imagens/Acompanhar Candidaturas.png" alt="" class="me-3">
                    <div>
                        <h3 class="fs-5 mb-1 fw-bold">Acompanhe candidaturas</h3>
                        <p class="mb-0 text-muted">Veja o status em tempo real.</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <form class="formulario text-start border shadow rounded-4 p-4"
                      style="max-width: 480px; margin: auto;" method="POST">

                    <h2 class="text-center mb-0 fw-bold">Acesse sua conta</h2>
                    <p class="text-center text-muted mb-4">Use seu RA e senha para entrar.</p>

                    <?php if (!empty($mensagemErro)): ?>
                        <div class="alert alert-danger text-center py-2">
                            <?= htmlspecialchars($mensagemErro) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="ra">RA (Registro Acadêmico)</label>
                        <input type="text" class="form-control p-3" name="ra" id="ra"
                               placeholder="Ex: 230001" maxlength="6"
                               value="<?= htmlspecialchars($_POST['ra'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="senha">Senha</label>
                        <input type="password" class="form-control p-3"
                               name="senha" id="senha" placeholder="Sua senha" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 p-3 mt-3 fw-bold">Entrar</button>

                </form>
            </div>

        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
