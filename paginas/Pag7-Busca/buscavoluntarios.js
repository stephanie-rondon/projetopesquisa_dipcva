//essa const guarda o nome do voluntario e a situação do seu termo de adesão de voluntariado (OBS: o gabriel,o rogerio e o Wesley ali só foram testes usados por mim)
const data = [
    {
    title: "gabriel",
    description: "Ativo",
    },
    {
    title: "rogerio",
    description: "Pendente",
},
{
    title: "Wesley",
    description: "Expirado",
},
];

const cardContainer = document.querySelector(".card-container");
const searchInput = document.querySelector ("#searchInput");

const displayData = data => {
    cardContainer.innerHTML = "";
    data.forEach (e =>{
        cardContainer.innerHTML += `
        <div class="card ${e.description}" onclick="details()">
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

function filterElements(estado) {
    console.log(estado)
    const cards = document.querySelectorAll(`.card`);
    console.log(cards)
    cards.forEach (card => {
        console.log(card)
        if(card.classList.contains(estado)) {
            card.style.display = "block";
        }
        else{
            card.style.display = "none"
        }
    }) 
}

function filterAll() {
    const cards = document.querySelectorAll(`.card`);
     cards.forEach (card => {
        card.style.display = "block";
     })
}
