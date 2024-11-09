<?php
session_start();

if(isset($_SESSION['mensagem_erro'])){
    echo "<p class='error-message'>" .$_SESSION['mensagem_erro']."</p>";
    unset($_SESSION['mensagem_erro']);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="processa_login.php" method="post">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
        <p></p>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        <p></p>

        <input type="submit" value="Entrar">

        
    </form>
</body>







