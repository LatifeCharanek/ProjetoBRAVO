<?php
session_start();
require_once('conexao.php');

if(!isset($_SESSION['admin_logado'])){
    header('location:login.php');
    exit();
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("INSERT INTO ADMINISTRADOR (ADM_NOME, ADM_EMAIL, ADM_SENHA, ADM_ATIVO) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha, $ativo]);
        $sucesso = "Administrador criado com sucesso!";
    } catch(PDOException $e) {
        $erro = "Erro ao criar administrador: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Administrador</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #E1E6EF;
            color: #303030;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #E02D3C;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        input[type="checkbox"] {
            margin-top: 10px;
        }

        input[type="submit"] {
            background-color: #E02D3C;
            color: #fff;
            border: none;
            padding: 12px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #c0242f;
        }

        .error {
            color: #E02D3C;
            margin-top: 10px;
        }

        .success {
            color: #4CAF50;
            margin-top: 10px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #303030;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Criar Novo Administrador</h2> <?php if ($erro): ?>
            <p class="error"><?= $erro ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p class="success"><?= $sucesso ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <label for="ativo">Ativo:</label>
            <input type="checkbox" id="ativo" name="ativo">
            <input type="submit" value="Criar Administrador">
        </form>
        <a href="listar_adm.php" class="back-link">Voltar ao painel de administrador</a>
    </div>
</body>
</html>