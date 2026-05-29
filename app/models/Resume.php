<?php
// app/models/Resume.php
class Resume {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($dados) {
        $sql = "INSERT INTO resumes (usuario_id, template_id, titulo, dados_json) 
                VALUES (:usuario_id, :template_id, :titulo, :dados_json)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $dados['usuario_id'],
            ':template_id' => $dados['template_id'],
            ':titulo' => $dados['titulo'],
            ':dados_json' => $dados['dados_json']
        ]);
    }
    
    public function update($id, $dados) {
        $sql = "UPDATE resumes SET template_id = :template_id, titulo = :titulo, 
                dados_json = :dados_json, ultima_versao = NOW() 
                WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $dados['usuario_id'],
            ':template_id' => $dados['template_id'],
            ':titulo' => $dados['titulo'],
            ':dados_json' => $dados['dados_json']
        ]);
    }
    
    public function delete($id, $usuario_id) {
        $sql = "DELETE FROM resumes WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':usuario_id' => $usuario_id]);
    }
    
    public function findById($id, $usuario_id) {
    $sql = "SELECT r.*, t.nome as template_nome, t.slug as template_slug 
            FROM resumes r 
            JOIN templates t ON r.template_id = t.id 
            WHERE r.id = :id AND r.usuario_id = :usuario_id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id, ':usuario_id' => $usuario_id]);
    return $stmt->fetch();
    }
    
    public function getByUser($usuario_id) {
        $sql = "SELECT r.*, t.nome as template_nome 
                FROM resumes r 
                JOIN templates t ON r.template_id = t.id 
                WHERE r.usuario_id = :usuario_id 
                ORDER BY r.ultima_versao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }
    
    public function getRecentByUser($usuario_id, $limit = 5) {
        $sql = "SELECT r.*, t.nome as template_nome 
                FROM resumes r 
                JOIN templates t ON r.template_id = t.id 
                WHERE r.usuario_id = :usuario_id 
                ORDER BY r.ultima_versao DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function duplicate($id, $usuario_id) {
        $original = $this->findById($id, $usuario_id);
        if (!$original) return false;
        
        $dados = [
            'usuario_id' => $usuario_id,
            'template_id' => $original['template_id'],
            'titulo' => $original['titulo'] . ' (Cópia)',
            'dados_json' => $original['dados_json']
        ];
        
        return $this->create($dados);
    }
    
    public function incrementDownload($id) {
        $sql = "UPDATE resumes SET downloads = downloads + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getStats($usuario_id) {
        $sql = "SELECT COUNT(*) as total_cvs, COALESCE(SUM(downloads), 0) as total_downloads 
                FROM resumes WHERE usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        $result = $stmt->fetch();
        
        return [
            'total_cvs' => $result['total_cvs'] ?? 0,
            'total_downloads' => $result['total_downloads'] ?? 0
        ];
    }
}
?>