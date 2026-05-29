-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29-Maio-2026 às 20:07
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ngola_cv`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `valor_kwanza` decimal(10,2) NOT NULL,
  `plano_comprado` enum('premium','profissional') NOT NULL,
  `tipo` enum('mensal','unico') DEFAULT 'mensal',
  `referencia_multicaixa` varchar(100) DEFAULT NULL,
  `status` enum('pendente','aprovado','falhou','reembolsado') DEFAULT 'pendente',
  `data_solicitacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_confirmacao` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `resumes`
--

CREATE TABLE `resumes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `dados_json` longtext NOT NULL,
  `visualizacoes` int(11) DEFAULT 0,
  `downloads` int(11) DEFAULT 0,
  `is_finalizado` tinyint(1) DEFAULT 0,
  `ultima_versao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `resumes`
--

INSERT INTO `resumes` (`id`, `usuario_id`, `template_id`, `titulo`, `dados_json`, `visualizacoes`, `downloads`, `is_finalizado`, `ultima_versao`, `data_criacao`) VALUES
(1, 1, 1, 'Renato Monana - Currículo', '{\"nome\":\"Renato Monana\",\"profissao\":\"Especialista em Hardware\",\"email\":\"2008Monana@gmail.com\",\"telefone\":\"952995369\",\"endereco\":\"Luanda,Angola\",\"sobre\":\"Foi muito bom\",\"experiencias\":[{\"cargo\":\"Estagiario\",\"empresa\":\"Lisamev\",\"periodo\":\"Nov 2025-Fev 2026\",\"descricao\":\"Foi muito bom\"}],\"educacoes\":[{\"curso\":\"Tecnico de Informatica\",\"instituicao\":\"IPIKK\",\"periodo\":\"2022-2026\"}],\"habilidades\":[\"PHP\",\"Javascript\",\"HTML\"]}', 0, 4, 0, '2026-05-29 18:03:47', '2026-05-29 17:04:03');

-- --------------------------------------------------------

--
-- Estrutura da tabela `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `plano_requerido` enum('gratuito','premium','profissional') DEFAULT 'gratuito',
  `html_estrutura` text NOT NULL,
  `css_estilo` text NOT NULL,
  `miniatura_url` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `templates`
--

INSERT INTO `templates` (`id`, `nome`, `slug`, `descricao`, `plano_requerido`, `html_estrutura`, `css_estilo`, `miniatura_url`, `ordem`, `ativo`) VALUES
(1, 'Moderno Azul', 'moderno-azul', 'Template limpo e profissional com destaque em azul', 'gratuito', '<div class=\"cv-moderno\">\r\n    <div class=\"cv-header\">\r\n        <div class=\"cv-foto\">{{foto}}</div>\r\n        <div class=\"cv-titulo\">\r\n            <h1>{{nome}}</h1>\r\n            <p>{{profissao}}</p>\r\n        </div>\r\n    </div>\r\n    <div class=\"cv-sobre\">\r\n        <h3><i class=\"fas fa-user\"></i> Sobre</h3>\r\n        <p>{{sobre}}</p>\r\n    </div>\r\n    <div class=\"cv-experiencias\">\r\n        <h3><i class=\"fas fa-briefcase\"></i> Experiência Profissional</h3>\r\n        {{experiencias}}\r\n    </div>\r\n    <div class=\"cv-educacao\">\r\n        <h3><i class=\"fas fa-graduation-cap\"></i> Formação Acadêmica</h3>\r\n        {{educacoes}}\r\n    </div>\r\n    <div class=\"cv-habilidades\">\r\n        <h3><i class=\"fas fa-code\"></i> Habilidades</h3>\r\n        {{habilidades}}\r\n    </div>\r\n    <div class=\"cv-footer\">\r\n        <p><i class=\"fas fa-envelope\"></i> {{email}} | <i class=\"fas fa-phone\"></i> {{telefone}} | <i class=\"fas fa-map-marker-alt\"></i> {{endereco}}</p>\r\n    </div>\r\n</div>', '.cv-moderno { font-family: \"Inter\", Arial, sans-serif; max-width: 900px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }\r\n.cv-header { background: linear-gradient(135deg, #2c3e66, #1a2a4a); color: white; padding: 30px; display: flex; align-items: center; gap: 30px; flex-wrap: wrap; }\r\n.cv-foto { width: 120px; height: 120px; border-radius: 50%; overflow: hidden; background: #fff; display: flex; align-items: center; justify-content: center; border: 4px solid #e67e22; }\r\n.cv-foto img { width: 100%; height: 100%; object-fit: cover; }\r\n.cv-foto i { font-size: 60px; color: #999; }\r\n.cv-titulo h1 { margin: 0 0 5px 0; font-size: 28px; }\r\n.cv-titulo p { margin: 0; opacity: 0.9; font-size: 16px; }\r\n.cv-sobre, .cv-experiencias, .cv-educacao, .cv-habilidades { padding: 20px 30px; border-bottom: 1px solid #eee; }\r\n.cv-sobre h3, .cv-experiencias h3, .cv-educacao h3, .cv-habilidades h3 { color: #2c3e66; margin-bottom: 15px; font-size: 18px; }\r\n.cv-sobre p { line-height: 1.6; color: #444; }\r\n.experiencia-item, .educacao-item { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; }\r\n.experiencia-item:last-child, .educacao-item:last-child { border-bottom: none; }\r\n.experiencia-item h4, .educacao-item h4 { color: #333; margin-bottom: 5px; font-size: 16px; }\r\n.empresa, .instituicao { font-weight: 500; color: #e67e22; margin: 5px 0; }\r\n.periodo { color: #888; font-size: 12px; margin-bottom: 8px; }\r\n.habilidades-container ul { list-style: none; display: flex; flex-wrap: wrap; gap: 10px; margin: 0; padding: 0; }\r\n.habilidades-container li { background: #2c3e66; color: white; padding: 6px 15px; border-radius: 20px; font-size: 13px; }\r\n.cv-footer { background: #f8f9fa; padding: 15px 30px; text-align: center; color: #666; font-size: 13px; }\r\n.cv-footer i { margin-right: 5px; color: #e67e22; }', 'assets/img/template-moderno-azul.jpg', 1, 1),
(2, 'Clássico Elegante', 'classico-elegante', 'Design tradicional e sofisticado', 'gratuito', '<div class=\"cv-classico\">\r\n    <div class=\"cv-sidebar\">\r\n        <div class=\"cv-foto\">{{foto}}</div>\r\n        <h2>{{nome}}</h2>\r\n        <p class=\"profissao\">{{profissao}}</p>\r\n        <hr>\r\n        <div class=\"contato\">\r\n            <h4><i class=\"fas fa-address-card\"></i> Contato</h4>\r\n            <p><i class=\"fas fa-envelope\"></i> {{email}}</p>\r\n            <p><i class=\"fas fa-phone\"></i> {{telefone}}</p>\r\n            <p><i class=\"fas fa-map-marker-alt\"></i> {{endereco}}</p>\r\n        </div>\r\n        <div class=\"habilidades-sidebar\">\r\n            <h4><i class=\"fas fa-code\"></i> Habilidades</h4>\r\n            {{habilidades}}\r\n        </div>\r\n    </div>\r\n    <div class=\"cv-main\">\r\n        <div class=\"sobre\">\r\n            <h3><i class=\"fas fa-user\"></i> Sobre</h3>\r\n            <p>{{sobre}}</p>\r\n        </div>\r\n        <div class=\"experiencias\">\r\n            <h3><i class=\"fas fa-briefcase\"></i> Experiência</h3>\r\n            {{experiencias}}\r\n        </div>\r\n        <div class=\"educacao\">\r\n            <h3><i class=\"fas fa-graduation-cap\"></i> Formação</h3>\r\n            {{educacoes}}\r\n        </div>\r\n    </div>\r\n</div>', '.cv-classico { display: flex; font-family: Georgia, serif; max-width: 1000px; margin: 0 auto; background: white; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }\r\n.cv-sidebar { width: 33%; background: #2c3e66; color: white; padding: 30px 20px; }\r\n.cv-foto { width: 150px; height: 150px; border-radius: 50%; overflow: hidden; margin: 0 auto 20px; background: white; border: 4px solid #e67e22; }\r\n.cv-foto img { width: 100%; height: 100%; object-fit: cover; }\r\n.cv-foto i { font-size: 80px; color: #999; display: block; text-align: center; line-height: 150px; }\r\n.cv-sidebar h2 { text-align: center; margin: 10px 0; font-size: 22px; }\r\n.cv-sidebar .profissao { text-align: center; opacity: 0.9; font-size: 14px; }\r\n.cv-sidebar hr { margin: 20px 0; border-color: rgba(255,255,255,0.2); }\r\n.cv-sidebar h4 { margin: 15px 0 10px; font-size: 16px; border-bottom: 2px solid #e67e22; display: inline-block; }\r\n.contato p { margin: 8px 0; font-size: 12px; word-break: break-word; }\r\n.contato i { width: 20px; margin-right: 5px; color: #e67e22; }\r\n.habilidades-sidebar ul { list-style: none; padding: 0; margin-top: 10px; }\r\n.habilidades-sidebar li { background: rgba(255,255,255,0.2); margin: 5px 0; padding: 5px 10px; border-radius: 5px; font-size: 12px; }\r\n.cv-main { width: 67%; padding: 30px; }\r\n.cv-main h3 { color: #2c3e66; border-bottom: 2px solid #e67e22; padding-bottom: 8px; margin: 20px 0 15px; font-size: 18px; }\r\n.cv-main .sobre { margin-top: 0; }\r\n.cv-main .sobre p { line-height: 1.6; color: #444; }\r\n.experiencia-item, .educacao-item { margin-bottom: 20px; }\r\n.experiencia-item h4, .educacao-item h4 { color: #333; margin-bottom: 5px; }\r\n.empresa, .instituicao { font-weight: 500; color: #e67e22; margin: 5px 0; }\r\n.periodo { color: #888; font-size: 12px; margin-bottom: 8px; }', 'assets/img/template-classico-elegante.jpg', 2, 1),
(3, 'Executivo Premium', 'executivo-premium', 'Design corporativo para altos executivos', 'premium', '<div class=\"cv-executivo\"><div class=\"topo\"><h1>{{nome}}</h1><h2>{{profissao}}</h2></div><div class=\"conteudo\">{{experiencias}}{{educacoes}}{{certificacoes}}</div></div>', '.cv-executivo { font-family: \"Helvetica Neue\", Arial, sans-serif; max-width: 1000px; margin: 0 auto; } .cv-executivo .topo { background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; padding: 40px; text-align: center; } .cv-executivo h1 { font-size: 36px; margin: 0; } .cv-executivo .conteudo { padding: 30px; }', 'assets/img/template-executivo-premium.jpg', 3, 1),
(4, 'Minimalista Moderno', 'minimalista-moderno', 'Design limpo e contemporâneo', 'profissional', '<div class=\"cv-minimal\"><div class=\"grid\"><div class=\"col-esquerda\"><img src=\"{{foto}}\"><h1>{{nome}}</h1><p>{{profissao}}</p></div><div class=\"col-direita\"><h3>Sobre</h3><p>{{sobre}}</p><h3>Experiência</h3>{{experiencias}}</div></div></div>', '.cv-minimal { font-family: \"Inter\", sans-serif; max-width: 800px; margin: 0 auto; } .cv-minimal .grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; } .cv-minimal .col-esquerda { background: #f8f9fa; padding: 20px; } .cv-minimal h3 { color: #0066cc; margin-top: 20px; }', 'assets/img/template-minimalista-moderno.jpg', 4, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(120) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `plano` enum('gratuito','premium','profissional') DEFAULT 'gratuito',
  `data_expiracao_plano` date DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expira` datetime DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `nome_completo`, `email`, `senha_hash`, `telefone`, `plano`, `data_expiracao_plano`, `reset_token`, `reset_token_expira`, `data_cadastro`, `ultimo_login`, `ativo`) VALUES
(1, 'Renato Monana', '2008Monana@gmail.com', '$2y$10$8dKlekPa9BAW4E3rxfrzP.2.tNGNJrHz7DaT6CYdsrV2vAZBTKpim', NULL, 'gratuito', NULL, NULL, NULL, '2026-05-29 15:36:11', '2026-05-29 16:23:53', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_lida` (`usuario_id`,`lida`);

--
-- Índices para tabela `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_usuario_pagamento` (`usuario_id`);

--
-- Índices para tabela `resumes`
--
ALTER TABLE `resumes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Índices para tabela `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_plano` (`plano`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `resumes`
--
ALTER TABLE `resumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);

--
-- Limitadores para a tabela `resumes`
--
ALTER TABLE `resumes`
  ADD CONSTRAINT `resumes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resumes_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
