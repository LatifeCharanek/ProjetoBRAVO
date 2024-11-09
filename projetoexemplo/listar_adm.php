<?php
session_start();
require_once('conexao.php');

if(!isset($_SESSION['admin_logado'])){
    header('location:login.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT ADM_ID, ADM_NOME, ADM_EMAIL, ADM_ATIVO FROM ADMINISTRADOR");
    $stmt->execute();
    $administradores = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "<p style='color:red;'>Erro ao listar administradores: " . $e->getMessage() . "</p>";
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Administradores</title>
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

        .action-btn {
            display: inline-block;
            background-color: #E02D3C;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }

        .action-btn:hover {
            background-color: #c0242f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #E1E6EF;
        }

        th {
            background-color: #303030;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            color: #fff;
            font-size: 0.9em;
        }

        .edit-btn {
            background-color: #4CAF50;
        }

        .delete-btn {
            background-color: #E02D3C;
        }

        .edit-btn:hover, .delete-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Administradores Cadastrados</h2>
        <a href="criar_adm.php" class="action-btn">Criar Novo Administrador</a>
        <a href="painel_admin.php" class="action-btn">Voltar para o painel</a>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($administradores as $adm): ?>
                <tr>
                    <td><?php echo $adm['ADM_ID']; ?></td>
                    <td><?php echo $adm['ADM_NOME']; ?></td>
                    <td><?php echo $adm['ADM_EMAIL']; ?></td>
                    <td><?php echo ($adm['ADM_ATIVO'] == 1 ? 'Sim' : 'Não'); ?></td>
                    <td>
                        <a href="editar_adm.php?id=<?php echo $adm['ADM_ID'] ?>" class="edit-btn">Editar</a>
                        <a href="excluir_adm.php?id=<?php echo $adm['ADM_ID'] ?>" class="delete-btn" onclick="return confirm('Tem certeza que deseja excluir este administrador?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            
        </table>
    </div>
</body>
</html>