CREATE DATABASE IF NOT EXISTS tony_baterias;
USE tony_baterias;

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorias` (`id`, `nome`, `criado_em`) VALUES
(1, 'Baterias automotivas', '2026-09-21 10:45:47'),
(2, 'Baterias moto', '2026-09-21 10:45:47'),
(3, 'Acessórios', '2026-09-21 10:45:47'),
(4, 'Alimentos', '2026-09-21 10:45:47');

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `cnpj` (`cnpj`),
  KEY `idx_clientes_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` (`id`, `nome`, `cpf`, `cnpj`, `telefone`, `email`, `endereco`, `ativo`, `criado_em`, `atualizado_em`) VALUES
(1, 'João Pereira', '529.982.247-25', NULL, '(11) 98888-1111', 'joao.pereira@example.com', NULL, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47'),
(2, 'Oficina Mecânica Silva', NULL, '11.222.333/0001-81', '(11) 97777-2222', 'contato@oficinasilva.example.com', NULL, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47');

DROP TABLE IF EXISTS `fornecedores`;
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `contato` varchar(100) DEFAULT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fornecedores_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `fornecedores` (`id`, `nome`, `contato`, `cnpj`, `ativo`, `criado_em`, `atualizado_em`) VALUES
(1, 'Heliar Distribuidora SP', '(11) 3456-7890', '12.345.678/0001-90', 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47'),
(2, 'Acdelco Centro-Oeste', '(11) 2345-6789', '23.456.789/0001-01', 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47'),
(3, 'Distribuidora Pururuca Ltda', '(11) 4567-8901', NULL, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47');

DROP TABLE IF EXISTS `itens_pedido`;
CREATE TABLE IF NOT EXISTS `itens_pedido` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id` int UNSIGNED NOT NULL,
  `produto_id` int UNSIGNED NOT NULL,
  `quantidade` int UNSIGNED NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_itens_produto` (`produto_id`),
  KEY `idx_itens_pedido` (`pedido_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `movimentacoes_estoque`;
CREATE TABLE IF NOT EXISTS `movimentacoes_estoque` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `produto_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `fornecedor_id` int UNSIGNED DEFAULT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `quantidade` int UNSIGNED NOT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_mov_usuario` (`usuario_id`),
  KEY `fk_mov_fornecedor` (`fornecedor_id`),
  KEY `idx_mov_produto_data` (`produto_id`,`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `movimentacoes_estoque` (`id`, `produto_id`, `usuario_id`, `fornecedor_id`, `tipo`, `quantidade`, `observacao`, `criado_em`) VALUES
(1, 1, 1, 2, 'entrada', 8, 'Estoque inicial (carga de dados)', '2026-09-21 10:45:47'),
(2, 2, 1, 2, 'entrada', 5, 'Estoque inicial (carga de dados)', '2026-09-21 10:45:47'),
(3, 3, 1, NULL, 'entrada', 2, 'Estoque inicial (carga de dados)', '2026-09-21 10:45:47'),
(4, 4, 1, NULL, 'entrada', 4, 'Estoque inicial (carga de dados)', '2026-09-21 10:45:47'),
(5, 5, 1, 1, 'entrada', 6, 'Estoque inicial (carga de dados)', '2026-09-21 10:45:47'),
(6, 6, 1, 3, 'entrada', 15, 'Estoque inicial (carga de dados)', '2026-09-21 10:45:47');

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` int UNSIGNED DEFAULT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `status` enum('pendente','concluido','cancelado') NOT NULL DEFAULT 'pendente',
  `forma_pagamento` enum('dinheiro','pix','cartao_credito','cartao_debito','boleto') DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `observacao` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pedidos_usuario` (`usuario_id`),
  KEY `idx_pedidos_cliente` (`cliente_id`),
  KEY `idx_pedidos_status_data` (`status`,`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `categoria_id` int UNSIGNED DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text,
  `marca` varchar(80) DEFAULT NULL,
  `sku` varchar(40) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT '0.00',
  `garantia_meses` smallint UNSIGNED NOT NULL DEFAULT '0',
  `estoque_atual` int NOT NULL DEFAULT '0',
  `estoque_minimo` int NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `fk_produtos_categoria` (`categoria_id`),
  KEY `idx_produtos_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `produtos` (`id`, `categoria_id`, `nome`, `descricao`, `marca`, `sku`, `preco`, `garantia_meses`, `estoque_atual`, `estoque_minimo`, `ativo`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 'Acdelco 50Ah', 'Bateria Acdelco 50Ah, ideal para carros de passeio', 'Acdelco', 'BAT-ACD-50', 320.00, 12, 8, 3, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47'),
(2, 1, 'Acdelco 60Ah', 'Bateria Acdelco 60Ah, alta durabilidade', 'Acdelco', 'BAT-ACD-60', 380.00, 12, 5, 3, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47'),
(3, 1, 'Bateria Semi-Nova 45Ah', 'Bateria seminova revisada, 45Ah', NULL, 'BAT-SNV-45', 150.00, 3, 2, 2, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47'),
(4, 1, 'Bateria Semi-Nova 60Ah', 'Bateria seminova revisada, 60Ah', NULL, 'BAT-SNV-60', 150.00, 3, 4, 2, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47'),
(5, 1, 'Heliar 60Ah', 'Bateria Heliar 60Ah original', 'Heliar', 'BAT-HEL-60', 410.00, 18, 6, 3, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47'),
(6, 4, 'Pururuca Tradicional', 'Petisco tradicional vendido no balcão', NULL, NULL, 25.00, 0, 15, 5, 1, '2026-09-21 10:45:47', '2026-09-21 10:45:47');

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','vendedor') NOT NULL DEFAULT 'vendedor',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `ativo`, `criado_em`, `atualizado_em`) VALUES
(1, 'Tony', 'admin@tonybaterias.com', '$2b$10$FejHXJbjYNCjjutO.UHU.OTQ51MpF4qEfbfoSJGkee7BFpVa545q6', 'admin', 1, '2026-09-21 10:45:47', '2026-09-28 18:18:16'),
(2, 'Funcionário', 'funcionario@email.com', '$2b$10$qdoPJIZM1jT5hKdcBEkxaOr8DTcQikRZLKXSefn.JEaMCZMUqok8K', 'vendedor', 1, '2026-09-21 11:53:33', '2026-09-28 18:18:16');

-- DADOS DE DEMONSTRAÇÃO: 7 pedidos concluídos (R$ 2.655,00 em outubro/2026), 1 cancelado e 1 reposição de estoque.
-- Para um banco sem esses dados, apague daqui até a linha "FIM DOS DADOS DE DEMONSTRAÇÃO".
INSERT INTO `pedidos` (`id`, `cliente_id`, `usuario_id`, `status`, `forma_pagamento`, `valor_total`, `observacao`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 2, 'concluido', 'pix', 320.00, NULL, '2026-10-01 10:15:00', '2026-10-01 10:20:00'),
(2, NULL, 2, 'concluido', 'dinheiro', 75.00, 'Venda no balcão', '2026-10-02 14:40:00', '2026-10-02 14:45:00'),
(3, 2, 1, 'concluido', 'boleto', 760.00, NULL, '2026-10-05 09:20:00', '2026-10-05 09:25:00'),
(4, NULL, 2, 'concluido', 'cartao_debito', 150.00, NULL, '2026-10-06 16:05:00', '2026-10-06 16:10:00'),
(5, 1, 2, 'cancelado', 'pix', 410.00, 'Cliente desistiu da compra', '2026-10-07 13:25:00', '2026-10-07 13:40:00'),
(6, 1, 1, 'concluido', 'cartao_credito', 460.00, NULL, '2026-10-08 11:30:00', '2026-10-08 11:35:00'),
(7, 2, 2, 'concluido', 'pix', 790.00, NULL, '2026-10-09 15:10:00', '2026-10-09 15:15:00'),
(8, NULL, 1, 'concluido', 'dinheiro', 100.00, 'Venda no balcão', '2026-10-12 10:00:00', '2026-10-12 10:05:00');

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 1, 1, 320.00),
(2, 2, 6, 3, 25.00),
(3, 3, 2, 2, 380.00),
(4, 4, 4, 1, 150.00),
(5, 5, 5, 1, 410.00),
(6, 6, 5, 1, 410.00),
(7, 6, 6, 2, 25.00),
(8, 7, 1, 2, 320.00),
(9, 7, 3, 1, 150.00),
(10, 8, 6, 4, 25.00);

INSERT INTO `movimentacoes_estoque` (`id`, `produto_id`, `usuario_id`, `fornecedor_id`, `tipo`, `quantidade`, `observacao`, `criado_em`) VALUES
(7, 1, 2, NULL, 'saida', 1, 'Venda - Pedido #1', '2026-10-01 10:20:00'),
(8, 6, 2, NULL, 'saida', 3, 'Venda - Pedido #2', '2026-10-02 14:45:00'),
(9, 5, 1, 1, 'entrada', 4, 'Reposição de estoque', '2026-10-03 09:00:00'),
(10, 2, 1, NULL, 'saida', 2, 'Venda - Pedido #3', '2026-10-05 09:25:00'),
(11, 4, 2, NULL, 'saida', 1, 'Venda - Pedido #4', '2026-10-06 16:10:00'),
(12, 5, 1, NULL, 'saida', 1, 'Venda - Pedido #6', '2026-10-08 11:35:00'),
(13, 6, 1, NULL, 'saida', 2, 'Venda - Pedido #6', '2026-10-08 11:35:00'),
(14, 1, 2, NULL, 'saida', 2, 'Venda - Pedido #7', '2026-10-09 15:15:00'),
(15, 3, 2, NULL, 'saida', 1, 'Venda - Pedido #7', '2026-10-09 15:15:00'),
(16, 6, 1, NULL, 'saida', 4, 'Venda - Pedido #8', '2026-10-12 10:05:00');

UPDATE `produtos` SET `estoque_atual` = 5 WHERE `id` = 1;
UPDATE `produtos` SET `estoque_atual` = 3 WHERE `id` = 2;
UPDATE `produtos` SET `estoque_atual` = 1 WHERE `id` = 3;
UPDATE `produtos` SET `estoque_atual` = 3 WHERE `id` = 4;
UPDATE `produtos` SET `estoque_atual` = 9 WHERE `id` = 5;
UPDATE `produtos` SET `estoque_atual` = 6 WHERE `id` = 6;
-- FIM DOS DADOS DE DEMONSTRAÇÃO

ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `fk_itens_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_itens_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT;

ALTER TABLE `movimentacoes_estoque`
  ADD CONSTRAINT `fk_mov_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_mov_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mov_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT;

ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT;

ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;
