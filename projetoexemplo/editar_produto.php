<?php
session_start();
require_once("conexao.php");

if (!isset($_SESSION['admin_logado'])) {
    header('location: login.php');
    exit();
}

// Variável para armazenar o ID do produto se estiver sendo editado
$produto_id = isset($_GET['id']) ? $_GET['id'] : null;

try {
    $stmt_categoria = $pdo->prepare("SELECT * FROM CATEGORIA");
    $stmt_categoria->execute();
    $categorias = $stmt_categoria->fetchAll(PDO::FETCH_ASSOC);
    
    // Se o ID do produto for fornecido, busque os dados do produto
    if ($produto_id) {
        $stmt_produto = $pdo->prepare("SELECT * FROM PRODUTO WHERE PRODUTO_ID = :id");
        $stmt_produto->bindParam(':id', $produto_id, PDO::PARAM_INT);
        $stmt_produto->execute();
        $produto = $stmt_produto->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $msg = "<p style='color: red;'>Erro ao buscar categorias. " . $e->getMessage() . "</p>";
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

    try {
        // Se o ID do produto estiver presente, é uma atualização
        if ($produto_id) {
            $sql = 'UPDATE PRODUTO SET PRODUTO_NOME = :nome, PRODUTO_DESC = :descricao, 
                    PRODUTO_PRECO = :preco, CATEGORIA_ID = :categoria_id, 
                    PRODUTO_ATIVO = :ativo, PRODUTO_DESCONTO = :desconto 
                    WHERE PRODUTO_ID = :produto_id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        } else { // Caso contrário, é uma inserção
            $sql = 'INSERT INTO PRODUTO(PRODUTO_NOME, PRODUTO_DESC, PRODUTO_PRECO, CATEGORIA_ID, PRODUTO_ATIVO, PRODUTO_DESCONTO)
                    VALUES (:nome, :descricao, :preco, :categoria_id, :ativo, :desconto)';
            $stmt = $pdo->prepare($sql);
        }
        
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->bindParam(':desconto', $desconto, PDO::PARAM_STR);
        $stmt->execute();

        // Se for uma nova inserção, pegue o ID do produto recém criado
        if (!$produto_id) {
            $produto_id = $pdo->lastInsertId();
        }

        // Atualiza ou insere no estoque
        $sql_estoque = 'INSERT INTO PRODUTO_ESTOQUE(PRODUTO_ID, PRODUTO_QTD) VALUES (:produto_id, :estoque)
                        ON DUPLICATE KEY UPDATE PRODUTO_QTD = :estoque'; // Se já existe, atualiza
        $stmt_estoque = $pdo->prepare($sql_estoque);
        $stmt_estoque->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        $stmt_estoque->bindParam(':estoque', $estoque, PDO::PARAM_INT);
        $stmt_estoque->execute();

        // Imagens
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

        echo "<p style='color:green;'>Produto cadastrado/atualizado com sucesso.</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro ao cadastrar/atualizar produto: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
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
    <h2><?php echo $produto_id ? 'Editar Produto' : 'Cadastrar Produto'; ?></h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" value="<?php echo $produto['PRODUTO_NOME'] ?? ''; ?>" required>
        <p></p>
        <label for="descricao">Descrição:</label>
        <textarea name="descricao" id="descricao" required><?php echo $produto['PRODUTO_DESC'] ?? ''; ?></textarea>
        <p></p>
        <label for="preco">Preço:</label>
        <input type="number" name="preco" id="preco" step="0.01" value="<?php echo $produto['PRODUTO_PRECO'] ?? ''; ?>" required>
        <p></p>
        <label for="desconto">Desconto:</label>
        <input type="number" name="desconto" id="desconto" step="0.01" value="<?php echo $produto['PRODUTO_DESCONTO'] ?? ''; ?>" required>
        <p></p>
        <label for="estoque">Estoque:</label>
        <input type="number" name="estoque" id="estoque" min="1" value="<?php echo $produto['PRODUTO_ID'] ?? ''; ?>" required>
        <p></p>
        <label for="categoria_id">Categoria:</label>
        <select name="categoria_id" id="categoria_id" required>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria['CATEGORIA_ID']; ?>" <?php echo (isset($produto) && $produto['CATEGORIA_ID'] == $categoria['CATEGORIA_ID']) ? 'selected' : ''; ?>>
                    <?php echo $categoria['CATEGORIA_NOME']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p></p>
        <label for="ativo">Ativo:</label>
        <input type="checkbox" name="ativo" id="ativo" value="1" <?php echo (isset($produto) && $produto['PRODUTO_ATIVO'] == 1) ? 'checked' : ''; ?>>
        <p></p>
        <div id="containerImagens">
            <?php if (isset($produto)): ?>
                <!-- Aqui você pode adicionar um loop para mostrar as imagens existentes, se necessário -->
            <?php else: ?>
                <div class="imagem-input">
                    <input type="text" name="imagem_url" placeholder="URL da imagem" required>
                    <input type="number" name="imagem_ordem[]" placeholder="Ordem" min="1" required>
                </div>
            <?php endif; ?>
        </div>
        <p></p>
        <button type="button" onclick="adicionarImagem()">Adicionar mais imagens</button>
        <p></p>
        <button type="submit"><?php echo $produto_id ? 'Atualizar Produto' : 'Cadastrar Produto'; ?></button>
    </form>
    <a href="painel_admin.php">Painel administrador</a>
</body>
</html>
