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
}
?>
