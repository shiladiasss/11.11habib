<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");

// === 1. FLAG PARA QUEBRAR LOOP ===
if (isset($_SESSION['redirecting_to_login'])) {
    unset($_SESSION['redirecting_to_login']);
    header("Location: inicial.php");
    exit;
}

// === 2. RESTAURA DADOS TEMPORÁRIOS DO LOGIN ===
if (isset($_SESSION['temp_adicionar'])) {
    $_SESSION['pendente_carrinho'] = $_SESSION['temp_adicionar'];
    unset($_SESSION['temp_adicionar']);
}

// === 3. SALVA INTENÇÃO DE COMPRA (DO FORMULÁRIO) ===
if ($_POST && isset($_POST['id']) && isset($_POST['qtd'])) {
    $_SESSION['pendente_carrinho'] = [
        'id' => (int)$_POST['id'],
        'qtd' => (int)$_POST['qtd']
    ];
}

// === 4. SE NÃO ESTIVER LOGADO, VAI PRO LOGIN ===
if (!isset($_SESSION['nome_cliente'])) {
    $_SESSION['redirecting_to_login'] = true;
    header("Location: login.php");
    exit;
}

// === 5. ADICIONA OU SOMA NO CARRINHO (SEM COLUNA ID) ===
if (isset($_SESSION['pendente_carrinho'])) {
    $id = $_SESSION['pendente_carrinho']['id'];
    $qtd = $_SESSION['pendente_carrinho']['qtd'];
    $nome_cliente = $_SESSION['nome_cliente'];

    // Pega preço do produto
    $stmt = $pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $preco = $stmt->fetchColumn();

    if (!$preco) {
        header("Location: inicial.php");
        exit;
    }

    $total_adicionar = $preco * $qtd;

    // === VERIFICA SE JÁ EXISTE (pela chave composta) ===
    $stmt = $pdo->prepare("SELECT quantidade, total FROM carrinho WHERE cod_produto = ? AND nome_cliente = ?");
    $stmt->execute([$id, $nome_cliente]);
    $existe = $stmt->fetch();

    if ($existe) {
        // === SOMA QUANTIDADE E TOTAL ===
        $nova_qtd = $existe['quantidade'] + $qtd;
        $novo_total = $existe['total'] + $total_adicionar;
        $pdo->prepare("UPDATE carrinho SET quantidade = ?, total = ? WHERE cod_produto = ? AND nome_cliente = ?")
            ->execute([$nova_qtd, $novo_total, $id, $nome_cliente]);
    } else {
        // === INSERE NOVO ITEM ===
        $pdo->prepare("INSERT INTO carrinho (cod_produto, data_compra, preco, nome_cliente, quantidade, total) 
                        VALUES (?, CURDATE(), ?, ?, ?, ?)")
            ->execute([$id, $preco, $nome_cliente, $qtd, $total_adicionar]);
    }

    unset($_SESSION['pendente_carrinho']);
    header("Location: carrinho.php");
    exit;
}

// === 6. SE CHEGOU AQUI SEM NADA, VAI PRA LOJA ===
header("Location: inicial.php");
exit;
?>