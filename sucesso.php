<?php
session_start();
if (!isset($_GET['pedido'])) {
    header("Location: inicial.php");
    exit;
}

$pedido_id = $_GET['pedido'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pedido Confirmado - Habib Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{background:#f5f5f5;font-family:Arial;text-align:center;padding:50px;}
        .box{max-width:500px;margin:auto;background:#fff;padding:40px;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.1);}
        h1{color:#8B4513;}
        .check{color:#8B4513;font-size:80px;}
        button{background:#8B4513;color:#fff;padding:15px 30px;border:none;border-radius:10px;font-size:18px;cursor:pointer;margin-top:20px;}
        button:hover{background:#723a0f;}
    </style>
</head>
<body>
<div class="box">
    <div class="check"><i class="fas fa-check-circle"></i></div>
    <h1>PEDIDO CONFIRMADO!</h1>
    <p><strong>Nº do Pedido:</strong> #<?= $pedido_id ?></p>
    <p>Em até 5 minutos enviaremos o <strong>PIX</strong> ou <strong>link de pagamento</strong> por e-mail.</p>
    <p>Obrigado por comprar na <strong>Habib Coffee</strong>!</p>
    <a href="inicial.php"><button>VOLTAR À LOJA</button></a>
</div>
</body>
</html>