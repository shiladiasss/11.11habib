-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11/11/2025 às 05:25
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `habib`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `login` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `admin`
--

INSERT INTO `admin` (`id`, `login`, `senha`, `nome`) VALUES
(3, '123456789', '$2y$10$pqQdmaRtXWQ20KI82HSaueN6lCU7D7lkZlQ4WbJbVNcv8TH8Qdw9i', 'Admin Habib');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cadastro`
--

CREATE TABLE `cadastro` (
  `nome` varchar(80) DEFAULT NULL,
  `cpf` varchar(11) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cep` int(11) DEFAULT NULL,
  `telefone` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cadastro`
--

INSERT INTO `cadastro` (`nome`, `cpf`, `endereco`, `cep`, `telefone`, `email`, `data_nascimento`, `senha`) VALUES
('Clicioni Soares', '04170030990', 'rua fortaleza', 85802000, 2147483647, 'clicioni@gmail.com', '1984-03-21', '$2y$10$9zL9OvR0KuJCPsdBpPpV6ufTuocp/M1ckKWnMOEtJaQ7M25U0ngJ.'),
('Eyshila Dias', '09830102939', 'rua fortaleza', 85802000, 2147483647, 'eyshila.dias@gmail.com', '2008-11-18', '$2y$10$HfofMStcVDnwmPhqr.JOfe4169QERkCy389C/e22bzhO9x8ZL2JbC'),
('Júlia Guesser', '15196926925', 'rua pio XII', 8580200, 2147483647, 'julia@gmail.com', '2007-05-21', '$2y$10$OITLdX.SyDmdOjDqfoeZLetHBfcUAVDulN4rD6Z41mSYJc5VlMeUK');

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `cod_produto` int(11) NOT NULL,
  `data_compra` date DEFAULT NULL,
  `preco` decimal(10,0) DEFAULT NULL,
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `nome_cliente` char(50) NOT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `total` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carrinho`
--

INSERT INTO `carrinho` (`cod_produto`, `data_compra`, `preco`, `forma_pagamento`, `nome_cliente`, `quantidade`, `total`) VALUES
(1, '2025-11-06', 50, NULL, 'Júlia Guesser', 1, 50),
(255, '2025-09-24', 50, '0', 'jonas', 2, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresa`
--

CREATE TABLE `empresa` (
  `nome` char(80) NOT NULL,
  `cnpj` int(11) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `telefone` int(11) NOT NULL,
  `email` varchar(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `empresa`
--

INSERT INTO `empresa` (`nome`, `cnpj`, `endereco`, `telefone`, `email`) VALUES
('habib', 85802000, 'rua assunção', 2147483647, 'eyshilafernanda2008@gmail.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cpf` varchar(11) DEFAULT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  `frete` decimal(8,2) DEFAULT NULL,
  `forma` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pendente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `cpf`, `total`, `frete`, `forma`, `status`, `criado_em`) VALUES
(1762519932, '09830102939', 101.00, 15.00, 'pix', 'Pendente', '2025-11-07 12:52:12'),
(1762521753, '09830102939', 360.00, 15.00, 'retirada', 'Pendente', '2025-11-07 13:22:33'),
(1762736532, '04373059969', 187.00, 15.00, 'pix', 'Pendente', '2025-11-10 01:02:12'),
(1762736901, '04170030990', 274.00, 15.00, 'pix', 'Pendente', '2025-11-10 01:08:21'),
(1762833105, '09830102939', 58.00, 15.00, 'pix', 'Pendente', '2025-11-11 03:51:45');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(8,2) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco`, `imagem`) VALUES
(4, 'BrazuCafé Chocolate e Mel', 43.09, 'imagens/4-079f460d1ab8566c0417474227437584-1024-1024.webp'),
(7, 'mor', 2.00, 'imagens/6f468c4fc2b57256a2a762f12078a266.jpg'),
(8, 'xerlis', 5.00, 'imagens/fcb8d8e4ba51e75532e2524c9218e9d7.jpg');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Índices de tabela `cadastro`
--
ALTER TABLE `cadastro`
  ADD PRIMARY KEY (`cpf`);

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`cod_produto`,`nome_cliente`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `cod_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
