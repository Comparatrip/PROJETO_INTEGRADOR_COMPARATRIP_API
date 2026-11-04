
<?php

require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido."]);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

$nome = trim($dados["nome"] ?? "");
$email = trim($dados["email"] ?? "");
$telefone = $dados["telefone"] ?? null;
$cpf = $dados["cpf"] ?? null;
$senha = $dados["senha"] ?? "";
$data_nascimento = $dados["data_nascimento"] ?? null;

if ($nome === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 8) {
    http_response_code(400);
    echo json_encode([
        "erro" => "Informe nome, e-mail válido e senha com pelo menos 8 caracteres."
    ]);
    exit;
}

try {

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios
        (nome, email, telefone, cpf, senha, data_nascimento)
        VALUES
        (:nome, :email, :telefone, :cpf, :senha, :data_nascimento)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":nome" => $nome,
        ":email" => $email,
        ":telefone" => $telefone,
        ":cpf" => $cpf ?: null,
        ":senha" => $senhaHash,
        ":data_nascimento" => $data_nascimento ?: null
    ]);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Usuário cadastrado com sucesso!",
        "id_usuario" => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {

    if ($e->getCode() == "23000") {
        http_response_code(409);
        echo json_encode([
            "erro" => "E-mail ou CPF já cadastrado."
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "erro" => "Erro ao cadastrar usuário."
        ]);
    }

}
?>
