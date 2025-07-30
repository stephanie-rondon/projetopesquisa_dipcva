<?php
session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}

include '../../backend/conexao.php';

$erro = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (empty($nome) || empty($tipo) || empty($data) || empty($status)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        $sql_insere = "INSERT INTO acampamentos (nome, tipo, data, status, data_criacao) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt_insere = $conexao->prepare($sql_insere);
        if ($stmt_insere === false) {
            $erro = "Erro ao preparar consulta: " . $conexao->error;
        } else {
            $stmt_insere->bind_param("ssss", $nome, $tipo, $data, $status);
            if ($stmt_insere->execute()) {
                $erro = "Acampamento cadastrado com sucesso!";

            } else {
                $erro = "Erro ao cadastrar acampamento: " . $stmt_insere->error;
            }
            $stmt_insere->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Acampamento</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <header>
        <h1>Cadastrar Acampamento</h1>
        <nav class="navbars">
            <a href="../Pag5-Dashboard/index.php" class="navlinks">Dashboard</a>
            <!--<a href="" class="navlinks">Cadastrar Acampamento</a>-->
        </nav>
    </header>
    <form class="formulario" method="POST">
        <label for="tipo">Acampamento</label>
        <select id="tipo" name="tipo" required>
            <option value="" disabled <?php echo !isset($_POST['tipo']) ? 'selected' : ''; ?>>Selecione</option>
            <option value="juvenil" <?php echo isset($_POST['tipo']) && $_POST['tipo'] == 'juvenil' ? 'selected' : ''; ?>>Juvenil</option>
            <option value="adulto" <?php echo isset($_POST['tipo']) && $_POST['tipo'] == 'adulto' ? 'selected' : ''; ?>>Adulto</option>
            <option value="infantil" <?php echo isset($_POST['tipo']) && $_POST['tipo'] == 'infantil' ? 'selected' : ''; ?>>Infantil</option>
        </select>
        <label for="data">Data:</label>
        <input type="date" id="data" name="data" value="<?php echo isset($_POST['data']) ? htmlspecialchars($_POST['data']) : ''; ?>" required>
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="" disabled <?php echo !isset($_POST['status']) ? 'selected' : ''; ?>>Selecione</option>
            <option value="ativo" <?php echo isset($_POST['status']) && $_POST['status'] == 'ativo' ? 'selected' : ''; ?>>Ativo</option>
            <option value="pendente" <?php echo isset($_POST['status']) && $_POST['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
            <option value="cancelado" <?php echo isset($_POST['status']) && $_POST['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
        </select>
        <div class="botao-submit">
            <button type="submit" class="salvar">Salvar</button>
        </div>
        <?php if ($erro) echo "<p style='color: red; text-align: center;'>$erro</p>"; ?>
    </form>
</body>
</html>