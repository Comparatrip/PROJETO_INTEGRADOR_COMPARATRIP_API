
<?php

require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido."]);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

$id_usuario = $dados["id_usuario"] ?? 0;
$id_viagem = $dados["id_viagem"] ?? 0;
$quantidade = $dados["quantidade_passageiros"] ?? 0;

if (
    !filter_var($id_usuario, FILTER_VALIDATE_INT) ||
    !filter_var($id_viagem, FILTER_VALIDATE_INT) ||
    !filter_var($quantidade, FILTER_VALIDATE_INT) ||
    $id_usuario <= 0 ||
    $id_viagem <= 0 ||
    $quantidade <= 0
) {
    http_response_code(400);
    echo json_encode(["erro" => "Dados da reserva inválidos."]);
    exit;
}

try {

    $pdo->beginTransaction();

    $sql = "SELECT preco, quantidade_vagas
            FROM viagens
            WHERE id_viagem = :id
            AND status = 'disponivel'
            FOR UPDATE";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $id_viagem]);

    $viagem = $stmt->fetch();

    if (!$viagem || $viagem["quantidade_vagas"] < $quantidade) {
        $pdo->rollBack();

        http_response_code(400);
        echo json_encode([
            "erro" => "Viagem indisponível ou vagas insuficientes."
        ]);
        exit;
    }

    $valor_total = $viagem["preco"] * $quantidade;

    $sql = "INSERT INTO reservas
            (id_usuario, id_viagem, quantidade_passageiros, valor_total)
            VALUES (:usuario, :viagem, :quantidade, :valor)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":usuario" => $id_usuario,
        ":viagem" => $id_viagem,
        ":quantidade" => $quantidade,
        ":valor" => $valor_total
    ]);

    $id_reserva = $pdo->lastInsertId();

    $sql = "UPDATE viagens
            SET quantidade_vagas = quantidade_vagas - :quantidade
            WHERE id_viagem = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":quantidade" => $quantidade,
        ":id" => $id_viagem
    ]);

    $pdo->commit();

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Reserva realizada com sucesso!",
        "id_reserva" => $id_reserva,
        "valor_total" => $valor_total
    ]);

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "erro" => "Erro ao realizar reserva."
    ]);

}
?>
