<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Estágios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
   <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header class="">
        <img class="" src="assets/imagens/logo-unialfa.png" style="width: 200px;" alt="logo unialfa">
    </header>

    <main>
        <div class="text-center mt-2">
            <p class="fw-bold fs-4 bem-vindo">Bem-vindo ao</p>
            <span class="fw-bold fs-1 logo-text-black">PORTAL DE ESTÁGIOS </span>
            <span class="fw-bold fs-1 logo-text-secun">UNI</span>
            <span class="fw-bold fs-1 logo-text-blue">ALFA</span>
            <br>
            <img src="assets/imagens/Line 2.png" alt="divisor" class="my-3">
            <p class="fw-bold fs-5 sub-ti">Conectando talentos às oportunidades locais.</p>
        </div>

        <div class=" d-flex justify-content-center flex-wrap gap-4 mt-5">

            <div class="empresa text-center border p-4 ">
                <img src="assets/imagens/Ícone da empresa.png" alt="Ícone da empresa" class="mb-3">
                <h2>Empresa</h2>
                <p class="p-2">Publique vagas, gerencie processos e encontre os melhores talentos</p>
                <a href="empresa/loginEmpresa.php" class="botao-for">Entrar como Empresa ➔</a>
            </div>

            <div class="estudante text-center border p-4 ">
                <img src="assets/imagens/Ícone do estudante.png" alt="Ícone do estudante" class="mb-3">
                <h2>Estudante</h2>
                <p class="p-2">Encontre oportunidades, cadastre seu currículo e inicie sua carreira</p>
                <a href="estudante/loginEstudante.php" class="botao-for">Entrar como Estudante ➔</a>
            </div>

        </div>
    </main>



    <footer class="text-center py-3 mt-auto">
        <div class="container">
            <p class=" fw-bold  mb-0">&copy; 2026 Portal de Estágios
                <span class="fw-bold  logo-text-secun">UNI</span>
                <span class="fw-bold  logo-text-blue">ALFA</span>
                - Todos os direitos reservados.
            </p>
        </div>
    </footer>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>