-- Módulo 1 - Perfil do Usuário
-- Execute este script em instalações existentes antes de acessar a página de perfil.

ALTER TABLE users
  ADD COLUMN endereco varchar(255) DEFAULT NULL AFTER telefone,
  ADD COLUMN avatar_url varchar(255) DEFAULT NULL AFTER endereco;
