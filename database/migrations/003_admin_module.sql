-- Módulo 4 - Área Administrativa
-- Execute em instalações existentes para ativar permissões de administrador.

ALTER TABLE users
  ADD COLUMN is_admin tinyint(1) DEFAULT 0 AFTER ativo;
