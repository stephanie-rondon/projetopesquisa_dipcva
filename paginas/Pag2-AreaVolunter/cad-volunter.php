<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true)) {
    header("Location: ../../login.php");
    exit();
}

include '../../backend/conexao.php';

$erro = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['name'] ?? '');
    $data_nascimento = trim($_POST['birthdate'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $rg = trim($_POST['rg'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $genero = trim($_POST['gender'] ?? '');
    $altura = trim($_POST['altura'] ?? '');
    $peso = trim($_POST['peso'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $orgao_up = trim($_POST['orgao_up'] ?? '');
    $profissao = trim($_POST['profissao'] ?? '');
    $nacionalidade = trim($_POST['nacionalidade'] ?? '');
    $estado_civil = trim($_POST['estado_civil'] ?? '');
    $alergias = trim($_POST['alergias'] ?? '');
    $medicamentos = trim($_POST['medicamentos'] ?? '');
    $restricoes = trim($_POST['restricoes'] ?? '');
    $doencas = trim($_POST['doencas'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');
    $funcao = trim($_POST['funcao'] ?? '');
    $tipo_acampamento = trim($_POST['tipo_acampamento'] ?? '');

    $concordou_termos = isset($_POST['concordou_termos']) && $_POST['concordou_termos'] === '1';

    $documento = null;
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == UPLOAD_ERR_OK) {
        $nome_arquivo = basename($_FILES['documento']['name']);
        $nome_arquivo = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $nome_arquivo);
        $destino = "uploads/" . $nome_arquivo;
        $tipos_permitidos = ['pdf', 'jpg', 'jpeg', 'png'];
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
        if (in_array($extensao, $tipos_permitidos)) {
            if (move_uploaded_file($_FILES['documento']['tmp_name'], $destino)) {
                $documento = $destino;
            } else {
                $erro = "Erro ao mover o arquivo para o servidor. Verifique as permissões da pasta uploads.";
            }
        } else {
            $erro = "Tipo de arquivo não permitido. Use PDF, JPG, JPEG ou PNG.";
        }
    } else {
        $erro = "O upload do RG ou CNH é obrigatório. Erro: " . $_FILES['documento']['error'];
    }

    $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
    if ($id_usuario) {
        $sql_verifica = "SELECT COUNT(*) as total FROM voluntarios WHERE id_usuario = ?";
        $stmt_verifica = $conexao->prepare($sql_verifica);
        $stmt_verifica->bind_param("i", $id_usuario);
        $stmt_verifica->execute();
        $result_verifica = $stmt_verifica->get_result();
        $row = $result_verifica->fetch_assoc();
        if ($row['total'] > 0) {
            $erro = "Você já enviou uma ficha de voluntariado. Apenas um envio é permitido por usuário.";
        }
        $stmt_verifica->close();
    } else {
        $erro = "Erro: Usuário não identificado. Faça login novamente.";
    }

    $numero = empty($numero) ? null : $numero;
    $bairro = empty($bairro) ? null : $bairro;
    $cidade = empty($cidade) ? null : $cidade;
    $estado = empty($estado) ? null : $estado;
    $cep = empty($cep) ? null : $cep;
    $orgao_up = empty($orgao_up) ? null : $orgao_up;
    $profissao = empty($profissao) ? null : $profissao;
    $nacionalidade = empty($nacionalidade) ? null : $nacionalidade;
    $estado_civil = empty($estado_civil) ? null : $estado_civil;
    $alergias = empty($alergias) ? null : $alergias;
    $medicamentos = empty($medicamentos) ? null : $medicamentos;
    $restricoes = empty($restricoes) ? null : $restricoes;
    $doencas = empty($doencas) ? null : $doencas;
    $observacoes = empty($observacoes) ? null : $observacoes;
    $funcao = empty($funcao) ? null : $funcao;
    $tipo_acampamento = empty($tipo_acampamento) ? null : $tipo_acampamento;

    if (empty($nome) || empty($data_nascimento) || empty($cpf) || empty($rg) || empty($email) || empty($telefone) || empty($genero) || empty($altura) || empty($peso) || $documento === null || !$concordou_termos || empty($funcao) || empty($tipo_acampamento)) {
        $erro = "Por favor, preencha todos os campos obrigatórios: Nome, Data de Nascimento, CPF, RG, E-mail, Telefone, Gênero, Altura, Peso, anexe um RG ou CNH, concorde com os termos, escolha uma função e o tipo de acampamento.";
    } elseif (empty($erro) && $id_usuario) {
        $sql_insere = "INSERT INTO voluntarios (id_usuario, nome, data_nascimento, cpf, rg, email, telefone, genero, altura, peso, numero, bairro, cidade, estado, cep, orgao_up, profissao, nacionalidade, estado_civil, alergias, medicamentos, restricoes, doencas, observacoes, documento, funcao, tipo_acampamento, data_cadastro, status, concordou_termos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 'pendente', ?)";
        $stmt_insere = $conexao->prepare($sql_insere);
        if ($stmt_insere === false) {
            $erro = "Erro ao preparar consulta: " . $conexao->error;
        } else {
            $concordou_termos_val = $concordou_termos ? 1 : 0;
            $params = array($id_usuario, $nome, $data_nascimento, $cpf, $rg, $email, $telefone, $genero, $altura, $peso, $numero, $bairro, $cidade, $estado, $cep, $orgao_up, $profissao, $nacionalidade, $estado_civil, $alergias, $medicamentos, $restricoes, $doencas, $observacoes, $documento, $funcao, $tipo_acampamento, $concordou_termos_val);
            $types = "issssssssssssssssssssssssssi";
            if (count($params) !== strlen($types)) {
                $erro = "Erro: Número de parâmetros (" . count($params) . ") não corresponde à string de tipo (" . strlen($types) . "). Parâmetros: " . implode(", ", $params);
            } else {
                $bind_names = array();
                for ($i = 0; $i < count($params); $i++) {
                    $bind_names[] = &$params[$i];
                }
                call_user_func_array(array($stmt_insere, 'bind_param'), array_merge(array($types), $bind_names));
                if ($stmt_insere->execute()) {
                    $erro = "Ficha de voluntariado enviada com sucesso! Aguarde aprovação.";
                    header("Location: cad-volunter.php");
                    exit();
                } else {
                    $erro = "Erro ao cadastrar voluntário: " . $stmt_insere->error;
                }
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
    <link rel="stylesheet" href="cad-volunter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Cadastro</title>
</head>
<body>
    <main id="form_container">
        <div id="form_header">
            <h1 id="form_title">
                Ficha de voluntariado
            </h1>
            <button onclick="window.location.href='home.php'" class="btn-default-i">
                <i class="fa-solid fa-right-to-bracket"></i>
            </button>
        </div>
        <form action="" method="POST" id="formVoluntario" enctype="multipart/form-data">
            <div id="input_container">
                <div class="input-box">
                    <label for="name" class="form-label">
                        Nome completo *
                    </label>
                    <div class="input-field">
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" placeholder="" required>
                        <i class="fa-regular fa-user"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="birthdate" class="form-label">
                        Data de nascimento *
                    </label>
                    <div class="input-field">
                        <input type="date" name="birthdate" id="birthdate" class="form-control" value="<?php echo isset($_POST['birthdate']) ? htmlspecialchars($_POST['birthdate']) : ''; ?>" required>
                    </div>
                </div>
                <div class="input-box">
                    <label for="cpf" class="form-label">
                        CPF *
                    </label>
                    <div class="input-field">
                        <input type="tel" name="cpf" id="cpf" class="form-control" value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" placeholder="123.456.789-00" required>
                        <i class="fa-regular fa-user"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="rg" class="form-label">
                        RG *
                    </label>
                    <div class="input-field">
                        <input type="tel" name="rg" id="rg" class="form-control" value="<?php echo isset($_POST['rg']) ? htmlspecialchars($_POST['rg']) : ''; ?>" placeholder="" required>
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="email" class="form-label">
                        E-mail *
                    </label>
                    <div class="input-field">
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="exemplo@gmail.com" required>
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="telefone" class="form-label">
                        Telefone *
                    </label>
                    <div class="input-field">
                        <input type="tel" name="telefone" id="telefone" class="form-control" value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" placeholder="(--) 99628-8313" required>
                        <i class="fa-solid fa-phone"></i>
                    </div>
                </div>
                <div class="radio-container">
                    <label class="form-label">
                        Gênero *
                    </label>
                    <div id="gender_inputs">
                        <div class="radio-box">
                            <input type="radio" name="gender" id="female" class="form-control" value="female" <?php echo isset($_POST['gender']) && $_POST['gender'] == 'female' ? 'checked' : ''; ?> required>
                            <label for="female" class="form-label">Feminino</label>
                        </div>
                        <div class="radio-box">
                            <input type="radio" name="gender" id="male" class="form-control" value="male" <?php echo isset($_POST['gender']) && $_POST['gender'] == 'male' ? 'checked' : ''; ?> required>
                            <label for="male" class="form-label">Masculino</label>
                        </div>
                    </div>
                </div>
                <div class="input-box">
                    <label for="numero" class="form-label">
                        Número
                    </label>
                    <div class="input-field">
                        <input type="text" name="numero" id="numero" class="form-control" value="<?php echo isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : ''; ?>" placeholder="Ex: 123">
                        <i class="fa-solid fa-hashtag"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="bairro" class="form-label">
                        Bairro
                    </label>
                    <div class="input-field">
                        <input type="text" name="bairro" id="bairro" class="form-control" value="<?php echo isset($_POST['bairro']) ? htmlspecialchars($_POST['bairro']) : ''; ?>" placeholder="Ex: Centro">
                        <i class="fa-solid fa-map-pin"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="cidade" class="form-label">
                        Cidade
                    </label>
                    <div class="input-field">
                        <input type="text" name="cidade" id="cidade" class="form-control" value="<?php echo isset($_POST['cidade']) ? htmlspecialchars($_POST['cidade']) : ''; ?>" placeholder="Ex: São Paulo">
                        <i class="fa-solid fa-city"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="estado" class="form-label">
                        Estado
                    </label>
                    <div class="input-field">
                        <input type="text" name="estado" id="estado" class="form-control" value="<?php echo isset($_POST['estado']) ? htmlspecialchars($_POST['estado']) : ''; ?>" placeholder="Ex: SP">
                        <i class="fa-solid fa-map-marked-alt"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="cep" class="form-label">
                        CEP
                    </label>
                    <div class="input-field">
                        <input type="text" name="cep" id="cep" class="form-control" value="<?php echo isset($_POST['cep']) ? htmlspecialchars($_POST['cep']) : ''; ?>" placeholder="Ex: 12345-678">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="orgao_up" class="form-label">
                        Orgão/UP
                    </label>
                    <div class="input-field">
                        <input type="text" name="orgao_up" id="orgao_up" class="form-control" value="<?php echo isset($_POST['orgao_up']) ? htmlspecialchars($_POST['orgao_up']) : ''; ?>" placeholder="Ex: SSP">
                        <i class="fa-solid fa-building"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="profissao" class="form-label">
                        Profissão
                    </label>
                    <div class="input-field">
                        <input type="text" name="profissao" id="profissao" class="form-control" value="<?php echo isset($_POST['profissao']) ? htmlspecialchars($_POST['profissao']) : ''; ?>" placeholder="Ex: Engenheiro">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="nacionalidade" class="form-label">
                        Nacionalidade
                    </label>
                    <div class="input-field">
                        <input type="text" name="nacionalidade" id="nacionalidade" class="form-control" value="<?php echo isset($_POST['nacionalidade']) ? htmlspecialchars($_POST['nacionalidade']) : ''; ?>" placeholder="Ex: Brasileira">
                        <i class="fa-solid fa-flag"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="estado_civil" class="form-label">
                        Estado Civil
                    </label>
                    <div class="input-field">
                        <input type="text" name="estado_civil" id="estado_civil" class="form-control" value="<?php echo isset($_POST['estado_civil']) ? htmlspecialchars($_POST['estado_civil']) : ''; ?>" placeholder="Ex: Solteiro">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="funcao" class="form-label">
                        Função *
                    </label>
                    <div class="input-field">
                        <select name="funcao" id="funcao" class="form-control" required>
                            <option value="" disabled <?php echo !isset($_POST['funcao']) ? 'selected' : ''; ?>>Selecione uma função</option>
                            <option value="Manutenção" <?php echo isset($_POST['funcao']) && $_POST['funcao'] == 'Manutenção' ? 'selected' : ''; ?>>Manutenção</option>
                            <option value="Saúde" <?php echo isset($_POST['funcao']) && $_POST['funcao'] == 'Saúde' ? 'selected' : ''; ?>>Saúde</option>
                            <option value="Cozinha" <?php echo isset($_POST['funcao']) && $_POST['funcao'] == 'Cozinha' ? 'selected' : ''; ?>>Cozinha</option>
                            <option value="Recreação" <?php echo isset($_POST['funcao']) && $_POST['funcao'] == 'Recreação' ? 'selected' : ''; ?>>Recreação</option>
                            <option value="Música" <?php echo isset($_POST['funcao']) && $_POST['funcao'] == 'Música' ? 'selected' : ''; ?>>Música</option>
                            <option value="Intercessão" <?php echo isset($_POST['funcao']) && $_POST['funcao'] == 'Intercessão' ? 'selected' : ''; ?>>Intercessão</option>
                            <option value="Missão" <?php echo isset($_POST['funcao']) && $_POST['funcao'] == 'Missão' ? 'selected' : ''; ?>>Missão</option>
                            <option value="Corujinha" <?php echo isset($_POST['funcao']) && $_POST['funcao'] == 'Corujinha' ? 'selected' : ''; ?>>Corujinha</option>
                        </select>
                    </div>
                </div>
                <div class="input-box">
                    <label for="tipo_acampamento" class="form-label">
                        Tipo de Acampamento *
                    </label>
                    <div class="input-field">
                        <select name="tipo_acampamento" id="tipo_acampamento" class="form-control" required>
                            <option value="" disabled <?php echo !isset($_POST['tipo_acampamento']) ? 'selected' : ''; ?>>Selecione o tipo de acampamento</option>
                            <option value="Manaim" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Manaim' ? 'selected' : ''; ?>>Manaim</option>
                            <option value="CES" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'CES' ? 'selected' : ''; ?>>CES</option>
                            <option value="Mirim" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Mirim' ? 'selected' : ''; ?>>Mirim</option>
                            <option value="Mulheres" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Mulheres' ? 'selected' : ''; ?>>Mulheres</option>
                            <option value="Educadores" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Educadores' ? 'selected' : ''; ?>>Educadores</option>
                            <option value="Juvenil" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Juvenil' ? 'selected' : ''; ?>>Juvenil</option>
                            <option value="FAC" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'FAC' ? 'selected' : ''; ?>>FAC</option>
                            <option value="Joam" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Joam' ? 'selected' : ''; ?>>Joam</option>
                            <option value="Raízes" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Raízes' ? 'selected' : ''; ?>>Raízes</option>
                            <option value="No limite" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'No limite' ? 'selected' : ''; ?>>No limite</option>
                            <option value="Casais" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Casais' ? 'selected' : ''; ?>>Casais</option>
                            <option value="Família" <?php echo isset($_POST['tipo_acampamento']) && $_POST['tipo_acampamento'] == 'Família' ? 'selected' : ''; ?>>Família</option>
                        </select>
                    </div>
                </div>
                <div class="input-box input-file">
                    <label for="documento" class="form-label">
                        Anexar RG ou CNH *
                    </label>
                    <div class="input-field">
                        <input type="file" name="documento" id="documento" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                <h2 class="form-section-title">Ficha de Saúde</h2>
                <div class="input-box">
                    <label for="altura" class="form-label">
                        Altura (em cm) *
                    </label>
                    <div class="input-field">
                        <input type="number" name="altura" id="altura" class="form-control" value="<?php echo isset($_POST['altura']) ? htmlspecialchars($_POST['altura']) : ''; ?>" placeholder="Ex: 165" required>
                        <i class="fa-solid fa-ruler-vertical"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="peso" class="form-label">
                        Peso (em kg) *
                    </label>
                    <div class="input-field">
                        <input type="number" name="peso" id="peso" class="form-control" value="<?php echo isset($_POST['peso']) ? htmlspecialchars($_POST['peso']) : ''; ?>" placeholder="Ex: 60" required>
                        <i class="fa-solid fa-weight-scale"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="doencas" class="form-label">É portador de alguma doença, sendo ela, neurológicas, cardíaca, hipertensão, diabetes, algum tipo de transtornos, doenças autoimunes, síndromes ou deficiência?</label>
                    <div class="input-field">
                        <input type="text" name="doencas" id="doencas" class="form-control" value="<?php echo isset($_POST['doencas']) ? htmlspecialchars($_POST['doencas']) : ''; ?>" placeholder="Descreva">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="alergias" class="form-label">Possui alergias?</label>
                    <div class="input-field">
                        <input type="text" name="alergias" id="alergias" class="form-control" value="<?php echo isset($_POST['alergias']) ? htmlspecialchars($_POST['alergias']) : ''; ?>" placeholder="Descreva aqui">
                        <i class="fa-solid fa-syringe"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="medicamentos" class="form-label">Usa medicamentos contínuos?</label>
                    <div class="input-field">
                        <input type="text" name="medicamentos" id="medicamentos" class="form-control" value="<?php echo isset($_POST['medicamentos']) ? htmlspecialchars($_POST['medicamentos']) : ''; ?>" placeholder="Quais?">
                        <i class="fa-solid fa-pills"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="restricoes" class="form-label">Restrições alimentares</label>
                    <div class="input-field">
                        <input type="text" name="restricoes" id="restricoes" class="form-control" value="<?php echo isset($_POST['restricoes']) ? htmlspecialchars($_POST['restricoes']) : ''; ?>" placeholder="Descreva">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                </div>
                <div class="input-box">
                    <label for="observacoes" class="form-label">Outras observações importantes</label>
                    <div class="input-field">
                        <input type="text" name="observacoes" id="observacoes" class="form-control" value="<?php echo isset($_POST['observacoes']) ? htmlspecialchars($_POST['observacoes']) : ''; ?>" placeholder="Se houver">
                        <i class="fa-regular fa-clipboard"></i>
                    </div>
                </div>
                <div class="input-box terms-container">
                    <label class="form-label">
                        <input type="checkbox" name="concordou_termos" id="concordou_termos" value="1" <?php echo isset($_POST['concordou_termos']) ? 'checked' : ''; ?> required>
                        Concordo com os termos e condições *
                    </label>
                    <a href="../../TERMO.pdf" target="_blank" class="terms-link">Ver termos (PDF)</a>
                </div>
                <button type="submit" class="btn-default">Enviar</button>
            </div>
            <?php if ($erro) echo "<p style='color: red; text-align: center; margin-top: 10px;'>$erro</p>"; ?>
        </form>
    </main>
    <script src="script.js"></script>
</body>
</html>