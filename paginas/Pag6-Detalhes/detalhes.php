<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}

include '../../backend/conexao.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status']) && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];
    $novo_status = $_POST['status'];
    $sql_update = "UPDATE voluntarios SET status = ? WHERE id_usuario = ?";
    $stmt = $conexao->prepare($sql_update);
    $stmt->bind_param("si", $novo_status, $id_usuario);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: detalhes.php?id=" . $id_usuario);
        exit();
    } else {
        error_log("Erro ao atualizar status: " . $stmt->error);
    }
    $stmt->close();
}


$id_usuario = $_GET['id'] ?? '';
if (empty($id_usuario)) {
    die("Nenhum voluntário selecionado.");
}

error_log("ID recebido: " . $id_usuario);

$sql = "SELECT * FROM voluntarios WHERE id_usuario = ? LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$voluntario = $result->fetch_assoc();
$stmt->close();

if (!$voluntario) {
    die("Voluntário não encontrado. ID buscado: " . htmlspecialchars($id_usuario));
}

$data_validade = date('d/m/Y', strtotime($voluntario['data_cadastro'] . ' +2 years'));

$rg_imagem = $voluntario['documento'] ?? null;
if ($rg_imagem) {
    $base_path = dirname(__DIR__, 2) . '/paginas/Pag2-AreaVolunter/uploads/' . basename($rg_imagem);
    $web_path = '/dipcva/paginas/Pag2-AreaVolunter/uploads/' . basename($rg_imagem);
    error_log("Caminho absoluto: " . $base_path);
    error_log("Caminho web: " . $web_path);
    if (file_exists($base_path)) {
        $rg_imagem_src = $web_path;
    } else {
        error_log("Arquivo não encontrado: " . $base_path);
        $rg_imagem_src = 'https://popbooksonline.com/wp-content/uploads/2018/03/image-placeholder-500x500.jpg';
    }
} else {
    $rg_imagem_src = 'https://popbooksonline.com/wp-content/uploads/2018/03/image-placeholder-500x500.jpg';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Voluntário</title>
    <link rel="stylesheet" href="detalhes.css">
    <script src="detalhes.js" defer></script>
</head>
<body>
    <div id="inicio">
        <button onclick="window.location.href='../Pag5-Busca/buscavoluntarios.php'" id="btnVoltar" class="botao">Voltar</button> 
        <h2 id="dis">Data de Validade da ficha:</h2>
        <h2 id="claimer"><?php echo htmlspecialchars($data_validade); ?></h2>
        <div class="status-buttons">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="id_usuario" value="<?php echo $voluntario['id_usuario']; ?>">
                <input type="hidden" name="status" value="Pendente">
                <button type="submit" class="status-btn <?php echo $voluntario['status'] === 'Pendente' ? 'active' : ''; ?>">Pendente</button>
            </form>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="id_usuario" value="<?php echo $voluntario['id_usuario']; ?>">
                <input type="hidden" name="status" value="Ativo">
                <button type="submit" class="status-btn <?php echo $voluntario['status'] === 'Ativo' ? 'active' : ''; ?>">Ativo</button>
            </form>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="id_usuario" value="<?php echo $voluntario['id_usuario']; ?>">
                <input type="hidden" name="status" value="Expirado">
                <button type="submit" class="status-btn <?php echo $voluntario['status'] === 'Expirado' ? 'active' : ''; ?>">Expirado</button>
            </form>
        </div>
    </div>  
    <section id="tudo">
        <img id="placeholder" src="<?php echo htmlspecialchars($rg_imagem_src); ?>" alt="Imagem do RG">
        <section id="info">
            <h2>Detalhes do Voluntário</h2>
            <div class="tt">
                <p>Nome:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['nome'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Profissão:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['profissao'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Nacionalidade:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['nacionalidade'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Estado Civil:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['estado_civil'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>RG:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['rg'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Orgão/UP:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['orgao_up'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>CPF:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['cpf'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Número:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['numero'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Bairro:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['bairro'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Cidade:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['cidade'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Estado:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['estado'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>CEP:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['cep'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Gênero:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['genero'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Data de Nascimento:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['data_nascimento'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>E-mail:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['email'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Telefone:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['telefone'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Função:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['funcao'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Tipo de Acampamento:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['tipo_acampamento'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>O Voluntário Concordou com as Clausulas?</p>
                <div class="resposta">
                    <p><?php echo $voluntario['concordou_termos'] ? 'Sim' : 'Não'; ?></p>
                </div>
            </div>
        </section>
        <section id="ds">
            <h2>Informações Relacionadas a Saúde do Voluntário</h2>
            <div class="tt">
                <p>É portador de alguma doença, sendo ela, neurológicas, cardíaca, hipertensão, diabetes, algum tipo de transtornos, doenças autoimunes, síndromes ou deficiência?</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['doencas'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Possui alergias?</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['alergias'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Usa medicamentos contínuos?</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['medicamentos'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Restrições alimentares:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['restricoes'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Outras observações importantes:</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['observacoes'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Altura (cm):</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['altura'] ?? 'Não informado'); ?></p>
                </div>
            </div>
            <div class="tt">
                <p>Peso (kg):</p>
                <div class="resposta">
                    <p><?php echo htmlspecialchars($voluntario['peso'] ?? 'Não informado'); ?></p>
                </div>
            </div>
        </section>
    </section>
</body>
</html>