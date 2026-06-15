<?php
// Descobre o nome do arquivo que está sendo acessado no momento
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>

<nav class="nav-estagios-aluno d-flex flex-column border-end-3 " style="width: 260px;">

    <a href="inicioEstudante.php" class="mt-3 px-3 py-2 <?= ($paginaAtual == 'inicioEstudante.php') ? 'ativo' : '' ?>">
        <img src="../assets/imagens/home-estudante.png" alt="Início">
        <p class="mb-0 ms-3 fw-bold nav-esagios-texto-aluno <?= ($paginaAtual != 'inicioEstudante.php') ? 'text-muted' : '' ?>">Início</p>
    </a>

    <a href="vagasEstudante.php" class="text-decoration-none px-3 d-flex my-1 align-items-center py-2 <?= ($paginaAtual == 'vagasEstudante.php') ? 'ativo' : '' ?>">
        <img src="../assets/imagens/vagas-estudante.png" alt="Vagas">
        <p class="mb-0 ms-3 fw-bold nav-esagios-texto-aluno <?= ($paginaAtual != 'vagasEstudante.php') ? 'text-muted' : '' ?>">Vagas</p>
    </a>

    <a href="candidaturasEstudante.php" class="my-1 px-3 py-2 <?= ($paginaAtual == 'candidaturasEstudante.php') ? 'ativo' : '' ?>">
        <img src="../assets/imagens/candidatura-estudante.png" alt="Candidaturas">
        <p class="mb-0 ms-3 fw-bold nav-esagios-texto-aluno <?= ($paginaAtual != 'candidaturasEstudante.php') ? 'text-muted' : '' ?>">Candidaturas</p>
    </a>

    <a href="perfilEstudante.php" class="my-1 px-3 py-2 <?= ($paginaAtual == 'perfilEstudante.php') ? 'ativo' : '' ?>">
        <img src="../assets/imagens/Perfil - Estudante.png" alt="Perfil">
        <p class="mb-0 ms-3 fw-bold nav-esagios-texto-aluno <?= ($paginaAtual != 'perfilEstudante.php') ? 'text-muted' : '' ?>">Perfil</p>
    </a>    

    <a href="../logout.php" class="text-decoration-none px-3 d-flex align-items-center py-2 mt-auto mb-4">
        <img src="../assets/imagens/portal-estagio/Sair.png" alt="Sair">
        <span class="mb-0 ms-3 fw-bold text-muted">Sair</span>
    </a>

</nav>