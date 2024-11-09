<?php
session_start();
require_once("conexao.php");

if (!isset($_SESSION['admin_logado'])) {
    header('location: login.php');
    exit();
}

$erro = '';
$sucesso = '';

// Adicionar nova categoria
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'adicionar') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $imagem = $_POST['imagem'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    try {
        $sql = 'INSERT INTO CATEGORIA (CATEGORIA_NOME, CATEGORIA_DESC, CATEGORIA_IMAGEM, CATEGORIA_ATIVO)
                VALUES (:nome, :descricao, :imagem, :ativo)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':imagem', $imagem, PDO::PARAM_STR);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->execute();

        $sucesso = "Categoria adicionada com sucesso!";
    } catch (PDOException $e) {
        $erro = "Erro ao adicionar categoria: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Categoria</title>
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

        .listar-categorias {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastrar Categoria</h2>
        <?php if ($erro): ?>
            <p class="error"><?= $erro ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p class="success"><?= $sucesso ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" required></textarea>
            <label for="imagem">Imagem:</label>
            <input type="text" name="imagem" id="imagem" required>
            <label for="ativo">Ativo:</label>
            <input type="checkbox" name="ativo" id="ativo" value="1" checked>
            <input type="hidden" name="acao" value="adicionar">
            <input type="submit" value="Adicionar Categoria">
        </form>
        <div class="listar-categorias">
            <a href="listar_categoria.php">Listar Categorias</a>
        </div>
        <a href="painel_admin.php" class="back-link">Painel administrador</a>
    </div>
</body>
</html>