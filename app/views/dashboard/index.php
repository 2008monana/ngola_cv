<?php
// app/views/dashboard/index.php
$user = Auth::getUser();
$db = (new Database())->getConnection();
require_once __DIR__ . '/../../models/Stats.php';

// Model dedicado para estatísticas do dashboard.
$statsModel = new Stats($db);
$summary = $statsModel->getSummary($user['id'], $user['plano']);
$resumesByMonth = $statsModel->getResumesByMonth($user['id']);
$downloadsByTemplate = $statsModel->getDownloadsByTemplate($user['id']);
$activityLast30Days = $statsModel->getActivityLast30Days($user['id']);
$topResumes = $statsModel->getTopResumes($user['id']);
$recentActivities = $statsModel->getRecentActivities($user['id']);
$page_specific_js = 'charts.js';

$planLabels = [
    'gratuito' => 'Gratuito',
    'premium' => 'Premium',
    'profissional' => 'Profissional'
];

$careerTips = [
    ['icon' => 'fa-bullseye', 'title' => 'Adapte o CV à vaga', 'text' => 'Destaque experiências e competências alinhadas com a função pretendida.'],
    ['icon' => 'fa-chart-line', 'title' => 'Use resultados mensuráveis', 'text' => 'Sempre que possível, inclua números: vendas, prazos, equipas lideradas ou projetos entregues.'],
    ['icon' => 'fa-spell-check', 'title' => 'Revise antes de enviar', 'text' => 'Erros ortográficos reduzem credibilidade. Leia o currículo em voz alta antes de exportar.'],
    ['icon' => 'fa-file-lines', 'title' => 'Mantenha clareza', 'text' => 'Use frases curtas, informação relevante e uma estrutura simples para facilitar a leitura.']
];

$activityIcons = [
    'criou_curriculo' => 'fa-plus-circle',
    'editou_curriculo' => 'fa-pen-to-square',
    'pagamento' => 'fa-credit-card'
];
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <p>Bem-vindo de volta, <?php echo htmlspecialchars($user['nome']); ?>! Acompanhe sua evolução profissional.</p>
        </div>
        <a href="index.php?page=editor" class="btn-primary"><i class="fas fa-plus-circle"></i> Novo Currículo</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['warning']); unset($_SESSION['warning']); ?>
        </div>
    <?php endif; ?>

    <!-- Cards de Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            <div class="stat-info">
                <h3><?php echo (int)$summary['total_cvs']; ?></h3>
                <p>Total de currículos criados</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-download"></i></div>
            <div class="stat-info">
                <h3><?php echo (int)$summary['total_downloads']; ?></h3>
                <p>Total de downloads realizados</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-crown"></i></div>
            <div class="stat-info">
                <h3><?php echo htmlspecialchars($planLabels[$user['plano']] ?? ucfirst($user['plano'])); ?></h3>
                <p>Plano atual do usuário</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="stat-info">
                <h3><?php echo (int)$summary['templates_disponiveis']; ?></h3>
                <p>Templates disponíveis para o plano</p>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
        <section class="dashboard-card chart-card chart-card-wide">
            <div class="card-header">
                <h2><i class="fas fa-chart-column"></i> Currículos criados por mês</h2>
                <span>Últimos 6 meses</span>
            </div>
            <div class="chart-wrapper"><canvas id="resumesByMonthChart"></canvas></div>
        </section>

        <section class="dashboard-card chart-card">
            <div class="card-header">
                <h2><i class="fas fa-chart-pie"></i> Downloads por template</h2>
            </div>
            <div class="chart-wrapper"><canvas id="downloadsByTemplateChart"></canvas></div>
        </section>

        <section class="dashboard-card chart-card chart-card-wide">
            <div class="card-header">
                <h2><i class="fas fa-chart-line"></i> Atividade nos últimos 30 dias</h2>
                <span>Criações e edições</span>
            </div>
            <div class="chart-wrapper"><canvas id="activityLast30DaysChart"></canvas></div>
        </section>
    </div>

    <div class="dashboard-grid">
        <!-- Dicas de carreira -->
        <section class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-lightbulb"></i> Dicas de Carreira</h2>
            </div>
            <div class="tips-list">
                <?php foreach ($careerTips as $tip): ?>
                    <article class="tip-item">
                        <i class="fas <?php echo htmlspecialchars($tip['icon']); ?>"></i>
                        <div>
                            <h3><?php echo htmlspecialchars($tip['title']); ?></h3>
                            <p><?php echo htmlspecialchars($tip['text']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Currículos mais acessados -->
        <section class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-ranking-star"></i> Currículos Mais Acessados</h2>
                <span>Top 3</span>
            </div>
            <?php if (!empty($topResumes)): ?>
                <div class="top-resumes-list">
                    <?php foreach ($topResumes as $index => $resume): ?>
                        <article class="top-resume-item">
                            <div class="rank-badge"><?php echo $index + 1; ?></div>
                            <div class="top-resume-info">
                                <h3><?php echo htmlspecialchars($resume['titulo']); ?></h3>
                                <p><?php echo htmlspecialchars($resume['template_nome']); ?></p>
                                <div class="resume-metrics">
                                    <span><i class="fas fa-eye"></i> <?php echo (int)$resume['visualizacoes']; ?></span>
                                    <span><i class="fas fa-download"></i> <?php echo (int)$resume['downloads']; ?></span>
                                </div>
                            </div>
                            <a href="index.php?page=editor&id=<?php echo (int)$resume['id']; ?>" class="btn-sm"><i class="fas fa-edit"></i></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-file-circle-question"></i>
                    <p>Ainda não existem currículos para destacar.</p>
                    <a href="index.php?page=editor" class="btn-secondary">Criar Currículo</a>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="dashboard-grid">
        <!-- Últimas atividades -->
        <section class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-clock-rotate-left"></i> Últimas Atividades</h2>
            </div>
            <?php if (!empty($recentActivities)): ?>
                <div class="activity-list">
                    <?php foreach ($recentActivities as $activity): ?>
                        <article class="activity-item">
                            <div class="activity-icon"><i class="fas <?php echo htmlspecialchars($activityIcons[$activity['tipo']] ?? 'fa-circle-info'); ?>"></i></div>
                            <div>
                                <h3><?php echo htmlspecialchars($activity['detalhe']); ?></h3>
                                <p><?php echo htmlspecialchars($activity['titulo']); ?></p>
                                <small><?php echo date('d/m/Y H:i', strtotime($activity['data_evento'])); ?></small>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clock"></i>
                    <p>Nenhuma atividade registada ainda.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Ações rápidas -->
        <section class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-bolt"></i> Ações Rápidas</h2>
            </div>
            <div class="actions-grid compact-actions">
                <a href="index.php?page=editor" class="action-card">
                    <i class="fas fa-plus-circle fa-2x"></i>
                    <h3>Criar Novo Currículo</h3>
                </a>
                <a href="index.php?page=meus-curriculos" class="action-card">
                    <i class="fas fa-edit fa-2x"></i>
                    <h3>Gerenciar Currículos</h3>
                </a>
                <a href="index.php?page=templates" class="action-card">
                    <i class="fas fa-layer-group fa-2x"></i>
                    <h3>Ver Templates</h3>
                </a>
                <?php if ($user['plano'] === 'gratuito'): ?>
                    <a href="index.php?page=planos" class="action-card premium">
                        <i class="fas fa-gem fa-2x"></i>
                        <h3>Fazer Upgrade</h3>
                    </a>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script>
window.ngolaDashboardCharts = <?php echo json_encode([
    'resumesByMonth' => $resumesByMonth,
    'downloadsByTemplate' => $downloadsByTemplate,
    'activityLast30Days' => $activityLast30Days
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>

<style>
.dashboard-container { max-width: 1280px; margin: 0 auto; padding: 40px 20px; }
.dashboard-header { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 34px; }
.dashboard-header h1 { font-size: 32px; color: #2c3e66; margin-bottom: 10px; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px; }
.stat-card, .dashboard-card { background: white; border-radius: 16px; box-shadow: 0 8px 28px rgba(0,0,0,0.06); border: 1px solid #edf0f4; }
.stat-card { padding: 22px; display: flex; align-items: center; gap: 18px; }
.stat-icon { width: 58px; height: 58px; border-radius: 16px; background: #fff3e8; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-icon i { font-size: 28px; color: #e67e22; }
.stat-info h3 { font-size: 28px; font-weight: 800; color: #2c3e66; }
.stat-info p { color: #6c757d; font-size: 14px; }
.charts-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; margin-bottom: 24px; }
.chart-card-wide { grid-column: span 1; }
.dashboard-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; margin-bottom: 24px; }
.dashboard-card { padding: 24px; }
.card-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 20px; }
.card-header h2 { color: #2c3e66; font-size: 20px; }
.card-header span { color: #6c757d; font-size: 13px; }
.chart-wrapper { min-height: 300px; position: relative; }
.tips-list, .activity-list, .top-resumes-list { display: grid; gap: 14px; }
.tip-item, .activity-item, .top-resume-item { display: flex; gap: 14px; align-items: flex-start; padding: 14px; background: #f8f9fa; border-radius: 12px; }
.tip-item > i, .activity-icon { width: 40px; height: 40px; border-radius: 12px; background: #2c3e66; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.tip-item h3, .activity-item h3, .top-resume-info h3 { color: #2c3e66; font-size: 16px; margin-bottom: 4px; }
.tip-item p, .activity-item p, .top-resume-info p { color: #6c757d; font-size: 14px; }
.activity-item small { color: #999; }
.rank-badge { width: 38px; height: 38px; border-radius: 50%; background: #e67e22; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0; }
.top-resume-info { flex: 1; }
.resume-metrics { display: flex; gap: 12px; margin-top: 8px; color: #555; font-size: 13px; }
.actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; }
.action-card { background: #f8f9fa; border-radius: 12px; padding: 20px; text-align: center; text-decoration: none; color: #333; transition: transform 0.3s, box-shadow 0.3s; border: 1px solid #e0e0e0; }
.action-card:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,0.09); }
.action-card i { color: #e67e22; margin-bottom: 10px; }
.action-card h3 { font-size: 15px; color: #2c3e66; }
.action-card.premium { background: linear-gradient(135deg, #fff5e6, #ffe0cc); }
.btn-sm { padding: 8px 12px; border-radius: 8px; text-decoration: none; background: #eef2f7; color: #2c3e66; display: inline-flex; align-items: center; justify-content: center; }
.empty-state { text-align: center; padding: 34px 20px; color: #6c757d; }
.empty-state i { font-size: 42px; color: #e67e22; margin-bottom: 12px; }
.empty-state p { margin-bottom: 16px; }
.alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
@media (max-width: 980px) { .dashboard-header, .charts-grid, .dashboard-grid { grid-template-columns: 1fr; } .dashboard-header { align-items: flex-start; flex-direction: column; } }
@media (max-width: 640px) { .stat-card, .tip-item, .activity-item, .top-resume-item { flex-direction: column; } .chart-wrapper { min-height: 260px; } }
</style>
