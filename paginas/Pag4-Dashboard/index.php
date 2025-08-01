<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}

include '../../backend/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['promover_admin']) && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];
    $sql_update = "UPDATE usuarios SET is_admin = 1 WHERE id = ? AND is_admin = 0";
    $stmt = $conexao->prepare($sql_update);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remover_admin']) && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];
    if ($id_usuario != $_SESSION['id_usuario']) {
        $sql_update = "UPDATE usuarios SET is_admin = 0 WHERE id = ? AND is_admin = 1";
        $stmt = $conexao->prepare($sql_update);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}

$sql_usuarios = "SELECT id, email as nome FROM usuarios WHERE id != ?";
$stmt_usuarios = $conexao->prepare($sql_usuarios);
$stmt_usuarios->bind_param("i", $_SESSION['id_usuario']);
$stmt_usuarios->execute();
$result_usuarios = $stmt_usuarios->get_result();
$usuarios = $result_usuarios->fetch_all(MYSQLI_ASSOC);
$stmt_usuarios->close();

$sql_voluntarios = "SELECT COUNT(*) as total FROM voluntarios";
$result_voluntarios = $conexao->query($sql_voluntarios);
$total_voluntarios = $result_voluntarios->fetch_assoc()['total'] ?? 0;

$sql_ativos = "SELECT COUNT(*) as ativos FROM voluntarios WHERE status = 'ativo'";
$result_ativos = $conexao->query($sql_ativos);
$ativos = $result_ativos->fetch_assoc()['ativos'] ?? 0;

$sql_pendentes = "SELECT COUNT(*) as pendentes FROM voluntarios WHERE status = 'pendente'";
$result_pendentes = $conexao->query($sql_pendentes);
$pendentes = $result_pendentes->fetch_assoc()['pendentes'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <button onclick="window.location.href='../../login.php'; console.log('Botão Voltar clicado')" id="btnVoltar" class="botao">Voltar</button>
    <header>
        <h1>Dashboard do Gestor</h1>
    </header>
    <section class="admin-management">
        <h3>Gerenciar Administradores</h3>
        <input type="text" id="searchAdmin" placeholder="Pesquisar usuário por e-mail..." onkeyup="filterAdmins()">
        <div id="admin-list">
            <?php foreach ($usuarios as $usuario): ?>
                <div class="admin-item">
                    <span><?php echo htmlspecialchars($usuario['nome']); ?> (ID: <?php echo $usuario['id']; ?>)</span>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                        <button type="submit" name="promover_admin" class="btn-promote">Promover a Admin</button>
                        <button type="submit" name="remover_admin" class="btn-demote">Remover Admin</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if (empty($usuarios)): ?>
                <p>Nenhum usuário encontrado.</p>
            <?php endif; ?>
        </div>
    </section>
    <section class="cards">
        <div class="card">
            <h2>Voluntários</h2>
            <p id="total-voluntarios"><?php echo $total_voluntarios; ?></p>
        </div>
        <div class="card">
            <h2>Voluntários Ativos</h2>
            <p id="voluntarios-ativos"><?php echo $ativos; ?></p>
        </div>
        <div class="card">
            <h2>Pendentes</h2>
            <p id="voluntarios"><?php echo $pendentes; ?></p>
        </div>
    </section>
    <section class="tabela-dados">
        <h2>Gerenciar Voluntários</h2>
        <button onclick="window.location.href='../Pag5-Busca/buscavoluntarios.php'" class="btn-voluntarios">Ver Voluntários</button>
    </section>
    <script>
        function filterAdmins() {
            let input = document.getElementById("searchAdmin").value.toLowerCase();
            let items = document.getElementsByClassName("admin-item");
            for (let i = 0; i < items.length; i++) {
                let text = items[i].getElementsByTagName("span")[0].textContent.toLowerCase();
                items[i].style.display = text.includes(input) ? "block" : "none";
            }
        }
    </script>
</body>
</html>