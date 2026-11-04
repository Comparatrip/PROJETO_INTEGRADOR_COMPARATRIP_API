
<?php

require_once "conexao.php";

try {

    $sql = "SELECT
                v.id_viagem,
                v.nome,
                v.origem,
                v.data_ida,
                v.data_volta,
                v.estadia_dias,
                v.hotel,
                v.descricao,
                v.preco,
                v.quantidade_vagas,
                v.inclui_bagagem,
                v.inclui_cafe,
                v.status,
                d.cidade,
                d.pais,
                c.nome AS companhia
            FROM viagens v
            INNER JOIN destinos d
                ON v.id_destino = d.id_destino
            LEFT JOIN companhias_aereas c
                ON v.id_companhia = c.id_companhia";

    $cidade = $_GET['cidade'] ?? null;

    if ($cidade) {
        $sql .= " WHERE d.cidade LIKE :cidade";
    }

    $sql .= " ORDER BY v.preco ASC";

    $stmt = $pdo->prepare($sql);

    if ($cidade) {
        $stmt->bindValue(":cidade", "%$cidade%");
    }

    $stmt->execute();

    $viagens = $stmt->fetchAll();

    echo json_encode($viagens, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Erro ao buscar viagens."
    ]);

}
?>
