<?php
session_start();
require_once("conexao.php");

if (!isset($_SESSION['admin_logado'])) {
    header('location: login.php');
    exit();
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE FROM CATEGORIA WHERE CATEGORIA_ATIVO = 0");
        $stmt->execute([$id]);
        
        $_SESSION['mensagem'] = "Categoria excluída com sucesso!";
    } catch(PDOException $e) {
        $_SESSION['mensagem'] = "Erro ao excluir categoria: " . $e->getMessage();
    }
} else {
    $_SESSION['mensagem'] = "ID da categoria não fornecido.";
}

header('Location: listar_categoria.php');
exit();
?>