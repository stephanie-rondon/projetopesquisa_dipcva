//essa const guarda o nome do voluntario e a situação (ativo/pendente/expirado) do seu termo de adesão de voluntariado (OBS: o gabriel e o rogerio ali só foram testes usados por mim)
const data = [
    {
    title: "Nome do Voluntario",
    description: "Status do Termo",
    },
    {
    title: "exemplo: Gabriel",
    description: "Pendente",
},
];

const cardContainer = document.querySelector(".card-container");
const searchInput = document.querySelector ("#searchInput");

const displayData = data => {
    cardContainer.innerHTML = "";
    data.forEach (e =>{
        cardContainer.innerHTML += `
        <div class="card" onclick="details()">
        <h3>${e.title}</h3>
        <p> ${e.description}</p>
        </div>
        `
    })
}

searchInput.addEventListener("keyup", (e) => {
    const search = data.filter (i => i.title.toLocaleLowerCase().includes(e.target.value.toLocaleLowerCase()));
    displayData(search);
})

window.addEventListener("load",displayData.bind(null,data))

function details() {
    location.href="detalhes.html"
}
