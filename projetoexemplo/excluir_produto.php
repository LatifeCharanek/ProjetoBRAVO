<?php
    session_start();
    if (!isset($_SESSION['admin_logado'])) {
    header('Location: login.php');
    exit();}
    require_once('conexao.php');
  
    $mensagem = "";

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset(&_GET['id'])) {
        
?>