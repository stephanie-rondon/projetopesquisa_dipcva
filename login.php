<?php
include 'backend/conexao.php';

session_start();

$erro = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $email, $senha);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['is_admin'] = $usuario['is_admin'] == 1;
            // Redireciona com base no papel
            if ($_SESSION['is_admin']) {
                header("Location: ./paginas/Pag4-Dashboard/index.php");
            } else {
                header("Location: ./paginas/Pag2-AreaVolunter/home.php");
            }
            exit();
        } else {
            $erro = "E-mail ou senha inválidos.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="paginas/Pag1-Login/style.css" />
    <title>Página de Login</title>
</head>

<body>
    <main id="form_container">
        <form action="" id="formLogin" method="POST">
            <div class="titulo">
                <h3>Faça o seu login</h3>
                <div class="barra-horizontal"></div>
            </div>
            
            <div class="campo-input">
                <label for="e-mail">Seu e-mail*</label>
                <input type="email" id="e-mail" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
            </div>

            <div class="campo-input">
                <label for="senha">Sua senha*</label>
                <div class="input-com-icone">
                    <input type="password" id="senha" name="senha" required />
                    <i id="toggleSenha" class="fa-solid fa-eye"></i>
                </div>
            </div>

            <button type="submit" id="btnEntrar">Entrar</button>
            <?php if ($erro) echo "<p style='color: red; text-align: center;'>$erro</p>"; ?>
            <p class="cadastro">
                Ainda não tem uma conta?
                <a href="paginas/Pag1-Login/cadastro.php">Cadastre-se</a>
            </p>
        </form>
    </main>

    <script src="login.js"></script>
</body>

</html>