<?php
// app/views/dashboard/index.php
$user = Auth::getUser();
$db = (new Database())->getConnection();

// Instanciar modelos
$resumeModel = new Resume($db);
$templateModel = new Template($db);

$recentResumes = $resumeModel->getRecentByUser($user['id'], 5);
$stats = $resumeModel->getStats($user['id']);
$totalTemplates = $templateModel->getAll();
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <p>Bem-vindo de volta, <?php echo htmlspecialchars($user['nome']); ?>!</p>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Cards de Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_cvs']; ?></h3>
                <p>Currículos Criados</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-download"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_downloads']; ?></h3>
                <p>Downloads</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-crown"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo ucfirst($user['plano']); ?></h3>
                <p>Plano Atual</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($totalTemplates); ?></h3>
                <p>Templates Disponíveis</p>
            </div>
        </div>
    </div>
    
    <!-- Ações Rápidas -->
    <div class="quick-actions">
        <h2><i class="fas fa-bolt"></i> Ações Rápidas</h2>
        <div class="actions-grid">
            <a href="index.php?page=editor" class="action-card">
                <i class="fas fa-plus-circle fa-3x"></i>
                <h3>Criar Novo Currículo</h3>
                <p>Comece do zero um currículo profissional</p>
            </a>
            <a href="index.php?page=meus-curriculos" class="action-card">
                <i class="fas fa-edit fa-3x"></i>
                <h3>Gerenciar Currículos</h3>
                <p>Editar, duplicar ou excluir currículos</p>
            </a>
            <a href="index.php?page=templates" class="action-card">
                <i class="fas fa-layer-group fa-3x"></i>
                <h3>Ver Templates</h3>
                <p>Explore novos designs para seu CV</p>
            </a>
            <?php if ($user['plano'] == 'gratuito'): ?>
            <a href="index.php?page=planos" class="action-card premium">
                <i class="fas fa-gem fa-3x"></i>
                <h3>Fazer Upgrade</h3>
                <p>Desbloqueie recursos premium</p>
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Currículos Recentes -->
    <?php if (!empty($recentResumes)): ?>
    <div class="recent-resumes">
        <h2><i class="fas fa-history"></i> Currículos Recentes</h2>
        <div class="resumes-list">
            <?php foreach ($recentResumes as $resume): ?>
            <div class="resume-item">
                <div class="resume-info">
                    <i class="fas fa-file-pdf"></i>
                    <div>
                        <h4><?php echo htmlspecialchars($resume['titulo']); ?></h4>
                        <small>Atualizado em <?php echo date('d/m/Y H:i', strtotime($resume['ultima_versao'])); ?></small>
                    </div>
                </div>
                <div class="resume-actions">
                    <a href="index.php?page=editor&id=<?php echo $resume['id']; ?>" class="btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="index.php?page=api-exportar-pdf&id=<?php echo $resume['id']; ?>" class="btn-sm btn-pdf" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.dashboard-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 40px 20px;
}

.dashboard-header {
    margin-bottom: 40px;
}

.dashboard-header h1 {
    font-size: 32px;
    color: #2c3e66;
    margin-bottom: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.stat-icon i {
    font-size: 48px;
    color: #e67e22;
}

.stat-info h3 {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e66;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.action-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #e0e0e0;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.action-card i {
    color: #e67e22;
    margin-bottom: 15px;
}

.action-card.premium {
    background: linear-gradient(135deg, #fff5e6, #ffe0cc);
}

.recent-resumes {
    margin-top: 40px;
}

.resumes-list {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    margin-top: 20px;
}

.resume-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.resume-item:last-child {
    border-bottom: none;
}

.resume-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.resume-info i {
    font-size: 32px;
    color: #e74c3c;
}

.btn-sm {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    background: #f0f0f0;
    color: #333;
    margin-left: 10px;
}

.btn-pdf {
    background: #e74c3c;
    color: white;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}
</style>