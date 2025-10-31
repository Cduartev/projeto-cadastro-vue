<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$host = 'localhost';
$db = 'usuarios';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, 
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['error' => 'Conexão falhou: ' . $e->getMessage()]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['action'])) {
    echo json_encode(['error' => 'Ação não especificada']);
    exit;
}

switch ($data['action']) {
    case 'cadastrar_usuario':
        $nome = trim($data['nome'] ?? '');
        $email = trim($data['email'] ?? '');
        $senha = $data['senha'] ?? '';

        if ($nome === '' || $email === '' || $senha === '') {
            echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
            break;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Email já cadastrado.']);
                break;
            }

            $hashedSenha = password_hash($senha, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $hashedSenha]);

            echo json_encode(['success' => true, 'message' => 'Usuário cadastrado com sucesso!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $e->getMessage()]);
        }
        break;

    case 'login_usuario':
        $email = trim($data['email'] ?? '');
        $senha = $data['senha'] ?? '';

        if ($email === '' || $senha === '') {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos']);
            break;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $secret_key = "testandoachavekey@teste"; 
                $issuedAt = time();
                $expire = $issuedAt + 3600;

                $payload = [
                    "iat" => $issuedAt,
                    "exp" => $expire,
                    "data" => [
                        "id" => $usuario['id'],
                        "nome" => $usuario['nome'],
                        "email" => $usuario['email']
                    ]
                ];

                $jwt = JWT::encode($payload, $secret_key, 'HS256');

                echo json_encode([
                    'success' => true,
                    'token' => $jwt,
                    'nome' => $usuario['nome']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao logar: ' . $e->getMessage()]);
        }
        break;
}
?>