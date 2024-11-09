<?php
session_start();

require_once('conexao.php');
if (!isset($_SESSION['admin_logado'])) {
    header('location:login.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT PRODUTO.*, CATEGORIA.CATEGORIA_NOME, PRODUTO_IMAGEM.IMAGEM_URL, PRODUTO_ESTOQUE.PRODUTO_QTD
    FROM PRODUTO
    JOIN CATEGORIA ON PRODUTO.CATEGORIA_ID = CATEGORIA.CATEGORIA_ID
    LEFT JOIN PRODUTO_IMAGEM ON PRODUTO.PRODUTO_ID = PRODUTO_IMAGEM.PRODUTO_ID
    LEFT JOIN PRODUTO_ESTOQUE ON PRODUTO.PRODUTO_ID = PRODUTO_ESTOQUE.PRODUTO_ID");
    $stmt->execute(); // execute a consulta 
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>erro ao listar produtos:" . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos</title>
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
            max-width: 1200px;
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

        table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 50px;
    font-size: 1em;
    table-layout: fixed; /* Garante que as colunas não estoure o limite */
}

th, td {
    overflow: hidden; /* Esconde o conteúdo que ultrapassa */
    text-overflow: ellipsis; /* Adiciona "..." para conteúdo que não cabe */
    white-space: nowrap; /* Impede que o texto quebre em várias linhas */
}
        th {
            background-color: #E02D3C;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 1rem;
            padding: 15px;
        }

        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }
        img {
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 50px;
            height: auto;
        }

        .action-btn {
            padding: 10px 15px;
            background-color: #E02D3C;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            display: inline-block;
            text-align: center;
            box-shadow: 0 4px 10px rgba(224, 45, 60, 0.3);
        }

        .action-btn:hover {
            background-color: #c0242f;
        }

        .delete-btn {
            background-color: #ff3366;
        }

        .delete-btn:hover {
            background-color: #ff1a50;
        }

        @media (max-width: 768px) {
            th, td {
                padding: 10px;
                font-size: 0.9rem;
            }

            h2 {
                font-size: 2rem;
            }

            .action-btn {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Produtos Cadastrados</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Categoria</th>
                <th>Ativo</th>
                <th>Desconto</th>
                <th>Estoque</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo $produto['PRODUTO_ID']; ?></td>
                    <td><?php echo $produto['PRODUTO_NOME']; ?></td>
                    <td><?php echo $produto['PRODUTO_DESC']; ?></td>
                    <td><?php echo $produto['PRODUTO_PRECO']; ?></td>
                    <td><?php echo $produto['CATEGORIA_NOME']; ?></td>
                    <td><?php echo ($produto['PRODUTO_ATIVO'] == 1 ? 'Sim' : 'Não'); ?></td>
                    <td><?php echo $produto['PRODUTO_DESCONTO']; ?></td>
                    <td><?php echo $produto['PRODUTO_QTD']; ?></td>
                    <td><img src="<?php echo $produto['IMAGEM_URL']; ?>" alt="<?php echo $produto['PRODUTO_NOME']; ?>"></td>
                    <td>
                        <a href="editar_produto.php?id=<?php echo $produto['PRODUTO_ID'] ?>" class="action-btn">Editar</a>
                        <a href="excluir_produto.php?id=<?php echo $produto['PRODUTO_ID'] ?>" class="action-btn delete-btn">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
