<?php
session_start();
require_once("conexao.php");

if (!isset($_SESSION['admin_logado'])) {
    header('location: login.php');
    exit();
}

$categoria_id = $_GET['id'];

// Função para buscar categoria
function buscarCategoria($pdo, $categoria_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM CATEGORIA WHERE CATEGORIA_ID = ?");
        $stmt->execute([$categoria_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Carregar categoria
$categoria = buscarCategoria($pdo, $categoria_id);

if (empty($categoria)) {
    header('location: listar_categoria.php');
    exit();
}

// Atualizar categoria
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoria_nome = $_POST['categoria_nome'];
    $categoria_desc = $_POST['categoria_desc'];
    $categoria_imagem = $_POST['categoria_imagem'];
    // Verifica se o checkbox está marcado e define como 1 ou 0
    //$categoria_ativo = isset($_POST['categoria_ativo']) ? 1 : 0;
    //ADCIONAR O BOTAO DE ATIVO E NAO ATIVO

    try {
        $stmt = $pdo->prepare("UPDATE CATEGORIA SET CATEGORIA_NOME = ?, CATEGORIA_DESC = ?, CATEGORIA_IMAGEM = ? WHERE CATEGORIA_ID = ?");
        $stmt->execute([$categoria_nome, $categoria_desc, $categoria_imagem, $categoria_id]);
        header('location: listar_categoria.php');
        exit();
    } catch (PDOException $e) {
        echo "Erro ao atualizar categoria: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria</title>
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
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #E02D3C;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        button[type="submit"] {
            background-color: #E02D3C;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #c0242f;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ccc;
            color: #303030;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #aaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Categoria</h2>
        <form action="" method="post">
            <label for="categoria_nome">Nome da Categoria:</label>
            <input type="text" id="categoria_nome" name="categoria_nome" value="<?php echo htmlspecialchars($categoria['CATEGORIA_NOME'], ENT_QUOTES, 'UTF-8'); ?>">

            <label for="categoria_desc">Descrição da Categoria:</label>
            <textarea id="categoria_desc" name="categoria_desc"><?php echo htmlspecialchars($categoria['CATEGORIA_DESC'], ENT_QUOTES, 'UTF-8'); ?></textarea>

            <label for="categoria_imagem">Imagem da Categoria:</label>
            <input type="text" id="categoria_imagem" name="categoria_imagem" value="<?php echo htmlspecialchars($categoria['CATEGORIA_IMAGEM'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit">Atualizar Categoria</button>
        </form>

        <!-- Botão de Voltar -->
        <a href="listar_categoria.php" class="back-btn">Voltar</a>
    </div>
</body>
</html>
