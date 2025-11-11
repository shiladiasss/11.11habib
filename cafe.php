<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ===== DADOS DA EMPRESA =====
$empresa = $pdo->query("SELECT * FROM empresa LIMIT 1")->fetch();

// ===== USUÁRIO LOGADO =====
$logado = isset($_SESSION['cpf']);
$nome   = $logado ? $_SESSION['nome'] : 'Visitante';

// ===== QUANTIDADE NO CARRINHO =====
$carrinho_qtd = 0;
if ($logado) {
    $stmt = $pdo->prepare("SELECT SUM(quantidade) FROM carrinho WHERE nome_cliente = ?");
    $stmt->execute([$_SESSION['nome']]);
    $carrinho_qtd = $stmt->fetchColumn() ?: 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>História do Café - Habib Special Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="imagens/logo.png">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
        html, body{height:100%;}
        body{background:#f5f5f5;color:#333;display:flex;flex-direction:column;min-height:100vh;}
        header{background:#8B4513;color:#fff;padding:15px 20px;}
        .topbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;}
        .logo img{height:60px;}
        .search{flex:1;max-width:400px;position:relative;}
        .search form{width:100%;}
        .search input{width:100%;padding:12px 45px 12px 15px;border:none;border-radius:25px;font-size:16px;outline:none;}
        .search button{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:#666;font-size:20px;cursor:pointer;}
        .search button:hover{color:#8B4513;}
        .user, .cart a{color:#fff;text-decoration:none;}
        .cart{position:relative;}
        .cart span{position:absolute;top:-10px;right:-10px;background:#ff4444;color:#fff;width:20px;height:20px;border-radius:50%;font-size:12px;display:flex;align-items:center;justify-content:center;}

        nav{background:#fff;text-align:center;padding:10px 0;box-shadow:0 2px 5px rgba(0,0,0,.1);}
        nav a{margin:0 25px;color:#333;text-decoration:none;font-weight:bold;font-size:17px;position:relative;}
        nav a::after{content:'';position:absolute;bottom:-6px;left:0;width:0;height:2px;background:#8B4513;transition:.3s;}
        nav a:hover::after{width:100%;}
        nav a.active{color:#8B4513;}
        nav a.active::after{width:100%;}

        .container{max-width:1200px;margin:40px auto;padding:0 20px;}
        .page-title{text-align:center;margin-bottom:40px;color:#8B4513;}
        .page-title h1{font-size:36px;margin-bottom:10px;}
        .page-title p{font-size:18px;color:#666;}

        .section{margin-bottom:60px;background:#fff;padding:30px;border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,.05);}
        .section h2{font-size:28px;color:#8B4513;margin-bottom:20px;text-align:center;position:relative;}
        .section h2::after{content:'';position:absolute;bottom:-10px;left:50%;transform:translateX(-50%);width:80px;height:3px;background:#8B4513;}
        .section p{line-height:1.8;font-size:16px;margin-bottom:18px;text-align:justify;}
        .section ul{list-style:disc;margin-left:25px;margin-bottom:18px;}
        .section ul li{margin-bottom:10px;}

        .highlight{background:#8B4513;color:#fff;padding:20px;border-radius:12px;text-align:center;font-size:18px;margin:30px 0;font-weight:bold;}

        footer{background:#8B4513;color:#fff;padding:40px 20px;margin-top:auto;}
        .footer-content{max-width:1200px;margin:auto;display:flex;flex-wrap:wrap;gap:40px;align-items:flex-start;justify-content:space-between;}
        .footer-content img{height:80px;}
        .footer-content div{flex:1;min-width:200px;}
        .social a{color:#fff;font-size:24px;margin-right:15px;}
        .social a:hover{transform:scale(1.2);}

        @media(max-width:768px){
            .topbar{flex-direction:column;text-align:center;}
            nav a{margin:0 15px;font-size:15px;}
            .page-title h1{font-size:28px;}
            .section{padding:20px;}
            .footer-content{flex-direction:column;text-align:center;}
            .footer-content img{margin:0 auto 20px;}
        }
    </style>
</head>
<body>

<header>
    <div class="topbar">
        <div class="logo"><a href="inicial.php"><img src="imagens/logo.png" alt="Habib"></a></div>
        <div class="search">
            <form action="inicial.php" method="get">
                <input type="text" name="q" placeholder="Buscar cafés..." autofocus>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="user">
            <i class="far fa-user"></i> Olá, <strong><?= $nome ?></strong><br>
            <?php if($logado): ?>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="#" id="btn-abre-modal" style="color:#fff;text-decoration:underline;">Entre ou cadastre-se</a>
            <?php endif; ?>
        </div>
        <div class="cart">
            <a href="carrinho.php">
                <i class="fas fa-shopping-bag"></i>
                <?php if($carrinho_qtd>0): ?><span><?= $carrinho_qtd ?></span><?php endif; ?>
            </a>
        </div>
    </div>

    <!-- MODAL -->
    <?php if(!$logado): ?>
    <div id="modal-login" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.8);z-index:9999;">
        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;width:90%;max-width:450px;border-radius:20px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.6);">
            <div style="background:#8B4513;color:#fff;padding:25px;text-align:center;">
                <i class="fas fa-coffee"></i> BEM-VINDO À HABIB COFFEE
            </div>
            <div style="padding:40px;text-align:center;">
                <p style="font-size:18px;color:#555;margin-bottom:30px;">Escolha uma opção:</p>
                <a href="login.php?volta=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                   style="display:block;margin:15px 0;padding:18px;background:#8B4513;color:#fff;border-radius:12px;text-decoration:none;font-size:18px;font-weight:bold;">
                    <i class="fas fa-sign-in-alt"></i> JÁ TENHO CONTA
                </a>
                <a href="cadastro.php?volta=inicial.php" 
                   style="display:block;margin:18px 0;padding:20px;background:#28a745;color:#fff;border-radius:14px;text-decoration:none;font-size:19px;font-weight:bold;">
                   <i class="fas fa-user-plus"></i> CRIAR CONTA GRÁTIS
                </a>
                <button onclick="document.getElementById('modal-login').style.display='none'" 
                        style="margin-top:25px;background:none;border:none;color:#999;font-size:30px;cursor:pointer;width:100%;">
                    X FECHAR
                </button>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('btn-abre-modal').onclick = function(e) {
        e.preventDefault();
        document.getElementById('modal-login').style.display = 'block';
    }
    </script>
    <?php endif; ?>
</header>

<nav>
    <a href="inicial.php">INÍCIO</a>
    <a href="cafe.php" class="active">CAFÉ</a>
    <a href="#">ACESSÓRIOS</a>
    <a href="#">PROMOÇÕES</a>
</nav>

<div class="container">
    <div class="page-title">
        <h1>A História do Café</h1>
        <p>Da Etiópia ao Brasil: uma jornada de sabor, cultura e tradição</p>
    </div>

    <div class="section">
        <h2>Origem do Café</h2>
        <p>O café é uma das bebidas mais consumidas no mundo, com uma jornada rica em lendas, descobertas e transformações econômicas. Sua origem remonta à <strong>Etiópia, no século IX</strong>, onde, segundo a lenda de <em>Kaldi</em>, um pastor de cabras descobriu os efeitos estimulantes dos grãos vermelhos ao ver seus animais dançando após comê-los.</p>
        <p>De lá, o café se espalhou pela <strong>Península Arábica</strong>, tornando-se uma bebida sagrada no mundo islâmico no século XV, consumida em mesquitas para manter os fiéis acordados durante as orações noturnas. No século XVI, chegou à <strong>Europa</strong>, inicialmente como remédio medicinal, e logo se popularizou em cafés parisienses e vienenses, tornando-se símbolo de socialização e intelectualidade.</p>
        <p>O café árabe (<em>Coffea arabica</em>), a variedade mais nobre, dominou o comércio global. No século XVII, holandeses e franceses o levaram para colônias nas Américas, plantando as sementes de uma revolução econômica.</p>
    </div>

    <div class="highlight">
        O café não é apenas uma bebida — é cultura, história e paixão.
    </div>

    <div class="section">
        <h2>O Café Gourmet: Qualidade e Sofisticação</h2>
        <p>O <strong>café gourmet</strong> representa o ápice da qualidade, diferenciando-se do café tradicional por seu processo artesanal e rigoroso. Classificado pela <strong>ABIC</strong> com nota acima de <strong>7,3 pontos</strong> no Programa de Qualidade do Café (PQC), o gourmet exige:</p>
        <ul>
            <li>Grãos <strong>100% arábica</strong> colhidos manualmente</li>
            <li>Cultivo em <strong>altitudes acima de 1.000m</strong></li>
            <li><strong>Torra média</strong> que preserva aromas sutis de frutas, flores e chocolate</li>
            <li>Origem controlada e rastreabilidade</li>
        </ul>
        <p>No Brasil, o gourmet ganhou força nos anos 2000, impulsionado pela demanda global por <strong>cafés especiais</strong> (nota acima de 80 pela SCA). Marcas como <em>Baggio</em> e <em>Fazenda Aliança</em> destacam-se por blends com notas sensoriais complexas, como caramelo e cacau, cultivados em regiões como <strong>Minas Gerais</strong> e <strong>São Paulo</strong>.</p>
        <p>É uma <strong>experiência sensorial</strong>, onde cada xícara conta a história do terroir e das mãos que a produziram.</p>
    </div>

    <div class="section">
        <h2>O Café no Brasil: De Ouro Verde a Líder Global</h2>
        <p>O <strong>Brasil é o maior produtor mundial de café</strong>, responsável por <strong>35% da produção global</strong>, com o estado de <strong>Minas Gerais</strong> sozinho equivalendo a um país inteiro.</p>
        <p>Sua história começou em <strong>1727</strong>, quando o sargento <em>Francisco de Melo Palheta</em> contrabandeou mudas da Guiana Francesa para Belém (Pará). As primeiras plantações no Norte falharam pelo clima úmido, mas mudas levadas ao Maranhão e Bahia prosperaram.</p>
        <p>O verdadeiro boom veio no <strong>Vale do Paraíba (RJ)</strong>, a partir de 1820, com o café se tornando o <strong>"ouro verde"</strong> do Império. <strong>São Paulo</strong> e <strong>Minas Gerais</strong> dominaram o <strong>Ciclo do Café (1850-1930)</strong>, financiando ferrovias, cidades e a imigração europeia após a Lei Áurea (1888).</p>
        <p>O café representou <strong>70% das exportações brasileiras</strong>, moldando a <em>República Café com Leite</em>. Crises como a Superprodução de 1929 levaram à Queima de Estoques e ao Convênio Internacional do Café (1940).</p>
        <p>Hoje, o Brasil produz <strong>3 milhões de toneladas anuais</strong>, com foco em sustentabilidade e cafés especiais. Regiões como <strong>Cerrado Mineiro</strong> e <strong>Sul de Minas</strong> oferecem grãos premiados, enquanto o gourmet impulsiona exportações para EUA, Alemanha e Japão.</p>
        <p>O café não é só economia — é cultura, com <strong>98% dos brasileiros consumindo diariamente</strong>, celebrando tradições como o <em>cafezinho</em>.</p>
    </div>

    <div class="highlight">
        O Brasil não planta café. Planta história.
    </div>
</div>

<footer>
    <div class="footer-content">
        <img src="imagens/logo.png" alt="Habib">
        <div><strong>Loja</strong><br><a href="inicial.php" style="color:#fff;">Início</a><br><a href="cafe.php" style="color:#fff;">Café</a><br><a href="#" style="color:#fff;">Acessórios</a><br><a href="#" style="color:#fff;">Promoções</a></div>
        <div>
            <strong>Atendimento</strong><br><?= nl2br($empresa['telefone'] ?? '(45) 99933-7261') ?><br>
            <strong>Endereço:</strong><br><?= nl2br($empresa['endereco'] ?? 'Rua Curitiba, 1417 – Neva\nCascavel – PR') ?>
        </div>
        <div class="social">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</footer>

</body>
</html>