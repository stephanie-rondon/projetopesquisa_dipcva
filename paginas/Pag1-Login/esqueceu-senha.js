document.getElementById("recuperação-senha").addEventListener("submit", function (event) {
    event.preventDefault(); 

    const emailInput = document.getElementById("e-mail");
    const email = emailInput.value.trim();

    if (!email) {
        alert("Por favor, preencha o campo de e-mail.");
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        alert("Por favor, insira um e-mail válido.");
        return;
    }

    alert("Se o e-mail estiver cadastrado, você receberá as instruções para recuperar sua senha.");
    emailInput.value = "";
});
