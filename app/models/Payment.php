<?php
// app/models/Payment.php

class Payment {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Lista os pagamentos de um usuário, mais recentes primeiro.
     */
    public function getByUser($usuario_id) {
        $sql = "SELECT * FROM payments WHERE usuario_id = :usuario_id ORDER BY data_solicitacao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    /**
     * Registra uma tentativa de pagamento simulada.
     */
    public function create($dados) {
        $sql = "INSERT INTO payments
                    (usuario_id, valor_kwanza, plano_comprado, tipo, metodo_pagamento, titular, referencia_multicaixa, status)
                VALUES
                    (:usuario_id, :valor_kwanza, :plano_comprado, :tipo, :metodo_pagamento, :titular, :referencia, :status)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':usuario_id' => $dados['usuario_id'],
            ':valor_kwanza' => $dados['valor_kwanza'],
            ':plano_comprado' => $dados['plano_comprado'],
            ':tipo' => $dados['tipo'] ?? 'mensal',
            ':metodo_pagamento' => $dados['metodo_pagamento'],
            ':titular' => $dados['titular'],
            ':referencia' => $dados['referencia'],
            ':status' => $dados['status'] ?? 'pendente'
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    public function markApproved($payment_id) {
        $sql = "UPDATE payments
                SET status = 'aprovado', data_confirmacao = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $payment_id]);
    }

    public function findById($payment_id) {
        $sql = "SELECT * FROM payments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $payment_id]);
        return $stmt->fetch();
    }

    public function findByReference($referencia) {
        $sql = "SELECT * FROM payments WHERE referencia_multicaixa = :referencia";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':referencia' => $referencia]);
        return $stmt->fetch();
    }
}
?>
