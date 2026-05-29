<?php
// app/models/User.php
class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($dados) {
        $sql = "INSERT INTO users (nome_completo, email, senha_hash) VALUES (:nome, :email, :senha)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome' => $dados['nome_completo'],
            ':email' => $dados['email'],
            ':senha' => $dados['senha_hash']
        ]);
    }
    
    public function emailExists($email) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ? true : false;
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email AND ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function updatePlano($user_id, $plano, $data_expiracao = null) {
        $sql = "UPDATE users SET plano = :plano, data_expiracao_plano = :expiracao WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':plano' => $plano,
            ':expiracao' => $data_expiracao,
            ':id' => $user_id
        ]);
    }
    
    public function updateLastLogin($user_id) {
        $sql = "UPDATE users SET ultimo_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $user_id]);
    }
    
    public function getStats($user_id) {
        $sql = "SELECT COUNT(*) as total_cvs FROM resumes WHERE usuario_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $user_id]);
        $total_cvs = $stmt->fetch()['total_cvs'] ?? 0;
        
        $sql = "SELECT COALESCE(SUM(downloads), 0) as total_downloads FROM resumes WHERE usuario_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $user_id]);
        $total_downloads = $stmt->fetch()['total_downloads'] ?? 0;
        
        return [
            'total_cvs' => $total_cvs,
            'total_downloads' => $total_downloads
        ];
    }
}
?>