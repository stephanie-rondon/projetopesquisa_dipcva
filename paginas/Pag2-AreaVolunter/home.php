<?php
session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../login.php");
    exit();
}


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="stylesheet" href="home.css" />
  <title>Área do Voluntário - Home</title>
</head>
<body>
  <div class="container-home">
    <h2>Bem-vindo, <span id="nomeVoluntario">Voluntário</span>!</h2>
    <p>O que você deseja fazer?</p>

    <div class="botoes">
      <a href="../Pag3-Documentos/index.php" class="botao">Meus Documentos</a>
      <a href="cad-volunter.php" class="botao">Preencher Ficha</a>
      <a href="?logout=1" id="btnSair" class="botao">Sair</a> 
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>