<?php
session_start();
require_once('conexao.php');

if(!isset($_SESSION['admin_logado'])){
    header('Location: login.php');
    exit();
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM ADMINISTRADOR WHERE ADM_ID = ?");
        $stmt->execute([$id]);
        
        $_SESSION['mensagem'] = "Administrador excluído com sucesso!";
    } catch(PDOException $e) {
        $_SESSION['mensagem'] = "Erro ao excluir administrador: " . $e->getMessage();
    }
} else {
    $_SESSION['mensagem'] = "ID do administrador não fornecido.";
}

header('Location: listar_adm.php');
exit();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Administrador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E1E6EF;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #E02D3C;
            margin-bottom: 30px;
        }
        .mensagem {
            background-color: #E02D3C;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .button {
            background-color: #E02D3C;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #C02430;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Excluir Administrador</h2>
        <?php
        if(isset($_SESSION['mensagem'])) {
            echo "<p class='mensagem'>" . $_SESSION['mensagem'] . "</p>";
            unset($_SESSION['mensagem']);
        }
        ?>
        <a href="painel_admin.php" class="button">Voltar ao Painel</a>
    </div>
</body>
</html>