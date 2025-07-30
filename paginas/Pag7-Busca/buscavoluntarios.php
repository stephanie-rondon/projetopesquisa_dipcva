<?php
session_start();

// Verifica se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}

include '../../backend/conexao.php';

// Busca todos os voluntários
$sql = "SELECT nome, status, data_cadastro FROM voluntarios";
$result = $conexao->query($sql);
$voluntarios = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $voluntarios[] = [
            'title' => htmlspecialchars($row['nome']),
            'description' => ucfirst(strtolower($row['status'])), // Normaliza para primeira letra maiúscula
            'data_cadastro' => htmlspecialchars($row['data_cadastro'])
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busca de Voluntários</title>
    <link rel="stylesheet" href="buscavoluntarios.css">
    <script src="buscavoluntarios.js" defer></script>
</head>
<body>
    <div class="filtros">
        <button class="btn" onclick="filterElements('Pendente')">Pendentes</button>
        <button onclick="filterElements('Ativo')">Ativos</button>
        <button onclick="filterElements('Expirado')">Expirados</button>
        <button onclick="filterAll()">Todos</button>
    </div>
    <div class="pesquisa">
        <input type="text" id="searchInput" placeholder="Pesquisar por Voluntários...">
    </div>
    <div class="card-container" id="card-container">
        <?php
        foreach ($voluntarios as $voluntario) {
            echo "<div class='card {$voluntario['description']}' onclick='details(\"{$voluntario['title']}\")'>";
            echo "<h3>{$voluntario['title']}</h3>";
            echo "<p>{$voluntario['description']}</p>";
            echo "</div>";
        }
        ?>
    </div>
    <script>
        // Dados reais são carregados diretamente no HTML via PHP
        const data = <?php echo json_encode($voluntarios); ?>;

        const cardContainer = document.querySelector("#card-container");
        const searchInput = document.querySelector("#searchInput");

        const displayData = (data) => {
            cardContainer.innerHTML = "";
            data.forEach(e => {
                cardContainer.innerHTML += `
                    <div class="card ${e.description}" onclick="details('${encodeURIComponent(e.title)}')">
                        <h3>${e.title}</h3>
                        <p>${e.description}</p>
                    </div>
                `;
            });
        };

        searchInput.addEventListener("keyup", (e) => {
            const search = data.filter(i => i.title.toLowerCase().includes(e.target.value.toLowerCase()));
            displayData(search);
        });

        window.addEventListener("load", () => displayData(data));

        function details(nome) {
            location.href = `../Pag8-Detalhes/detalhes.php?nome=${encodeURIComponent(nome)}`;
        }

        function filterElements(estado) {
            const cards = document.querySelectorAll(".card");
            cards.forEach(card => {
                const cardState = card.querySelector("p").textContent.trim().toLowerCase(); // Normaliza
                if (cardState === estado.toLowerCase()) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        }

        function filterAll() {
            const cards = document.querySelectorAll(".card");
            cards.forEach(card => {
                card.style.display = "block";
            });
        }
    </script>
</body>
</html>