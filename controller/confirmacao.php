<?php
require 'vendor/autoload.php';

$host = 'localhost';
$db   = 'usuarios';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$token = $_GET['token'] ?? null;
$status = "erro";
$titulo = "Ops! Algo deu errado";
$mensagem = "Token não fornecido ou inválido.";
$nomeUsuario = "";

if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE token_confirmacao = ?");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $stmt = $pdo->prepare("UPDATE usuarios SET confirmado = 1, token_confirmacao = NULL WHERE id = ?");
        $stmt->execute([$usuario['id']]);
        $status = "sucesso";
        $nomeUsuario = $usuario['nome'] ?? 'Usuário';
        $titulo = "Parabéns, $nomeUsuario!";
        $mensagem = "Sua conta foi confirmada com sucesso! Agora você já pode acessar todos os recursos da plataforma.";
    } else {
        $titulo = "Token Inválido";
        $mensagem = "O link de confirmação é inválido ou já foi utilizado. Se você já confirmou seu cadastro, pode fazer login normalmente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $status === 'sucesso' ? 'Cadastro Confirmado' : 'Erro na Confirmação'; ?> - Sistema Codify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Efeito de partículas no fundo */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background-image: 
                radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px),
                radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .container-custom {
            position: relative;
            z-index: 1;
            max-width: 550px;
            width: 100%;
        }

        .card-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .icon-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            animation: scaleIn 0.5s ease-out 0.2s both, pulse 2s ease-in-out 0.8s infinite;
        }

        .icon-error {
            background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
            color: white;
            animation: scaleIn 0.5s ease-out 0.2s both, shake 0.5s ease-in-out 0.8s;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .brand-logo {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }

        .title {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            animation: fadeIn 0.6s ease-out 0.3s both;
        }

        .message {
            color: #4a5568;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 35px;
            animation: fadeIn 0.6s ease-out 0.4s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .btn-custom {
            display: inline-block;
            padding: 14px 40px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            animation: fadeIn 0.6s ease-out 0.5s both;
            border: none;
            cursor: pointer;
        }

        .btn-success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }

        .btn-error {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-error:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }

        .divider {
            margin: 30px 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            animation: fadeIn 0.6s ease-out 0.6s both;
        }

        .info-box {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: left;
            margin-top: 25px;
            animation: fadeIn 0.6s ease-out 0.7s both;
        }

        .info-box p {
            margin: 0;
            color: #4a5568;
            font-size: 14px;
            line-height: 1.5;
        }

        .info-box strong {
            color: #2d3748;
        }

        /* Responsividade */
        @media (max-width: 576px) {
            .card-custom {
                padding: 40px 25px;
            }

            .title {
                font-size: 24px;
            }

            .icon-container {
                width: 100px;
                height: 100px;
                font-size: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="card-custom">
            <div class="brand-logo">
                Sistema De Cadastro
            </div>

            <div class="icon-container <?php echo $status === 'sucesso' ? 'icon-success' : 'icon-error'; ?>">
                <?php if ($status === 'sucesso'): ?>
                    <i class="fas fa-check"></i>
                <?php else: ?>
                    <i class="fas fa-times"></i>
                <?php endif; ?>
            </div>

            <h1 class="title"><?php echo $titulo; ?></h1>
            
            <p class="message"><?php echo $mensagem; ?></p>

            <div class="divider"></div>

            <?php if ($status === 'sucesso'): ?>
                <a href="/" class="btn-custom btn-success">
                    <i class="fas fa-sign-in-alt"></i> Fazer Login
                </a>
                
                <div class="info-box">
                    <p>
                        <strong><i class="fas fa-lightbulb"></i> Próximos passos:</strong><br>
                        Agora você pode fazer login e começar a explorar todos os recursos do Sistema!
                    </p>
                </div>
            <?php else: ?>
                <a href="/cadastro" class="btn-custom btn-error">
                    <i class="fas fa-redo-alt"></i> Voltar ao Cadastro
                </a>
                
                <div class="info-box">
                    <p>
                        <strong><i class="fas fa-info-circle"></i> Precisa de ajuda?</strong><br>
                        Entre em contato com nosso suporte ou tente fazer um novo cadastro.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>