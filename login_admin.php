<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$erro = '';

if ($_POST) {
    $login = preg_replace('/\D/', '', $_POST['login']); // Remove tudo que não é número
    $senha = $_POST['senha'];

    if (empty($login) || empty($senha)) {
        $erro = "Preencha login e senha!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE login = ?");
        $stmt->execute([$login]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['admin'] = true;
            $_SESSION['admin_nome'] = $admin['nome'];
            header("Location: admin_clientes.php");
            exit;
        } else {
            $erro = "Acesso negado! Verifique login e senha.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Habib Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{background:#f0f2f5;font-family:Arial;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
        .box{max-width:400px;width:100%;background:#fff;padding:40px;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.1);text-align:center;}
        h2{color:#8B4513;margin-bottom:20px;}
        input{width:100%;padding:14px;margin:10px 0;border:1px solid #ccc;border-radius:8px;font-size:16px;}
        button{width:100%;padding:14px;background:#8B4513;color:#fff;border:none;border-radius:8px;font-size:18px;cursor:pointer;}
        button:hover{background:#723a0f;}
        .erro{color:#e74c3c;margin:15px 0;font-weight:bold;background:#ffe6e6;padding:10px;border-radius:8px;}
        .voltar{margin-top:20px;}
        .voltar a{color:#8B4513;text-decoration:none;}
    </style>
</head>
<body>
<div class="box">
    <h2>ADMIN LOGIN</h2>
    <?php if($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="login" placeholder="CPF ou Login (ex: 123456789)" required maxlength="11" value="123456789">
        <input type="password" name="senha" placeholder="Senha (ex: admin)" required value="admin">
        <button type="submit">ENTRAR COMO ADMIN</button>
    </form>
    <div class="voltar">
        <a href="inicial.php">Voltar à Loja</a>
    </div>
</div>
</body>
</html>

<?php
echo password_verify('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') ? 'SENHA CORRETA' : 'SENHA ERRADA';
?>