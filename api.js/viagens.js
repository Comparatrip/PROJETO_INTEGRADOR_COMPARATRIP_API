
const API = "http://localhost/COMPARATRIP/api";

// Buscar viagens do banco de dados
async function buscarViagens() {

    try {

        const resposta = await fetch(`${API}/viagens.php`);

        if (!resposta.ok) {
            throw new Error("Erro ao buscar viagens");
        }

        const viagens = await resposta.json();

        const container = document.getElementById("lista-viagens");

        container.innerHTML = "";

        viagens.forEach(viagem => {

            const card = document.createElement("div");

            card.classList.add("card-viagem");

            const titulo = document.createElement("h3");
            titulo.textContent = viagem.nome;

            const destino = document.createElement("p");
            destino.textContent = `${viagem.cidade}, ${viagem.pais}`;

            const preco = document.createElement("p");
            preco.textContent = `R$ ${Number(viagem.preco).toFixed(2)}`;

            const descricao = document.createElement("p");
            descricao.textContent = viagem.descricao;

            const datas = document.createElement("p");
            datas.textContent =
                `Ida: ${viagem.data_ida} | Volta: ${viagem.data_volta}`;

            card.append(
                titulo,
                destino,
                descricao,
                datas,
                preco
            );

            container.appendChild(card);

        });

    } catch (erro) {

        console.error(erro);

    }

}

buscarViagens();
