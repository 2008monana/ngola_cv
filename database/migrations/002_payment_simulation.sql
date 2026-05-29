-- Módulo 3 - Pagamento Simulado
-- Execute este script em instalações existentes antes de usar o checkout.

ALTER TABLE payments
  ADD COLUMN metodo_pagamento enum('multicaixa_express','cartao','transferencia') DEFAULT 'multicaixa_express' AFTER tipo,
  ADD COLUMN titular varchar(120) DEFAULT NULL AFTER metodo_pagamento;
