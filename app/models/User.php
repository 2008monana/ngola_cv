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
    

    public function updateProfile($user_id, $dados) {
        $sql = "UPDATE users
                SET nome_completo = :nome_completo,
                    telefone = :telefone,
                    endereco = :endereco,
                    avatar_url = :avatar_url
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome_completo' => $dados['nome_completo'],
            ':telefone' => $dados['telefone'] ?: null,
            ':endereco' => $dados['endereco'] ?: null,
            ':avatar_url' => $dados['avatar_url'] ?: null,
            ':id' => $user_id
        ]);
    }

    public function updatePassword($user_id, $senha_hash) {
        $sql = "UPDATE users SET senha_hash = :senha_hash WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':senha_hash' => $senha_hash,
            ':id' => $user_id
        ]);
    }

    public function deleteAccount($user_id) {
        $this->db->beginTransaction();
        try {
            // Remover dados dependentes antes do usuário para respeitar as chaves estrangeiras existentes.
            $stmt = $this->db->prepare("DELETE FROM payments WHERE usuario_id = :id");
            $stmt->execute([':id' => $user_id]);

            $stmt = $this->db->prepare("DELETE FROM notifications WHERE usuario_id = :id");
            $stmt->execute([':id' => $user_id]);

            $stmt = $this->db->prepare("DELETE FROM resumes WHERE usuario_id = :id");
            $stmt->execute([':id' => $user_id]);

            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $result = $stmt->execute([':id' => $user_id]);

            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
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