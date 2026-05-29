<?php $page_title = 'Admin - Dashboard'; include __DIR__ . '/_nav.php'; ?>
<div class="admin-container">
    <h1><i class="fas fa-shield-halved"></i> Dashboard Administrativo</h1>
    <div class="admin-stats-grid">
        <div class="admin-card"><i class="fas fa-users"></i><strong><?php echo (int)$stats['total_usuarios']; ?></strong><span>Usuários</span></div>
        <div class="admin-card"><i class="fas fa-file-alt"></i><strong><?php echo (int)$stats['total_curriculos']; ?></strong><span>Currículos</span></div>
        <div class="admin-card"><i class="fas fa-coins"></i><strong><?php echo number_format((float)$stats['receita_total'], 2, ',', '.'); ?> Kz</strong><span>Receita</span></div>
        <div class="admin-card"><i class="fas fa-hourglass-half"></i><strong><?php echo (int)$stats['pagamentos_pendentes']; ?></strong><span>Pagamentos pendentes</span></div>
    </div>
    <div class="admin-grid">
        <section class="admin-panel"><h2>Novos usuários por mês</h2><div class="admin-chart"><canvas id="adminUsersChart"></canvas></div></section>
        <section class="admin-panel"><h2>Receita mensal</h2><div class="admin-chart"><canvas id="adminRevenueChart"></canvas></div></section>
    </div>
</div>
<script>
window.adminCharts = <?php echo json_encode(['users' => $newUsersChart, 'revenue' => $revenueChart], JSON_UNESCAPED_UNICODE); ?>;
document.addEventListener('DOMContentLoaded', function() {
    if (!window.Chart || !window.adminCharts) return;
    new Chart(document.getElementById('adminUsersChart'), { type: 'bar', data: { labels: adminCharts.users.labels, datasets: [{ data: adminCharts.users.values, backgroundColor: '#e67e22', label: 'Usuários' }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } } });
    new Chart(document.getElementById('adminRevenueChart'), { type: 'line', data: { labels: adminCharts.revenue.labels, datasets: [{ data: adminCharts.revenue.values, borderColor: '#2c3e66', backgroundColor: 'rgba(44,62,102,.12)', fill: true, label: 'Receita' }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } } });
});
</script>
<style>
.admin-container{max-width:1280px;margin:0 auto;padding:34px 20px}.admin-container h1{color:#2c3e66;margin-bottom:22px}.admin-tabs{max-width:1280px;margin:24px auto 0;padding:0 20px;display:flex;gap:10px;flex-wrap:wrap}.admin-tabs a{background:#fff;border:1px solid #e5e7eb;color:#2c3e66;text-decoration:none;padding:10px 14px;border-radius:10px}.admin-stats-grid,.admin-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;margin-bottom:22px}.admin-card,.admin-panel{background:#fff;border-radius:16px;padding:22px;box-shadow:0 8px 28px rgba(0,0,0,.06);border:1px solid #edf0f4}.admin-card{display:grid;gap:6px}.admin-card i{font-size:28px;color:#e67e22}.admin-card strong{font-size:26px;color:#2c3e66}.admin-card span{color:#6b7280}.admin-panel h2{color:#2c3e66;margin-bottom:14px}.admin-chart{height:320px}
</style>
