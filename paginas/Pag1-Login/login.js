document.getElementById('formLogin').addEventListener('submit', function (e) {
    e.preventDefault();

    const email = document.getElementById('e-mail').value.trim();
    const senha = document.getElementById('senha').value.trim();

    if (!email || !senha) {
        alert('Por favor, preencha todos os campos.');
        return;
    }

    const usuariosSalvos = JSON.parse(localStorage.getItem('usuarios')) || [];

    const usuarioEncontrado = usuariosSalvos.find(function (usuario) {
        return usuario.email === email && usuario.senha === senha;
    });

    if (usuarioEncontrado) {
        window.location.href = '../Pag2-AreaVolunter/home.html';
    } else {
        alert('E-mail ou senha inválidos. Tente novamente.');
    }
});

const senhaInput = document.getElementById("senha");
const toggleSenha = document.getElementById("toggleSenha");

toggleSenha.addEventListener("click", () => {
    const tipo = senhaInput.getAttribute("type");
    if (tipo === "password") {
        senhaInput.setAttribute("type", "text");
        toggleSenha.classList.remove("fa-eye");
        toggleSenha.classList.add("fa-eye-slash");
    } else {
        senhaInput.setAttribute("type", "password");
        toggleSenha.classList.remove("fa-eye-slash");
        toggleSenha.classList.add("fa-eye");
    }
});

