<?php
$user = Auth::getUser();
$user_plano = $user ? $user['plano'] : 'gratuito';

$db = (new Database())->getConnection();
$templateModel = new Template($db);
$templates = $templateModel->getAll();

$page_title = 'Templates - Ngola CV';

?>

<div class="templates-container">
    <div class="templates-header">
        <h1><i class="fas fa-layer-group"></i> Templates Profissionais</h1>
        <p>Escolha o design perfeito para o seu currículo</p>
    </div>
    
    <div class="templates-grid">
        <?php foreach ($templates as $template): 
            $is_locked = $template['plano_requerido'] != 'gratuito' && $user_plano != $template['plano_requerido'] && 
                        !($user_plano == 'profissional' && $template['plano_requerido'] == 'premium');
        ?>
            <div class="template-card <?php echo $is_locked ? 'locked' : ''; ?>">
                <div class="template-preview">
                    <i class="fas fa-file-alt fa-5x"></i>
                    <?php if ($is_locked): ?>
                        <div class="lock-overlay">
                            <i class="fas fa-lock fa-3x"></i>
                            <p>Plano <?php echo ucfirst($template['plano_requerido']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="template-info">
                    <h3><?php echo htmlspecialchars($template['nome']); ?></h3>
                    <p><?php echo htmlspecialchars($template['descricao']); ?></p>
                    <div class="template-badge <?php echo $template['plano_requerido']; ?>">
                        <i class="fas <?php echo $template['plano_requerido'] == 'gratuito' ? 'fa-gift' : 'fa-gem'; ?>"></i>
                        <?php echo ucfirst($template['plano_requerido']); ?>
                    </div>
                </div>
                <div class="template-actions">
                    <?php if ($is_locked): ?>
                        <a href="index.php?page=planos" class="btn-upgrade">
                            <i class="fas fa-arrow-up"></i> Fazer Upgrade
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=editor&template=<?php echo $template['id']; ?>" class="btn-use">
                            <i class="fas fa-check"></i> Usar Template
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.templates-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 40px 20px;
}

.templates-header {
    text-align: center;
    margin-bottom: 50px;
}

.templates-header h1 {
    font-size: 36px;
    color: #2c3e66;
    margin-bottom: 15px;
}

.templates-header p {
    color: #666;
    font-size: 18px;
}

.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}

.template-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}

.template-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.template-card.locked {
    opacity: 0.8;
}

.template-preview {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    color: white;
}

.lock-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
}

.lock-overlay i {
    margin-bottom: 10px;
}

.template-info {
    padding: 20px;
}

.template-info h3 {
    font-size: 20px;
    color: #2c3e66;
    margin-bottom: 10px;
}

.template-info p {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
}

.template-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.template-badge.gratuito {
    background: #27ae60;
    color: white;
}

.template-badge.premium {
    background: #e67e22;
    color: white;
}

.template-badge.profissional {
    background: #8e44ad;
    color: white;
}

.template-actions {
    padding: 15px 20px;
    border-top: 1px solid #eee;
}

.btn-use, .btn-upgrade {
    display: block;
    text-align: center;
    padding: 10px;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-use {
    background: #2c3e66;
    color: white;
}

.btn-use:hover {
    background: #1a2a4a;
}

.btn-upgrade {
    background: #e67e22;
    color: white;
}

.btn-upgrade:hover {
    background: #d35400;
}
</style>

