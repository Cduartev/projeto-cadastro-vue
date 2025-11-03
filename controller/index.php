<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ===== CONFIGURAÇÃO DO BANCO =====
$host = 'localhost';
$db = 'usuarios';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Conexão falhou: ' . $e->getMessage()]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Ação não especificada ou dados inválidos']);
    exit;
}

$secret_key = "testandoachavekey@teste"; // chave JWT

// ===== FUNÇÃO PARA ENVIAR E-MAIL DE CONFIRMAÇÃO =====
function enviarEmailConfirmacao($emailDestino, $nome, $token) {
    $mail = new PHPMailer(true);
    try {
        // Configuração SMTP (exemplo Gmail)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'barberprooficiall@gmail.com'; // altere para o seu e-mail
        $mail->Password = 'zdnt wxvz ttkz qgaz'; // use App Password se for Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('barberprooficiall@gmail.com', 'Equipe do Sistema');
        $mail->addAddress($emailDestino, $nome);
        $link = "http://localhost/controller/confirmar.php?token=" . urlencode($token);

        $mail->isHTML(true);
        $mail->Subject = "Confirme seu cadastro, $nome!";
        $mail->Body = "
              <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f4f4;padding:40px 0;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' style='background-color:#ffffff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);overflow:hidden;'>
                            
                            <!-- Header com gradiente -->
                            <tr>
                                <td style='background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);padding:40px 30px;text-align:center;'>
                                    <h1 style='margin:0;color:#ffffff;font-size:32px;font-weight:bold;'>
                                        Sistema De Gerenciamento
                                    </h1>
                                    <p style='margin:10px 0 0 0;color:#ffffff;font-size:14px;opacity:0.9;'>
                                        Sistema de Gerenciamento
                                    </p>
                                </td>
                            </tr>
                            
                            <!-- Conteúdo principal -->
                            <tr>
                                <td style='padding:50px 40px;'>
                                    <h2 style='margin:0 0 20px 0;color:#333333;font-size:24px;font-weight:600;'>
                                        Olá, $nome! 👋
                                    </h2>
                                    
                                    <p style='margin:0 0 20px 0;color:#666666;font-size:16px;line-height:1.6;'>
                                        Bem-vindo ao <strong>Codifyup Sistemas</strong>! Estamos muito felizes em ter você conosco.
                                    </p>
                                    
                                    <p style='margin:0 0 30px 0;color:#666666;font-size:16px;line-height:1.6;'>
                                        Para ativar sua conta e começar a usar todos os recursos da plataforma, 
                                        por favor confirme seu endereço de e-mail clicando no botão abaixo:
                                    </p>
                                    
                                    <!-- Botão de confirmação -->
                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td align='center' style='padding:20px 0;'>
                                                <a href='$link' style='display:inline-block;background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 4px 12px rgba(102,126,234,0.4);transition:all 0.3s;'>
                                                    Confirmar Meu Cadastro
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <!-- Link alternativo -->
                                    <table width='100%' cellpadding='0' cellspacing='0' style='margin-top:30px;'>
                                        <tr>
                                            <td style='background-color:#f8f9fa;padding:20px;border-radius:8px;border-left:4px solid #667eea;'>
                                                <p style='margin:0 0 10px 0;color:#333333;font-size:14px;font-weight:600;'>
                                                    Problemas com o botão?
                                                </p>
                                                <p style='margin:0;color:#666666;font-size:13px;line-height:1.5;'>
                                                    Copie e cole este link no seu navegador:<br>
                                                    <a href='$link' style='color:#667eea;word-break:break-all;text-decoration:none;'>$link</a>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <!-- Informação de segurança -->
                                    <table width='100%' cellpadding='0' cellspacing='0' style='margin-top:30px;'>
                                        <tr>
                                            <td style='background-color:#fff3cd;padding:15px;border-radius:8px;border-left:4px solid #ffc107;'>
                                                <p style='margin:0;color:#856404;font-size:13px;line-height:1.5;'>
                                                    <strong>⏰ Link válido por 24 horas</strong><br>
                                                    Por questões de segurança, este link expira em 24 horas.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                </td>
                            </tr>
                            
                            <!-- Rodapé -->
                            <tr>
                                <td style='background-color:#f8f9fa;padding:30px 40px;border-top:1px solid #e9ecef;'>
                                    <p style='margin:0 0 10px 0;color:#999999;font-size:13px;text-align:center;line-height:1.6;'>
                                        Você recebeu este e-mail porque se cadastrou no Sistema De Cadastro.<br>
                                        Se não foi você, por favor ignore este e-mail.
                                    </p>
                                    
                                    <table width='100%' cellpadding='0' cellspacing='0' style='margin-top:20px;'>
                                        <tr>
                                            <td align='center'>
                                                <p style='margin:0;color:#cccccc;font-size:12px;'>
                                                    © 2025 Codifyup. Todos os direitos reservados.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// ===== ROTAS =====
switch ($data['action']) {

    // --- CADASTRAR USUÁRIO COM CONFIRMAÇÃO DE E-MAIL ---
    case 'cadastrar_usuario':
        $nome = trim($data['nome'] ?? '');
        $email = trim($data['email'] ?? '');
        $senha = $data['senha'] ?? '';

        if ($nome === '' || $email === '' || $senha === '') {
            echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Email já cadastrado.']);
                exit;
            }

            $hashedSenha = password_hash($senha, PASSWORD_BCRYPT);
            $token = bin2hex(random_bytes(32));

            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, confirmado, token_confirmacao) VALUES (?, ?, ?, 0, ?)");
            $stmt->execute([$nome, $email, $hashedSenha, $token]);

            if (enviarEmailConfirmacao($email, $nome, $token)) {
                echo json_encode(['success' => true, 'message' => 'Cadastro realizado! Verifique seu e-mail para confirmar.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao enviar e-mail de confirmação.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()]);
        }
        break;

    // --- LOGIN (só se confirmado) ---
    case 'login_usuario':
        $email = trim($data['email'] ?? '');
        $senha = $data['senha'] ?? '';

        if ($email === '' || $senha === '') {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
                exit;
            }

            if ($usuario['confirmado'] == 0) {
                echo json_encode(['success' => false, 'message' => 'Confirme seu e-mail antes de fazer login.']);
                exit;
            }

            if (password_verify($senha, $usuario['senha'])) {
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

                echo json_encode(['success' => true, 'token' => $jwt, 'nome' => $usuario['nome']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao logar: ' . $e->getMessage()]);
        }
        break;

    // --- ROTAS DE PESSOAS ---
    case 'listar':
        try {
            $stmt = $pdo->query("SELECT * FROM pessoas ORDER BY id DESC");
            echo json_encode($stmt->fetchAll());
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'cadastrar':
        $nome = trim($data['nome'] ?? '');
        $idade = intval($data['idade'] ?? 0);
        $profissao = trim($data['profissao'] ?? '');

        if ($nome === '' || $idade <= 0 || $profissao === '') {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos corretamente.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO pessoas (nome, idade, profissao) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $idade, $profissao]);
            echo json_encode(['success' => true, 'message' => 'Pessoa cadastrada com sucesso!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'editar':
        $id = intval($data['id'] ?? 0);
        $nome = trim($data['nome'] ?? '');
        $idade = intval($data['idade'] ?? 0);
        $profissao = trim($data['profissao'] ?? '');

        if ($id <= 0 || $nome === '' || $idade <= 0 || $profissao === '') {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE pessoas SET nome=?, idade=?, profissao=? WHERE id=?");
            $stmt->execute([$nome, $idade, $profissao, $id]);
            echo json_encode(['success' => true, 'message' => 'Pessoa atualizada com sucesso.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'excluir':
        $id = intval($data['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM pessoas WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Pessoa excluída com sucesso.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    // --- ROTAS DE FAMILIARES ---
    case 'listar_familiares':
        $id_pessoa = intval($data['id_pessoa'] ?? 0);
        if ($id_pessoa <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("SELECT * FROM familiares WHERE id_pessoa = ?");
            $stmt->execute([$id_pessoa]);
            echo json_encode(['success' => true, 'familiares' => $stmt->fetchAll()]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'cadastrar_familiar':
        $id_pessoa = intval($data['id_pessoa'] ?? 0);
        $nome = trim($data['nome'] ?? '');
        $idade = intval($data['idade'] ?? 0);
        $parentesco = trim($data['parentesco'] ?? '');

        if ($id_pessoa <= 0 || $nome === '' || $idade <= 0 || $parentesco === '') {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos corretamente.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO familiares (id_pessoa, nome, idade, parentesco) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_pessoa, $nome, $idade, $parentesco]);
            echo json_encode(['success' => true, 'message' => 'Familiar cadastrado com sucesso.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}
