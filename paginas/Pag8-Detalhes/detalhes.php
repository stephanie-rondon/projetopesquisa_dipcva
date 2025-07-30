<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}

include '../../backend/conexao.php';

$nome = urldecode($_GET['nome'] ?? '');
if (empty($nome)) {
    die("Nenhum voluntário selecionado.");
}

error_log("Nome recebido: " . $nome);

$sql = "SELECT * FROM voluntarios WHERE LOWER(nome) = LOWER(?) LIMIT 1";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $nome);
$stmt->execute();
$result = $stmt->get_result();
$voluntario = $result->fetch_assoc();
$stmt->close();

if (!$voluntario) {
    die("Voluntário não encontrado. Nome buscado: " . htmlspecialchars($nome));
}


$data_validade = date('d/m/Y', strtotime($voluntario['data_cadastro'] . ' +2 years'));

$rg_imagem = $voluntario['documento'] ?? null;
if ($rg_imagem && file_exists('../Pag2-AreaVolunter/uploads' . $rg_imagem)) {
    $rg_imagem_src = '../Pag2-AreaVolunter/uploads' . $rg_imagem;
} else {
    $rg_imagem_src = "https://popbooksonline.com/wp-content/uploads/2018/03/image-placeholder-500x500.jpg"; // Fallback
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
        <button class="butao" type="submit" onclick="retornar()">
            <img title="Voltar a página de busca" class="butao" src="https://static.vecteezy.com/ti/vetor-gratis/p1/21797173-seta-esquerda-icone-isolado-em-branco-fundo-vetor.jpg">
        </button>
        <h2 id="dis">Data de Validade da ficha:</h2>
        <h2 id="claimer"><?php echo htmlspecialchars($data_validade); ?></h2>
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
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>Nacionalidade:</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>Estado Civil:</p>
            <div class="resposta">
                <p>Não informado</p>
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
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>CPF:</p>
            <div class="resposta">
                <p><?php echo htmlspecialchars($voluntario['cpf'] ?? 'Não informado'); ?></p>
            </div>
        </div>
        <div class="tt">
            <p>Residente/Domiciliada na:</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>Número:</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>Bairro:</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>Cidade:</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>Estado:</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>CEP:</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>
        <div class="tt">
            <p>O Voluntário Concordou com as Clausulas?</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>
    </section>
    <section id="ds">
        <h2>Informações Relacionadas a Saúde do Voluntário</h2>
        <div class="tt">
            <p>É portador de alguma doença, sendo ela, neurológicas, cardíaca, hipertensão, diabetes, algum tipo de transtornos, doenças autoimunes, síndromes ou deficiência – Relatar abaixo qualquer uma (Caso não, Estará em branco):</p>
            <div class="resposta">
                <p><?php echo htmlspecialchars($voluntario['doencas'] ?? 'Não informado'); ?></p>
                <br>
            </div>
        </div>
        <div class="tt">
            <p>Situações que já ocorreram e que tenham influência direta na sua saúde ou integridade física, como por exemplo, cirurgias (restrição) uso de próteses (dentária) crise convulsiva, epilepsia, desmaios etc. Caso não, Estará em branco.</p>
            <div class="resposta">
                <p><?php echo htmlspecialchars($voluntario['observacoes'] ?? 'Não informado'); ?></p>
                <br>
            </div>
        </div>
        <div class="tt">
            <p>Tem Convênio? Qual?</p>
            <div class="resposta">
                <p>Não informado</p>
                <br>
            </div>
        </div>
        <div class="tt">
            <p>Toma alguma medicação de uso contínuo e/ou controlado. Qual ou quais?</p>
            <div class="resposta">
                <p><?php echo htmlspecialchars($voluntario['medicamentos'] ?? 'Não informado'); ?></p>
                <br>
            </div>
        </div>
        <div class="tt">
            <p>Em caso de dor, qual medicamento utiliza?</p>
            <div class="resposta">
                <p>Não informado</p>
                <br>
            </div>
        </div>
        <div class="tt">
            <p>Quanto a ALERGIAS, responda sim ou não, sendo “sim” descreva:</p>
            <div class="resposta">
                <p><?php echo htmlspecialchars($voluntario['alergias'] ?? 'Não informado'); ?></p>
            </div>
        </div>  
        <div class="tt">
            <p>É alérgico a alguma medicação? Qual?</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>      
        <div class="tt">
            <p>É alérgico a algum alimento? Qual?</p>
            <div class="resposta">
                <p>Não informado</p>
            </div>
        </div>   
        <div class="tt">
            <p>É alérgico a picada de abelha? Qual?</p>
            <div class="resposta">
                <p>Não informado</p>
                <br>
            </div>
        </div>                       
        <div class="tt">
            <p>OBSERVAÇÕES: Qualquer informação que julge ser útil relacionado a sua saúde.</p>
            <div class="resposta">
                <p><?php echo htmlspecialchars($voluntario['observacoes'] ?? 'Não informado'); ?></p>
            </div>
        </div>                   
    </section>  
    </section>
</body>
</html>