<?php
// app/models/Template.php
class Template {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        $sql = "SELECT * FROM templates WHERE ativo = 1 ORDER BY ordem ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getByPlan($plano) {
        $planos = ['gratuito' => 1, 'premium' => 2, 'profissional' => 3];
        $nivel = $planos[$plano] ?? 1;
        
        $sql = "SELECT * FROM templates WHERE ativo = 1 
                AND CASE plano_requerido 
                    WHEN 'gratuito' THEN 1 
                    WHEN 'premium' THEN 2 
                    WHEN 'profissional' THEN 3 
                END <= :nivel 
                ORDER BY ordem ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nivel' => $nivel]);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM templates WHERE id = :id AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getPremiumCount() {
        $sql = "SELECT COUNT(*) as total FROM templates WHERE plano_requerido IN ('premium', 'profissional')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getFreeCount() {
        $sql = "SELECT COUNT(*) as total FROM templates WHERE plano_requerido = 'gratuito'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>