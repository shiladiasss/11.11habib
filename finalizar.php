<?php
session_start();
if (!isset($_SESSION['cpf'])) {
    header("Location: login.php?volta=finalizar.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// === DADOS DO CLIENTE ===
$cpf = $_SESSION['cpf'];
$stmt = $pdo->prepare("SELECT * FROM cadastro WHERE cpf = ?");
$stmt->execute([$cpf]);
$cliente = $stmt->fetch();

// === ATUALIZA CEP ===
if (isset($_POST['atualizar_cep']) && !empty($_POST['cep_novo'])) {
    $novo_cep = preg_replace('/\D/', '', $_POST['cep_novo']);
    if (strlen($novo_cep) == 8) {
        $pdo->prepare("UPDATE cadastro SET cep = ? WHERE cpf = ?")->execute([$novo_cep, $cpf]);
        $cliente['cep'] = $novo_cep;
    }
    echo "<script>alert('CEP atualizado!'); location.reload();</script>";
    exit;
}

// === ITENS DO CARRINHO ===
$stmt = $pdo->prepare("SELECT c.*, p.nome, p.preco, p.imagem FROM carrinho c JOIN produtos p ON c.cod_produto = p.id WHERE c.nome_cliente = ?");
$stmt->execute([$_SESSION['nome']]);
$itens = $stmt->fetchAll();
$total_produtos = array_sum(array_column($itens, 'total'));

// === FRETE ===
$cep_origem = '85802000';
$cep_destino = preg_replace('/\D/', '', $cliente['cep']);
$peso = 1000;
$frete = 15.00; // fallback
if ($cep_destino) {
    $url = "https://cepcerto.com/ws/json-frete/$cep_origem/$cep_destino/$peso/16/11/16/0/0/0/SEU_TOKEN_AQUI";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    $frete = $data['Valor'] ?? 15.00;
}
$total_geral = $total_produtos + $frete;

// === PROCESSAR PAGAMENTO ===
if ($_POST && isset($_POST['forma'])) {
    $forma = $_POST['forma'];
    $pedido_id = time();

    $pdo->prepare("INSERT INTO pedidos (id, cpf, total, frete, forma, status) VALUES (?,?,?,?,?,?)")
        ->execute([$pedido_id, $cpf, $total_geral, $frete, $forma, 'Pendente']);

    $pdo->prepare("DELETE FROM carrinho WHERE nome_cliente = ?")->execute([$_SESSION['nome']]);

    header("Location: sucesso.php?pedido=$pedido_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - Habib Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{background:#f5f5f5;font-family:Arial;padding:20px;}
        .container{max-width:900px;margin:auto;background:#fff;padding:30px;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.1);}
        h1{color:#8B4513;text-align:center;}
        .cliente-info, .itens, .frete, .pagamento{display:grid;gap:15px;margin:20px 0;}
        .cliente-info h3, .itens h3, .frete h3, .pagamento h3{color:#8B4513;border-bottom:2px solid #8B4513;padding-bottom:10px;}
        input, select{padding:12px;border:1px solid #ccc;border-radius:8px;font-size:16px;}
        button{width:100%;padding:15px;background:#8B4513;color:#fff;border:none;border-radius:10px;font-size:18px;cursor:pointer;margin:5px 0;}
        button:hover{background:#723a0f;}
        table{width:100%;border-collapse:collapse;}
        th{background:#8B4513;color:#fff;padding:12px;text-align:left;}
        td{padding:12px;border-bottom:1px solid #eee;}
        .total{font-size:24px;color:#8B4513;font-weight:bold;text-align:right;}
        .formas{display:flex;flex-wrap:wrap;gap:10px;}
        .forma{width:48%;padding:15px;background:#f9f9f9;border-radius:10px;text-align:center;cursor:pointer;border:2px solid #ddd;}
        .forma:hover{border-color:#8B4513;}
        .forma input{display:none;}
        .forma input:checked + label{background:#8B4513;color:#fff;}
    </style>
</head>
<body>
<div class="container">
    <h1>FINALIZAR COMPRA</h1>
    <a href="carrinho.php" style="color:#666;">Voltar ao Carrinho</a>

    <!-- DADOS DO CLIENTE -->
    <div class="cliente-info">
        <h3>Dados de Entrega</h3>
        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['nome']) ?></p>
        <p><strong>Endereço:</strong> <?= htmlspecialchars($cliente['endereco']) ?></p>
        <p><strong>CEP:</strong> <?= htmlspecialchars($cliente['cep']) ?></p>
        <p><strong>Telefone:</strong> <?= htmlspecialchars($cliente['telefone']) ?></p>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($cliente['email']) ?></p>

        <form method="post" style="display:flex;gap:10px;margin-top:15px;">
            <input type="text" name="cep_novo" placeholder="Novo CEP?" value="<?= $cliente['cep'] ?>">
            <button type="submit" name="atualizar_cep" value="1">Atualizar CEP</button>
        </form>
    </div>

    <!-- ITENS -->
    <div class="itens">
        <h3>Itens Comprados</h3>
        <table>
            <tr><th>Produto</th><th>Qtd</th><th>Unit.</th><th>Total</th></tr>
            <?php foreach($itens as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nome']) ?></td>
                <td><?= $item['quantidade'] ?></td>
                <td>R$ <?= number_format($item['preco'],2,',','.') ?></td>
                <td>R$ <?= number_format($item['total'],2,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <p class="total">Subtotal: R$ <?= number_format($total_produtos,2,',','.') ?></p>
    </div>

    <!-- FRETE -->
    <div class="frete">
        <h3>Frete</h3>
        <p><strong>De:</strong> Cascavel-PR</p>
        <p><strong>Para:</strong> <?= $cep_destino ?></p>
        <p><strong>Valor:</strong> R$ <?= number_format($frete,2,',','.') ?></p>
        <p class="total">Total Geral: R$ <?= number_format($total_geral,2,',','.') ?></p>
    </div>

    <!-- PAGAMENTO -->
    <form method="post">
        <div class="pagamento">
            <h3>Forma de Pagamento</h3>
            <div class="formas">
                <label class="forma">
                    <input type="radio" name="forma" value="pix" checked required>
                    PIX
                </label>
                <label class="forma">
                    <input type="radio" name="forma" value="cartao">
                    Cartão
                </label>
                <label class="forma">
                    <input type="radio" name="forma" value="boleto">
                    Boleto
                </label>
                <label class="forma">
                    <input type="radio" name="forma" value="retirada">
                    Retirada na Loja
                </label>
            </div>
            <button type="submit">CONFIRMAR PAGAMENTO</button>
        </div>
    </form>
</div>
</body>
</html>