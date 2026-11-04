
<?php

require_once "conexao.php";

try {

    $sql = "SELECT
                id_destino,
                nome,
                pais,
                estado,
                cidade,
                descricao,
                destaque
            FROM destinos
            ORDER BY nome ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $destinos = $stmt->fetchAll();

    echo json_encode($destinos, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Erro ao buscar destinos."
    ]);

}
?>
