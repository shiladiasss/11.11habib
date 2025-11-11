<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");

// === LOGIN ADMIN (CPF: qualquer 9 números | SENHA: admin) ===
if (!isset($_SESSION['admin'])) {
    if ($_POST && preg_match('/^\d{9}$/', $_POST['cpf']) && $_POST['senha'] === 'admin') {
        $_SESSION['admin'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html><head><meta charset="UTF-8"><title>Login Admin</title>
        <style>body{background:#f5f5f5;font-family:Arial;text-align:center;padding:100px;}
        .box{max-width:380px;margin:auto;background:#fff;padding:40px;border-radius:20px;box-shadow:0 15px 40px rgba(0,0,0,.2);}
        input{width:100%;padding:16px;margin:10px 0;border:1px solid #ddd;border-radius:10px;}
        button{width:100%;padding:16px;background:#8B4513;color:#fff;border:none;border-radius:10px;font-size:18px;cursor:pointer;}
        .dica{font-size:13px;color:#666;margin-top:5px;}
        </style></head><body>
        <div class="box">
            <h2>🔐 PAINEL ADMIN</h2>
            <form method="post">
                <input name="cpf" placeholder="CPF (9 números)" maxlength="9" required>
                <div class="dica">Ex: 123456789</div>
                <input name="senha" type="password" placeholder="Senha" value="admin" required>
                <button type="submit">ENTRAR</button>
            </form>
        </div>
        </body></html>
        <?php exit;
    }
}

// === ADICIONAR PRODUTO ===
if (isset($_POST['acao']) && $_POST['acao'] === 'add') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $img = 'imagens/' . basename($_FILES['img']['name']);
    move_uploaded_file($_FILES['img']['tmp_name'], $img);
    $pdo->prepare("INSERT INTO produtos (nome, preco, imagem) VALUES (?,?,?)")
        ->execute([$nome, $preco, $img]);
}

// === EDITAR ===
if (isset($_POST['acao']) && $_POST['acao'] === 'edit') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    if ($_FILES['img']['name']) {
        $img = 'imagens/' . basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], $img);
        $pdo->prepare("UPDATE produtos SET nome=?, preco=?, imagem=? WHERE id=?")
            ->execute([$nome, $preco, $img, $id]);
    } else {
        $pdo->prepare("UPDATE produtos SET nome=?, preco=? WHERE id=?")
            ->execute([$nome, $preco, $id]);
    }
}
// === EXCLUIR COM SEGURANÇA ===
if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $img_path = $stmt->fetchColumn();
    
    if ($img_path && file_exists($img_path) && strpos($img_path, 'http') !== 0) {
        unlink($img_path);
    }
    
    $pdo->prepare("DELETE FROM produtos WHERE id = ?")->execute([$id]);
    header("Location: admin.php");
    exit;
}



$produtos = $pdo->query("SELECT * FROM produtos ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Habib Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{background:#f5f5f5;font-family:Arial;margin:0;padding:20px;}
        .container{max-width:1000px;margin:auto;background:#fff;padding:30px;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.1);}
        h1{color:#8B4513;text-align:center;margin-bottom:30px;}
        .form-add{background:#f9f9f9;padding:20px;border-radius:10px;margin-bottom:30px;}
        input, button{width:100%;padding:12px;margin:8px 0;border:1px solid #ccc;border-radius:8px;}
        button{background:#8B4513;color:#fff;border:none;cursor:pointer;font-size:16px;}
        button:hover{background:#723a0f;}
        table{width:100%;border-collapse:collapse;margin-top:20px;}
        th{background:#8B4513;color:#fff;padding:15px;text-align:center;}
        td{padding:15px;border-bottom:1px solid #eee;text-align:center;}
        .img-thumb{width:60px;height:60px;object-fit:cover;border-radius:8px;}
        .acoes a{color:#c33;margin:0 8px;text-decoration:none;}
        .edit-form{display:none;background:#fff;padding:15px;border:1px solid #ddd;border-radius:8px;margin-top:10px;}
    </style>
</head>
<body>
<div class="container">
    <h1><i class="fas fa-cog"></i> PAINEL ADMIN - HABIB COFFEE</h1>
    <a href="admin_clientes.php" style="position:absolute;top:50px;right:20px;color:#8B4513;;">Clientes</a>
    <a href="inicial.php" style="position:absolute;top:20px;right:20px;color:#8B4513;">← Voltar à Loja</a>

    <!-- FORM ADICIONAR -->
    <div class="form-add">
        <h3>Adicionar Novo Produto</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="add">
            <input name="nome" placeholder="Nome do Café" required>
            <input name="preco" type="number" step="0.01" placeholder="Preço (ex: 49.90)" required>
            <input type="file" name="img" accept="image/*" required>
            <button type="submit"><i class="fas fa-plus"></i> ADICIONAR PRODUTO</button>
        </form>
    </div>

    <!-- LISTA DE PRODUTOS -->
    <table>
        <tr><th>Imagem</th><th>Nome</th><th>Preço</th><th>Ações</th></tr>
        <?php foreach($produtos as $p): ?>
        <tr>
            <td><img src="<?= $p['imagem'] ?>" class="img-thumb"></td>
            <td><strong><?= $p['nome'] ?></strong></td>
            <td>R$ <?= number_format($p['preco'],2,',','.') ?></td>
            <td class="acoes">
                <a href="javascript:void(0)" onclick="edit(<?= $p['id'] ?>)"><i class="fas fa-edit"></i></a>
                <a href="?del=<?= $p['id'] ?>" onclick="return confirm('Excluir?')"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <tr class="edit-row" id="edit-<?= $p['id'] ?>" style="display:none;">
            <td colspan="4">
                <div class="edit-form">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="acao" value="edit">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <input name="nome" value="<?= $p['nome'] ?>" required>
                        <input name="preco" type="number" step="0.01" value="<?= $p['preco'] ?>" required>
                        <input type="file" name="img" accept="image/*">
                        <button type="submit">SALVAR ALTERAÇÕES</button>
                        <button type="button" onclick="this.closest('tr').style.display='none'">Cancelar</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
function edit(id) {
    document.querySelectorAll('.edit-row').forEach(r => r.style.display = 'none');
    document.getElementById('edit-'+id).style.display = 'table-row';
}
</script>
</body>
</html>