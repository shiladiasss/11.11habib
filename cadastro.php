<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");

// Página para onde o usuário será redirecionado após cadastrar
$volta_para = $_GET['volta'] ?? 'inicial.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Limpa CPF
    $cpf_limpo = preg_replace('/\D/', '', $_POST['cpf']);

    // Verifica se já existe
    $check = $pdo->prepare("SELECT cpf FROM cadastro WHERE cpf = ?");
    $check->execute([$cpf_limpo]);

    if ($check->fetch()) {
        $erro = "CPF já cadastrado!";
    } else {

        // Hash da senha
        $hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

        // Salva no banco
        $sql = "INSERT INTO cadastro 
                (nome, cpf, endereco, cep, telefone, email, data_nascimento, senha)
                VALUES (?,?,?,?,?,?,?,?)";

        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            $_POST['nome'],
            $cpf_limpo,
            $_POST['endereco'],
            $_POST['cep'],
            $_POST['telefone'],
            $_POST['email'],
            $_POST['data_nascimento'],
            $hash
        ]);

        if ($ok) {
            // Login automático após cadastro
            $_SESSION['cpf']  = $cpf_limpo;
            $_SESSION['nome'] = $_POST['nome'];
            header("Location: $volta_para");
            exit;
        } else {
            $erro = "Erro ao salvar no banco.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro Habib</title>
    <style>
        body{background:#f5f5f5;font-family:Arial;text-align:center;padding:40px;}
        .box{max-width:420px;margin:auto;background:#fff;padding:40px;border-radius:20px;box-shadow:0 15px 40px rgba(0,0,0,.2);}
        h2{color:#8B4513;margin-bottom:20px;}
        input{width:100%;padding:16px;margin:12px 0;border:1px solid:#ddd;border-radius:10px;font-size:16px;}
        button{width:100%;padding:18px;background:#8B4513;color:#fff;border:none;border-radius:10px;font-size:20px;cursor:pointer;}
        button:hover{background:#723a0f;}
        .erro{color:red;margin:15px 0;font-weight:bold;}
    </style>
</head>
<body>

<div class="box">
    <h2>☕ CRIE SUA CONTA</h2>

    <?php if(isset($erro)) echo "<div class='erro'>$erro</div>"; ?>

    <form method="post">
        <input name="nome" placeholder="Nome completo" required>
        <input name="cpf" placeholder="CPF (com ou sem pontos)" required>
        <input name="endereco" placeholder="Endereço" required>
        <input name="cep" placeholder="CEP" required>
        <input name="telefone" placeholder="Telefone" required>
        <input name="email" type="email" placeholder="E-mail" required>
        <input name="data_nascimento" type="date" required>
        <input name="senha" type="password" placeholder="Senha (mín. 6)" minlength="6" required>
        <button type="submit">CADASTRAR E CONTINUAR</button>
    </form>
</div>

</body>
</html>
