<?php
session_start();

// === PROTEÇÃO: SÓ ADMIN ENTRA ===
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login_admin.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// === EXCLUIR CLIENTE ===
if (isset($_GET['excluir'])) {
    $cpf = $_GET['excluir'];
    $pdo->prepare("DELETE FROM cadastro WHERE cpf = ?")->execute([$cpf]);
    $pdo->prepare("DELETE FROM carrinho WHERE nome_cliente IN (SELECT nome FROM cadastro WHERE cpf = ?)")
         ->execute([$cpf]); // Limpa carrinho
    echo "<script>alert('Cliente excluído!'); location.href='admin_clientes.php';</script>";
    exit;
}

// === EDITAR CLIENTE ===
if ($_POST && isset($_POST['editar_cpf'])) {
    $cpf = $_POST['editar_cpf'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = preg_replace('/\D/', '', $_POST['telefone']);
    $endereco = $_POST['endereco'];
    $cep = preg_replace('/\D/', '', $_POST['cep']);

    $pdo->prepare("UPDATE cadastro SET nome=?, email=?, telefone=?, endereco=?, cep=? WHERE cpf=?")
        ->execute([$nome, $email, $telefone, $endereco, $cep, $cpf]);

    echo "<script>alert('Cliente atualizado!'); location.reload();</script>";
    exit;
}

// === BUSCA ===
$busca = $_GET['busca'] ?? '';
$where = $busca ? "WHERE nome LIKE ? OR cpf LIKE ? OR email LIKE ?" : "";
$params = $busca ? ["%$busca%", "%$busca%", "%$busca%"] : [];

// === LISTA CLIENTES ===
$sql = "SELECT * FROM cadastro $where ORDER BY nome";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Clientes - Habib Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{background:#f0f2f5;font-family:Arial;margin:0;padding:20px;}
        .header{background:#8B4513;color:#fff;padding:15px;text-align:center;font-size:24px;position:relative;}
        .header a{color:#fff;text-decoration:none;position:absolute;left:20px;top:15px;}
        .container{max-width:1100px;margin:20px auto;background:#fff;padding:25px;border-radius:15px;box-shadow:0 8px 25px rgba(0,0,0,.1);}
        h1{color:#8B4513;text-align:center;margin-bottom:20px;}
        .busca{margin-bottom:20px;text-align:center;}
        .busca input{padding:12px;width:300px;border:1px solid #ccc;border-radius:8px;font-size:16px;}
        .busca button{padding:12px 20px;background:#8B4513;color:#fff;border:none;border-radius:8px;cursor:pointer;}
        .busca button:hover{background:#723a0f;}
        table{width:100%;border-collapse:collapse;margin-top:20px;}
        th{background:#8B4513;color:#fff;padding:15px;text-align:left;}
        td{padding:12px;border-bottom:1px solid #eee;}
        .acoes a{color:#8B4513;margin:0 8px;text-decoration:none;}
        .acoes a:hover{color:#d35400;}
        .edit-form{background:#f9f9f9;padding:15px;border-radius:8px;margin:10px 0;display:none;}
        .edit-form input{width:100%;padding:10px;margin:5px 0;border:1px solid #ccc;border-radius:6px;}
        .edit-form button{background:#27ae60;color:#fff;padding:10px;border:none;border-radius:6px;cursor:pointer;}
        .edit-form button:hover{background:#1e8449;}
        .excluir{color:#e74c3c;}
        .excluir:hover{color:#c0392b;}
        .total-clientes{background:#f8f9fa;padding:15px;border-radius:8px;text-align:center;font-weight:bold;color:#8B4513;margin-bottom:20px;}
    </style>
</head>
<body>
<div class="header">
    <a href="admin.php"><i class="fas fa-arrow-left"></i> Voltar</a>
    ADMIN - CLIENTES
</div>

<div class="container">
    <h1>GERENCIAR CLIENTES</h1>

    <div class="total-clientes">
        Total de Clientes: <?= count($clientes) ?>
    </div>

    <!-- BUSCA -->
    <form class="busca" method="get">
        <input type="text" name="busca" placeholder="Buscar por nome, CPF ou e-mail..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit"><i class="fas fa-search"></i> Buscar</button>
    </form>

    <!-- TABELA DE CLIENTES -->
    <table>
        <tr>
            <th>Nome</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Endereço</th>
            <th>CEP</th>
            <th>Ações</th>
        </tr>
        <?php foreach($clientes as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars($c['cpf']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['telefone']) ?></td>
            <td><?= htmlspecialchars($c['endereco']) ?></td>
            <td><?= htmlspecialchars($c['cep']) ?></td>
            <td class="acoes">
                <a href="javascript:void(0)" onclick="editar('<?= addslashes($c['cpf']) ?>','<?= addslashes($c['nome']) ?>','<?= addslashes($c['email']) ?>','<?= addslashes($c['telefone']) ?>','<?= addslashes($c['endereco']) ?>','<?= addslashes($c['cep']) ?>')">
                    Editar
                </a>
                <a href="?excluir=<?= $c['cpf'] ?>" class="excluir" onclick="return confirm('Excluir cliente <?= addslashes($c['nome']) ?>?')">
                    Excluir
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="7">
                <form method="post" class="edit-form" id="form-<?= $c['cpf'] ?>">
                    <input type="hidden" name="editar_cpf" value="<?= $c['cpf'] ?>">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <input type="text" name="nome" value="<?= htmlspecialchars($c['nome']) ?>" required placeholder="Nome">
                        <input type="email" name="email" value="<?= htmlspecialchars($c['email']) ?>" required placeholder="E-mail">
                        <input type="text" name="telefone" value="<?= htmlspecialchars($c['telefone']) ?>" required placeholder="Telefone">
                        <input type="text" name="endereco" value="<?= htmlspecialchars($c['endereco']) ?>" required placeholder="Endereço">
                        <input type="text" name="cep" value="<?= htmlspecialchars($c['cep']) ?>" required placeholder="CEP">
                        <button type="submit">SALVAR ALTERAÇÕES</button>
                    </div>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
function editar(cpf, nome, email, tel, end, cep) {
    const form = document.getElementById('form-' + cpf);
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
    // Preenche os campos
    form.querySelector('[name="nome"]').value = nome;
    form.querySelector('[name="email"]').value = email;
    form.querySelector('[name="telefone"]').value = tel;
    form.querySelector('[name="endereco"]').value = end;
    form.querySelector('[name="cep"]').value = cep;
}
</script>
</body>
</html>