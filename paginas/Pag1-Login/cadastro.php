<!-- Página de Stephanie -->
<?php
include '../../backend/conexao.php'; 

session_start();

$erro = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $confirmarSenha = trim($_POST['confirmar'] ?? '');

    if (empty($email) || empty($senha) || empty($confirmarSenha)) {
        $erro = "Por favor, preencha todos os campos.";
    } elseif ($senha !== $confirmarSenha) {
        $erro = "As senhas não coincidem.";
    } else {
 
        $sql_verifica = "SELECT email FROM usuarios WHERE email = ?";
        $stmt_verifica = $conexao->prepare($sql_verifica);
        if ($stmt_verifica === false) {
            $erro = "Erro ao preparar consulta de verificação: " . $conexao->error;
        } else {
            $stmt_verifica->bind_param("s", $email);
            $stmt_verifica->execute();
            $resultado_verifica = $stmt_verifica->get_result();

            if ($resultado_verifica->num_rows > 0) {
                $erro = "Este e-mail já está cadastrado.";
            } else {
                
                $sql_insere = "INSERT INTO usuarios (email, senha, data_cadastro) VALUES (?, ?, CURRENT_TIMESTAMP)";
                $stmt_insere = $conexao->prepare($sql_insere);
                if ($stmt_insere === false) {
                    $erro = "Erro ao preparar consulta de inserção: " . $conexao->error;
                } else {
                    $stmt_insere->bind_param("ss", $email, $senha);
                    if ($stmt_insere->execute()) {
                        $erro = "Cadastro realizado com sucesso!";
                        header("Location: ../../login.php"); 
                        exit();
                    } else {
                        $erro = "Erro ao executar inserção: " . $stmt_insere->error;
                    }
                    $stmt_insere->close();
                }
            }
            $stmt_verifica->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <title>Página de Login</title>
</head>

<body>
    <main id="form_container">
        <form action="" id="formCadastro" method="POST">
            <div class="titulo">
                <h3>Faça o seu cadastro</h3>
                <div class="barra-horizontal"></div>
            </div>

            <div class="campo-input">
                <label for="e-mail">Seu e-mail*</label>
                <input type="email" id="e-mail" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
            </div>

            <div class="campo-input senha-container">
                <label for="senha">Sua senha*</label>
                <div class="input-com-icone">
                    <input type="password" id="senha" name="senha" required />
                    <i id="toggleSenha" class="fa-solid fa-eye"></i>
                </div>
            </div>

            <div class="campo-input senha-container">
                <label for="confirmar-senha">Confirme sua senha*</label>
                <div class="input-com-icone">
                    <input type="password" id="confirmar-senha" name="confirmar" required />
                    <i id="toggleConfirmarSenha" class="fa-solid fa-eye"></i>
                </div>
            </div>

            <button type="submit" id="btnCadastrar">Cadastrar</button>
            <?php if ($erro) echo "<p style='color: red; text-align: center;'>$erro</p>"; ?>
            <p class="cadastro">
                Já possui cadastro?
                <a href="../../login.php">Fazer login</a>
            </p>
        </form>
    </main>

    <script src="cadastro.js"></script>
</body>

</html>