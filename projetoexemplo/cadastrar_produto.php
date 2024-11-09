<?php
session_start();
require_once("conexao.php");

if (!isset($_SESSION['admin_logado'])) {
    header('location: login.php');
    exit();
}

$erro = ''; // Inicialização da variável de erro
$sucesso = ''; // Inicialização da variável de sucesso

try {
    $stmt_categoria = $pdo->prepare("SELECT * FROM CATEGORIA");
    $stmt_categoria->execute();
    $categorias = $stmt_categoria->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "<p style='color: red;'>Erro ao buscar categorias. " . $e->getMessage() . "</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $desconto = $_POST['desconto'];
    $estoque = $_POST['estoque'];
    $categoria_id = $_POST['categoria_id'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $imagem_urls = $_POST['imagem_url'];
    $imagem_ordens = $_POST['imagem_ordem'];

    // Bloco para inserir no banco de dados os valores capturados
    try {
        $sql = 'INSERT INTO PRODUTO(PRODUTO_NOME, PRODUTO_DESC, PRODUTO_PRECO, CATEGORIA_ID, PRODUTO_ATIVO, PRODUTO_DESCONTO)
                VALUES (:nome, :descricao, :preco, :categoria_id, :ativo, :desconto)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->bindParam(':desconto', $desconto, PDO::PARAM_STR);
        $stmt->execute();

        $produto_id = $pdo->lastInsertId();

        $sql_estoque = 'INSERT INTO PRODUTO_ESTOQUE(PRODUTO_ID, PRODUTO_QTD) VALUES (:produto_id, :estoque)';
        $stmt_estoque = $pdo->prepare($sql_estoque);
        $stmt_estoque->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        $stmt_estoque->bindParam(':estoque', $estoque, PDO::PARAM_INT);
        $stmt_estoque->execute();

        foreach ($imagem_urls as $index => $url) {
            $ordem = $imagem_ordens[$index];
            $sql_imagem = 'INSERT INTO PRODUTO_IMAGEM(IMAGEM_URL, PRODUTO_ID, IMAGEM_ORDEM)
                           VALUES (:url, :produto_id, :ordem)';
            $stmt_imagem = $pdo->prepare($sql_imagem);
            $stmt_imagem->bindParam(':url', $url, PDO::PARAM_STR);
            $stmt_imagem->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
            $stmt_imagem->bindParam(':ordem', $ordem, PDO::PARAM_INT);
            $stmt_imagem->execute();
        }

        $sucesso = "<p class='success'>Produto cadastrado com sucesso.</p>";
    } catch (PDOException $e) {
        $erro = "<p class='error'>Erro ao cadastrar produto: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
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
            max-width : 600px;
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
    <script>
        function adicionarImagem() {
            const containerImagens = document.getElementById('containerImagens');
            const novoDiv = document.createElement('div');
            novoDiv.className = 'imagem-input';

            const novoInputURL = document.createElement('input');
            novoInputURL.type = 'text';
            novoInputURL.name = 'imagem_url[]';
            novoInputURL.placeholder = 'URL da imagem';
            novoInputURL.required = true;

            const novoInputOrdem = document.createElement('input');
            novoInputOrdem.type = 'number';
            novoInputOrdem.name = 'imagem_ordem[]';
            novoInputOrdem.placeholder = 'Ordem';
            novoInputOrdem.min = '1';
            novoInputOrdem.required = true;

            novoDiv.appendChild(novoInputURL);
            novoDiv.appendChild(novoInputOrdem);

            containerImagens.appendChild(novoDiv);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Cadastrar Produto</h2>
        <?php if ($erro): ?>
            <?= $erro ?>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <?= $sucesso ?>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" required></textarea>
            <label for="preco">Preço:</label>
            <input type="number" name="preco" id="preco" step="0.01" required>
            <label for="desconto">Desconto:</label>
            <input type="number" name="desconto" id="desconto" step="0.01" required>
            <label for="estoque">Estoque:</label>
            <input type="number" name="estoque" id="estoque" min="1" required>
            <label for="categoria_id">Categoria:</label>
            <select name="categoria_id" id="categoria_id" required>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['CATEGORIA_ID']; ?>">
                        <?php echo $categoria['CATEGORIA_NOME']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="ativo">Ativo:</label>
            <input type="checkbox" name="ativo" id="ativo" value="1" checked>
            <div id="containerImagens">
                <div class="imagem-input">
                    <input type="text" name="imagem_url[]" placeholder="URL da imagem" required>
                    <input type="number" name="imagem_ordem[]" placeholder="Ordem" min="1" required >
                </div>
            </div>
            <button type="button" onclick="adicionarImagem()">Adicionar mais imagens</button>
            <input type="submit" value="Cadastrar Produto">
        </form>
        <a href="painel_admin.php" class="back-link">Painel administrador</a>
    </div>
</body>
</html>