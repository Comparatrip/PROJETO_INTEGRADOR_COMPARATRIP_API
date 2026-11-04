
async function cadastrarUsuario() {

    const dados = {
        nome: document.getElementById("nome").value,
        email: document.getElementById("email").value,
        telefone: document.getElementById("telefone").value,
        cpf: document.getElementById("cpf").value,
        senha: document.getElementById("senha").value,
        data_nascimento:
            document.getElementById("data_nascimento").value
    };

    try {

        const resposta = await fetch(
            "http://localhost/COMPARATRIP/api/cadastro.php",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(dados)
            }
        );

        const resultado = await resposta.json();

        if (!resposta.ok) {
            alert(resultado.erro);
            return;
        }

        alert(resultado.mensagem);

        window.location.href = "login.html";

    } catch (erro) {

        console.error(erro);
        alert("Não foi possível realizar o cadastro.");

    }

}
