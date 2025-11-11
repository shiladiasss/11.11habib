<?php
session_start();
if (!isset($_SESSION['cpf'])) { header("Location: login.php"); exit; }

$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");
$itens = $pdo->prepare("SELECT c.*, p.nome FROM carrinho c JOIN produtos p ON c.cod_produto = p.id WHERE nome_cliente = ?");
$itens->execute([$_SESSION['nome']]);
$carrinho = $itens->fetchAll();
$total = array_sum(array_column($carrinho, 'total'));
?>

<!DOCTYPE html><html lang="pt-BR"><head>
<link rel="icon" type="image/png" href="imagens/logo.png">
<meta charset="UTF-8"><title>Meu Carrinho</title><style>body{background:#f5f5f5;font-family:Arial;padding:30px;}.container{max-width:800px;margin:auto;background:#fff;padding:30px;border-radius:15px;}table{width:100%;border-collapse:collapse;margin:20px 0;}th,td{border:1px solid #ddd;padding:12px;text-align:center;}th{background:#8B4513;color:#fff;}.total{font-size:24px;font-weight:bold;color:#8B4513;}</style></head><body>
<div class="container">
    <h1>Meu Carrinho</h1>
    <?php if(empty($carrinho)): ?>
        <p>Carrinho vazio.</p>
    <?php else: ?>
        <table>
            <tr><th>Produto</th><th>Preço</th><th>Qty</th><th>Total</th></tr>
            <?php foreach($carrinho as $item): ?>
            <tr>
                <td><?= $item['nome'] ?></td>
                <td>R$ <?= number_format($item['preco'],2,',','.') ?></td>
                <td><?= $item['quantidade'] ?></td>
                <td>R$ <?= number_format($item['total'],2,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <p class="total">Total: R$ <?= number_format($total,2,',','.') ?></p>
        <button onclick="alert('Compra finalizada! Entraremos em contato.')" style="background:#8B4513;color:#fff;padding:15px 30px;border:none;border-radius:10px;cursor:pointer;font-size:18px;">Finalizar Compra</button>
    <?php endif; ?>
</div>
<div style="text-align:center;margin:30px 0;">
    <a href="inicial.php" style="background:#28a745;color:#fff;padding:15px 30px;border-radius:10px;text-decoration:none;font-weight:bold;margin:0 10px;">
        CONTINUAR COMPRANDO
    </a>
    <a href="finalizar.php" style="background:#dc3545;color:#fff;padding:15px 30px;border-radius:10px;text-decoration:none;font-weight:bold;margin:0 10px;">
        PAGAR AGORA
    </a>
</div>
</body></html>
