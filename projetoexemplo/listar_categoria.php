<?php
session_start();
require_once("conexao.php");

if (!isset($_SESSION['admin_logado'])) {
    header('location: login.php');
    exit();
}

$categorias = [];

// Função para buscar categorias
function buscarCategorias($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM CATEGORIA");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Carregar categorias
$categorias = buscarCategorias($pdo);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Categorias</title>
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

        .action-btn {
            display: inline-block;
            background-color: #E02D3C; /* Cinza claro */
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin: 10px 0;
            text-align: center;
        }

      
        .category-btn {
            display: block;
            background-color: #D3D3D3; /* Cinza claro */
            color: #303030;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            transition: background-color 0.3s ease;
        }

        .category-btn:hover {
            background-color: #B0B0B0; /* Cinza um pouco mais escuro */
        }

        /* Estilo do modal */
        .modal {
            display: none; /* Oculto por padrão */
            position: fixed; /* Fixo */
            z-index: 1; /* Fica na frente */
            left: 0;
            top: 0;
            width: 100%; /* Largura total */
            height: 100%; /* Altura total */
            overflow: auto; /* Habilita scroll se necessário */
            background-color: rgba(0,0,0,0.4); /* Fundo com opacidade */
            padding-top: 60px; /* Espaço no topo */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 15% do topo e centralizado */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Largura do modal */
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            color: #fff;
            font-size: 0.9em;
            margin-right: 5px; /* Espaço entre os botões */
        }

        .edit-btn {
            background-color: #4CAF50;
        }

        .delete-btn {
            background-color: #E02D3C;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Categorias Cadas tradas</h2>
        <a href="criar_categoria.php" class="action-btn">Criar Nova Categoria</a>
        <a href="painel_admin.php" class="action-btn">Voltar para o painel</a>

        <?php foreach ($categorias as $categoria): ?>
            <a href="#" class="category-btn" onclick="openModal(<?php echo htmlspecialchars(json_encode($categoria), ENT_QUOTES, 'UTF-8'); ?>)">
                <?php echo $categoria['CATEGORIA_NOME']; ?>
            </a>
        <?php endforeach; ?>

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="categoria-nome"></h2>
                <p id="categoria-descricao"></p>
                <p id="categoria-imagem"></p>
                <p id="categoria_ativo"></p>
                <a href="#" id="editar-btn" class="edit-btn">Editar</a>
                <a href="#" id="excluir-btn" class="delete-btn">Excluir</a>
            </div>
        </div>
    </div>

    <script>
        function openModal(categoria) {
            var modal = document.getElementById("myModal");
            var span = document.getElementsByClassName("close")[0];
            var categoriaNome = document.getElementById("categoria-nome");
            var categoriaDescricao = document.getElementById("categoria-descricao");
            var categoriaImagem = document.getElementById("categoria-imagem");
            var editarBtn = document.getElementById("editar-btn");
            var excluirBtn = document.getElementById("excluir-btn");

            categoriaNome.innerHTML = categoria.CATEGORIA_NOME;
            categoriaDescricao.innerHTML = "Descrição: " + categoria.CATEGORIA_DESC;
            categoriaImagem.innerHTML = "Imagem: " + categoria.CATEGORIA_IMAGEM;
            editarBtn.href = "editar_categoria.php?id=" + categoria.CATEGORIA_ID;
            excluirBtn.href = "excluir_categoria.php?id=" + categoria.CATEGORIA_ID;

            modal.style.display = "block";

            span.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    </script>
</body>
</html>