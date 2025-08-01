<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../login.php");
    exit();
}

include '../../backend/conexao.php';

$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT * FROM voluntarios WHERE id_usuario = ? LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$ficha = $result->fetch_assoc();
$stmt->close();

$data_envio = $ficha ? $ficha['data_cadastro'] : null;
$data_vencimento = $data_envio ? date('Y-m-d', strtotime($data_envio . ' +2 years')) : null;
$status = $ficha ? $ficha['status'] : 'Não enviada';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <title>Meus Documentos</title>
</head>
<body>
    <main class="container">
        <div class="title">
            <h2>Meus documentos</h2>
        </div>

        <div class="documento-card">
            <h3>Ficha de Voluntariado</h3>
            <p>Enviado em: <span class="data-envio"><?php echo $data_envio ? $data_envio : 'Não enviada'; ?></span></p>
            <p>Vence em: <span class="data-vencimento"><?php echo $data_vencimento ? $data_vencimento : 'Não aplicável'; ?></span></p>
            <p class="status" id="statusFicha"><?php echo "Status: $status"; ?></p>
            <?php if ($ficha): ?>
                <div class="ficha-dados">
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($ficha['nome']); ?></p>
                    <p><strong>CPF:</strong> <?php echo htmlspecialchars($ficha['cpf']); ?></p>
                    <p><strong>RG:</strong> <?php echo htmlspecialchars($ficha['rg']); ?></p>
                    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($ficha['email']); ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($ficha['telefone']); ?></p>
                    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($ficha['genero']); ?></p>
                    <p><strong>Número:</strong> <?php echo htmlspecialchars($ficha['numero'] ?: 'Nenhum'); ?></p>
                    <p><strong>Bairro:</strong> <?php echo htmlspecialchars($ficha['bairro'] ?: 'Nenhum'); ?></p>
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($ficha['cidade'] ?: 'Nenhuma'); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($ficha['estado'] ?: 'Nenhum'); ?></p>
                    <p><strong>CEP:</strong> <?php echo htmlspecialchars($ficha['cep'] ?: 'Nenhum'); ?></p>
                    <p><strong>Orgão/UP:</strong> <?php echo htmlspecialchars($ficha['orgao_up'] ?: 'Nenhum'); ?></p>
                    <p><strong>Profissão:</strong> <?php echo htmlspecialchars($ficha['profissao'] ?: 'Nenhuma'); ?></p>
                    <p><strong>Nacionalidade:</strong> <?php echo htmlspecialchars($ficha['nacionalidade'] ?: 'Nenhuma'); ?></p>
                    <p><strong>Estado Civil:</strong> <?php echo htmlspecialchars($ficha['estado_civil'] ?: 'Nenhum'); ?></p>
                    <p><strong>Altura:</strong> <?php echo htmlspecialchars($ficha['altura']); ?> cm</p>
                    <p><strong>Peso:</strong> <?php echo htmlspecialchars($ficha['peso']); ?> kg</p>
                    <p><strong>Alergias:</strong> <?php echo htmlspecialchars($ficha['alergias'] ?: 'Nenhuma'); ?></p>
                    <p><strong>Medicamentos:</strong> <?php echo htmlspecialchars($ficha['medicamentos'] ?: 'Nenhum'); ?></p>
                    <p><strong>Restrições Alimentares:</strong> <?php echo htmlspecialchars($ficha['restricoes'] ?: 'Nenhuma'); ?></p>
                    <p><strong>Doenças Pré-existentes:</strong> <?php echo htmlspecialchars($ficha['doencas'] ?: 'Nenhuma'); ?></p>
                    <p><strong>Observações:</strong> <?php echo htmlspecialchars($ficha['observacoes'] ?: 'Nenhuma'); ?></p>
                    <p><strong>Função:</strong> <?php echo htmlspecialchars($ficha['funcao'] ?: 'Não informada'); ?></p>
                    <p><strong>Tipo de Acampamento:</strong> <?php echo htmlspecialchars($ficha['tipo_acampamento'] ?: 'Não informado'); ?></p>
                </div>
            <?php else: ?>
                <p>Não há ficha de voluntariado registrada.</p>
            <?php endif; ?>
        </div>

        <div class="documento-card">
            <h3>RG</h3>
            <p>Enviado em: <span class="data-envio"><?php echo $data_envio ? $data_envio : 'Não enviada'; ?></span></p>
            <p>Vence em: <span class="data-vencimento"><?php echo $data_vencimento ? $data_vencimento : 'Não aplicável'; ?></span></p>
            <p class="status" id="statusRG"><?php echo "Status: $status"; ?></p>
            <?php if ($ficha && $ficha['documento']): ?>
                <p>RG enviado com sucesso.</p>
            <?php else: ?>
                <p>RG não enviado.</p>
            <?php endif; ?>
        </div>

        <button onclick="window.location.href='../Pag2-AreaVolunter/home.php'" id="btnVoltar" class="botao">Voltar</button>
    </main>

    <script src="script.js"></script>
</body>
</html>