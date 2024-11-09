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
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE ADMINISTRADOR SET ADM_NOME = ?, ADM_EMAIL = ?, ADM_SENHA = ?, ADM_ATIVO = ? WHERE ADM_ID = ?");
        $stmt->execute([$nome, $email, $senha, $ativo, $id]);
        $sucesso = "Administrador atualizado com sucesso!";
    } catch(PDOException $e) {
        $erro = "Erro ao atualizar administrador: " . $e->getMessage();
    }
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM ADMINISTRADOR WHERE ADM_ID = ?");
$stmt->execute([$id]);
$adm = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Administrador</title>
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
        <h2>Editar Administrador</h2>
        <?php if ($erro): ?>
            <p class="error"><?= $erro ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p class="success"><?= $sucesso ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="id" value="<?= $adm['ADM_ID'] ?>">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= $adm['ADM_NOME'] ?>" required>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?= $adm['ADM_EMAIL'] ?>" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <label for="ativo">Ativo:</label>
            <input type="checkbox" id="ativo" name="ativo" <?= ($adm['ADM_ATIVO'] == 1) ? 'checked' : '' ?>>
            <input type="submit" value="Editar Administrador">
        </form>
        <a href="painel_admin.php" class="back-link">Voltar ao painel de administrador</a>
    </div>
</body>
</html>