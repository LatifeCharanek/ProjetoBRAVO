<?php
session_start();

require_once('conexao.php');
if (!isset($_SESSION['admin_logado'])) {
    header('location:login.php');
    exit();
}

// Consulta para listar produtos
try {
    $stmt = $pdo->prepare("SELECT PRODUTO.*, CATEGORIA.CATEGORIA_NOME, PRODUTO_IMAGEM.IMAGEM_URL, PRODUTO_ESTOQUE.PRODUTO_QTD
    FROM PRODUTO
    JOIN CATEGORIA ON PRODUTO.CATEGORIA_ID = CATEGORIA.CATEGORIA_ID
    LEFT JOIN PRODUTO_IMAGEM ON PRODUTO.PRODUTO_ID = PRODUTO_IMAGEM.PRODUTO_ID
    LEFT JOIN PRODUTO_ESTOQUE ON PRODUTO.PRODUTO_ID = PRODUTO_ESTOQUE.PRODUTO_ID");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao listar produtos: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administrador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E1E6EF;
            margin: 0;
            display: flex;
        }
        .sidebar {
            background-color: #E02D3C;
            width: 200px;
            height: 100vh;
            padding: 20px;
            color: white;
        }
        .sidebar h2 {
            color: white;
            margin: 0 0 20px 0;
        }
        .sidebar a {
            text-decoration: none;
            color: white;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #C02430;
        }
        .main {
            flex-grow: 1;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #E02D3C;
            margin-bottom: 10px;
        }
        p {
            color: #333;
            line-height: 1.5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 30px 30px; /* Removido o padding superior */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
            font-size: 1em;
            table-layout: fixed;
        }
        th, td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
    <div class="sidebar">
        <h2>Painel de Controle</h2>
        <a href="listar_adm.php">CRUD ADM</a>
        <a href="cadastrar_produto.php">CRUD PRODUTO</a>
        <a href="listar_categoria.php">CRUD CATEGORIA</a>

    </div>
    <div class="main">
        <h2>Bem-vindo, administrador!</h2>
        <p>Estamos felizes em tê-lo aqui. Este painel é dedicado à gestão de produtos e categorias na nossa plataforma de revenda de carros. Utilize as opções ao lado para gerenciar os dados e garantir que nossa oferta esteja sempre atualizada e atrativa para os clientes.</p>
        

        
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
                    <th>Editar</th>
                    <th>Excluir</th>
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
                        </td>
                        <td>
                        <a href="excluir_produto.php?id=<?php echo $produto['PRODUTO_ID'] ?>" class="action-btn delete-btn">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

</body>
</html>