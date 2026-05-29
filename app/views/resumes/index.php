<?php
$user = Auth::getUser();
$db = (new Database())->getConnection();
$resumeModel = new Resume($db);
$resumes = $resumeModel->getByUser($user['id']);

$page_title = 'Meus Currículos - Ngola CV';

?>

<div class="resumes-container">
    <div class="resumes-header">
        <h1><i class="fas fa-file-alt"></i> Meus Currículos</h1>
        <a href="index.php?page=editor" class="btn-primary">
            <i class="fas fa-plus"></i> Criar Novo Currículo
        </a>
    </div>
    
    <?php if (empty($resumes)): ?>
        <div class="empty-state">
            <i class="fas fa-file-alt fa-5x"></i>
            <h3>Nenhum currículo criado ainda</h3>
            <p>Comece agora mesmo criando seu primeiro currículo profissional</p>
            <a href="index.php?page=editor" class="btn-primary">
                <i class="fas fa-plus"></i> Criar Primeiro Currículo
            </a>
        </div>
    <?php else: ?>
        <div class="resumes-grid">
            <?php foreach ($resumes as $resume): ?>
                <div class="resume-card">
                    <div class="resume-card-header">
                        <i class="fas fa-file-pdf"></i>
                        <h3><?php echo htmlspecialchars($resume['titulo']); ?></h3>
                    </div>
                    <div class="resume-card-body">
                        <p>
                            <i class="fas fa-layer-group"></i> 
                            Template: <?php echo htmlspecialchars($resume['template_nome']); ?>
                        </p>
                        <p>
                            <i class="fas fa-calendar-alt"></i> 
                            Atualizado: <?php echo date('d/m/Y', strtotime($resume['ultima_versao'])); ?>
                        </p>
                        <p>
                            <i class="fas fa-download"></i> 
                            Downloads: <?php echo $resume['downloads']; ?>
                        </p>
                    </div>
                    <div class="resume-card-actions">
                        <a href="index.php?page=editor&id=<?php echo $resume['id']; ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="index.php?page=duplicar-curriculo&id=<?php echo $resume['id']; ?>" 
                           class="btn-duplicate" onclick="return confirm('Duplicar este currículo?')">
                            <i class="fas fa-copy"></i> Duplicar
                        </a>
                        <a href="index.php?page=exportar-pdf&id=<?php echo $resume['id']; ?>" class="btn-pdf">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a href="index.php?page=excluir-curriculo&id=<?php echo $resume['id']; ?>" 
                           class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este currículo?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.resumes-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 40px 20px;
}

.resumes-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 20px;
}

.resumes-header h1 {
    font-size: 32px;
    color: #2c3e66;
}

.resumes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
}

.resume-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.3s, box-shadow 0.3s;
}

.resume-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.resume-card-header {
    background: linear-gradient(135deg, #2c3e66, #1a2a4a);
    color: white;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.resume-card-header i {
    font-size: 32px;
}

.resume-card-header h3 {
    font-size: 18px;
    margin: 0;
}

.resume-card-body {
    padding: 20px;
}

.resume-card-body p {
    margin-bottom: 10px;
    color: #555;
}

.resume-card-body i {
    width: 25px;
    color: #e67e22;
}

.resume-card-actions {
    display: flex;
    gap: 8px;
    padding: 15px 20px;
    border-top: 1px solid #eee;
    background: #fafafa;
}

.resume-card-actions a {
    flex: 1;
    text-align: center;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-edit {
    background: #2c3e66;
    color: white;
}

.btn-edit:hover {
    background: #1a2a4a;
}

.btn-duplicate {
    background: #27ae60;
    color: white;
}

.btn-duplicate:hover {
    background: #219a52;
}

.btn-pdf {
    background: #e74c3c;
    color: white;
}

.btn-pdf:hover {
    background: #c0392b;
}

.btn-delete {
    background: #e74c3c;
    color: white;
    width: 40px;
    flex: none;
}

.btn-delete:hover {
    background: #c0392b;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 12px;
}

.empty-state i {
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 24px;
    color: #333;
    margin-bottom: 10px;
}

.empty-state p {
    color: #666;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .resumes-header {
        flex-direction: column;
        text-align: center;
    }
    
    .resumes-grid {
        grid-template-columns: 1fr;
    }
}
</style>

