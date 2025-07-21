document.getElementById('formCadastro').addEventListener('submit', function (e) {
    e.preventDefault();

    const email = document.getElementById('e-mail').value.trim();
    const senha = document.getElementById('senha').value;
    const confirmarSenha = document.getElementById('confirmar-senha').value;

    if (!email || !senha || !confirmarSenha) {
        alert("Por favor, preencha todos os campos.");
        return;
    }

    if (senha !== confirmarSenha) {
        alert("As senhas não coincidem.");
        return;
    }

    let usuarios = JSON.parse(localStorage.getItem('usuarios')) || [];

    const usuarioExistente = usuarios.find(user => user.email === email);

    if (usuarioExistente) {
        alert("Este e-mail já está cadastrado.");
        return;
    }

    const novoUsuario = {
        email: email,
        senha: senha
    };

    //localStorage
    usuarios.push(novoUsuario);
    localStorage.setItem('usuarios', JSON.stringify(usuarios));

    alert("Cadastro realizado com sucesso!");
    window.location.href = "login.html";
});

const senhaInput = document.getElementById("senha");
const toggleSenha = document.getElementById("toggleSenha");

const confirmarInput = document.getElementById("confirmar-senha");
const toggleConfirmarSenha = document.getElementById("toggleConfirmarSenha");

function togglePasswordVisibility(input, toggleIcon) {
  if (input.type === "password") {
    input.type = "text";
    toggleIcon.classList.remove("fa-eye");
    toggleIcon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    toggleIcon.classList.remove("fa-eye-slash");
    toggleIcon.classList.add("fa-eye");
  }
}

toggleSenha.addEventListener("click", () => {
  togglePasswordVisibility(senhaInput, toggleSenha);
});

toggleConfirmarSenha.addEventListener("click", () => {
  togglePasswordVisibility(confirmarInput, toggleConfirmarSenha);
});
