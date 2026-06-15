<?php
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>

<nav class="nav-estagios bg-white d-flex flex-column border-end-3" style="width: 260px;">

    <a href="inicioEmpresa.php" class="text-decoration-none px-3 d-flex align-items-center py-2 mt-3 <?= ($paginaAtual == 'inicioEmpresa.php') ? 'ativo' : '' ?>">
        <img src="../assets/imagens/portal-estagio/inicio.png" alt="">
        <p class="mb-0 ms-3 fw-bold nav-esagios-texto <?= ($paginaAtual != 'inicioEmpresa.php') ? 'text-muted' : '' ?>">Início</p>
    </a>

    <a href="vagasEmpresa.php" class="text-decoration-none px-3 d-flex my-1 align-items-center py-2 <?= in_array($paginaAtual, ['vagasEmpresa.php','candidatosVaga.php']) ? 'ativo' : '' ?>">
        <img src="../assets/imagens/portal-estagio/Vagas.png" alt="">
        <p class="mb-0 ms-3 fw-bold nav-esagios-texto <?= !in_array($paginaAtual, ['vagasEmpresa.php','candidatosVaga.php']) ? 'text-muted' : '' ?>">Vagas</p>
    </a>

    <a href="perfilEmpresa.php" class="text-decoration-none px-3 d-flex my-1 align-items-center py-2 <?= ($paginaAtual == 'perfilEmpresa.php') ? 'ativo' : '' ?>">
        <img src="../assets/imagens/portal-estagio/Perfil.png" alt="">
        <p class="mb-0 ms-3 fw-bold nav-esagios-texto <?= ($paginaAtual != 'perfilEmpresa.php') ? 'text-muted' : '' ?>">Perfil</p>
    </a>

    <a href="../logout.php" class="text-decoration-none px-3 d-flex align-items-center py-2 mt-auto mb-4">
        <img src="../assets/imagens/portal-estagio/Sair.png" alt="">
        <span class="mb-0 ms-3 fw-bold nav-esagios-texto-sair text-muted">Sair</span>
    </a>

</nav>
