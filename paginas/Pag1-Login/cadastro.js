const senhaInput = document.getElementById("senha");
const toggleSenha = document.getElementById("toggleSenha");

const confirmarInput = document.getElementById("confirmar-senha");
const toggleConfirmarSenha = document.getElementById("toggleConfirmarSenha");

function togglePasswordVisibility(input, toggleIcon) {
    if (input.type === "password") {
        input.type = "text";
        toggleIcon.classList.remove("fa-solid fa-eye");
        toggleIcon.classList.add("fa-solid fa-eye-slash");
    } else {
        input.type = "password";
        toggleIcon.classList.remove("fa-solid fa-eye-slash");
        toggleIcon.classList.add("fa-solid fa-eye");
    }
}

toggleSenha.addEventListener("click", () => {
    togglePasswordVisibility(senhaInput, toggleSenha);
});

toggleConfirmarSenha.addEventListener("click", () => {
    togglePasswordVisibility(confirmarInput, toggleConfirmarSenha);
});