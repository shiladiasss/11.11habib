<?php
session_start();
if (!isset($_SESSION['cpf'])) exit;

$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");
$id = $_GET['id'] ?? 0;

$pdo->prepare("DELETE FROM carrinho WHERE cod_produto = ? AND nome_cliente = ?")
    ->execute([$id, $_SESSION['nome']]);

header("Location: carrinho.php");
