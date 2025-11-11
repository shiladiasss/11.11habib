<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");

// === DE ONDE VEIO? (volta pro lugar certo) ===
$volta_para = $_GET['volta'] ?? 'inicial.php';

if ($_POST) {
    $cpf_limpo = preg_replace('/\D/', '', $_POST['cpf']);
    
    $stmt = $pdo->prepare("SELECT * FROM cadastro WHERE cpf = ?");
    $stmt->execute([$cpf_limpo]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['senha'], $user['senha'])) {
        $_SESSION['cpf'] = $user['cpf'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['nome_cliente'] = $user['nome']; // Para carrinho
        
        // === REDIRECIONA SEM LOOP ===
        if (isset($_SESSION['pendente_carrinho'])) {
            $temp = $_SESSION['pendente_carrinho'];
            unset($_SESSION['pendente_carrinho']);
            $_SESSION['temp_adicionar'] = $temp;
            header("Location: adicionar.php");
            exit;
        } else {
            header("Location: $volta_para");
            exit;
        }
    } else {
        $erro = "CPF ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Habib Coffee</title>
    <style>
        body{background:#f5f5f5;font-family:Arial;text-align:center;padding:50px;}
        .box{max-width:380px;margin:auto;background:#fff;padding:35px;border-radius:15px;box-shadow:0 8px 25px rgba(0,0,0,.1);}
        input{width:100%;padding:14px;margin:10px 0;border:1px solid #ccc;border-radius:8px;font-size:16px;}
        button{width:100%;padding:14px;background:#8B4513;color:#fff;border:none;border-radius:8px;font-size:18px;cursor:pointer;}
        button:hover{background:#723a0f;}
        .erro{color:red;margin:10px 0;font-weight:bold;}
        .voltar{margin-top:20px;font-size:14px;}
    </style>
</head>
<body>
<div class="box">
    <h2>☕ Entre na sua conta</h2>
    <?php if(isset($erro)) echo "<div class='erro'>$erro</div>"; ?>
    <form method="post">
        <input type="text" name="cpf" placeholder="CPF (com ou sem pontos)" required maxlength="14">
        <input type="password" name="senha" placeholder="Sua senha" required>
        <button type="submit">Entrar</button>
    </form>
    <p>Ainda não tem conta? <a href="cadastro.php?volta=<?= urlencode($volta_para) ?>" style="color:#8B4513;">Cadastre-se</a></p>
    <div class="voltar">
        <a href="<?= $volta_para ?>">← Voltar</a>
    </div>
</div>
</body>
</html>