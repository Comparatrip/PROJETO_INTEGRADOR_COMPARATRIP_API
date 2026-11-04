
<?php

require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido."]);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

$email = trim($dados["email"] ?? "");
$senha = $dados["senha"] ?? "";

try {

    $sql = "SELECT id_usuario, nome, email, senha, status
            FROM usuarios
            WHERE email = :email";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":email" => $email
    ]);

    $usuario = $stmt->fetch();

    if (
        !$usuario ||
        !password_verify($senha, $usuario["senha"]) ||
        $usuario["status"] !== "ativo"
    ) {

        http_response_code(401);

        echo json_encode([
            "erro" => "E-mail ou senha incorretos."
        ]);

        exit;
    }

    unset($usuario["senha"]);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Login realizado com sucesso!",
        "usuario" => $usuario
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Erro ao realizar login."
    ]);

}
?>
