<?php
// app/views/profile/index.php - Perfil do usuário
$page_title = 'Meu Perfil - Ngola CV';

$user = $profileUser ?? [];
$payments = $payments ?? [];
$csrfToken = $csrfToken ?? ($_SESSION['csrf_token'] ?? '');
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['errors'], $_SESSION['success'], $_SESSION['error']);

$planLabels = [
    'gratuito' => 'Gratuito',
    'premium' => 'Premium',
    'profissional' => 'Profissional'
];
$statusLabels = [
    'pendente' => 'Pendente',
    'aprovado' => 'Aprovado',
    'falhou' => 'Falhou',
    'reembolsado' => 'Reembolsado'
];
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php if (!empty($user['avatar_url'])): ?>
                <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Avatar de <?php echo htmlspecialchars($user['nome_completo'] ?? 'usuário'); ?>">
            <?php else: ?>
                <i class="fas fa-user-circle"></i>
            <?php endif; ?>
        </div>
        <div>
            <h1><i class="fas fa-user-edit"></i> Meu Perfil</h1>
            <p>Gerencie seus dados pessoais, segurança, plano e histórico de pagamentos.</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <ul>
                <?php foreach ($errors as $message): ?>
                    <li><?php echo htmlspecialchars($message); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="profile-grid">
        <section class="profile-card">
            <h2><i class="fas fa-id-card"></i> Dados Pessoais</h2>
            <form action="index.php?page=atualizar-perfil" method="POST" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

                <div class="form-group">
                    <label for="nome_completo">Nome completo</label>
                    <input type="text" id="nome_completo" name="nome_completo" required minlength="3" value="<?php echo htmlspecialchars($user['nome_completo'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" maxlength="20" value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>" placeholder="Ex: 923 456 789">
                    </div>
                    <div class="form-group">
                        <label for="avatar_url">Avatar (URL da foto)</label>
                        <input type="url" id="avatar_url" name="avatar_url" value="<?php echo htmlspecialchars($user['avatar_url'] ?? ''); ?>" placeholder="https://exemplo.com/foto.jpg">
                    </div>
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <textarea id="endereco" name="endereco" rows="3" placeholder="Cidade, província, país"><?php echo htmlspecialchars($user['endereco'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Atualizar Perfil</button>
            </form>
        </section>

        <section class="profile-card" id="alterar-senha">
            <h2><i class="fas fa-lock"></i> Alterar Senha</h2>
            <form action="index.php?page=alterar-senha" method="POST" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

                <div class="form-group">
                    <label for="senha_atual">Senha atual</label>
                    <input type="password" id="senha_atual" name="senha_atual" required autocomplete="current-password">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nova_senha">Nova senha</label>
                        <input type="password" id="nova_senha" name="nova_senha" required minlength="6" autocomplete="new-password">
                        <small>Mínimo 6 caracteres, 1 maiúscula e 1 número.</small>
                    </div>
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar nova senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6" autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="btn-secondary"><i class="fas fa-key"></i> Alterar Senha</button>
            </form>
        </section>
    </div>

    <div class="profile-grid profile-grid-secondary">
        <section class="profile-card plan-card">
            <h2><i class="fas fa-crown"></i> Meus Planos</h2>
            <div class="plan-summary">
                <span class="plan-badge plan-<?php echo htmlspecialchars($user['plano'] ?? 'gratuito'); ?>">
                    <?php echo htmlspecialchars($planLabels[$user['plano'] ?? 'gratuito'] ?? 'Gratuito'); ?>
                </span>
                <div>
                    <strong>Expiração:</strong>
                    <?php if (!empty($user['data_expiracao_plano'])): ?>
                        <?php echo date('d/m/Y', strtotime($user['data_expiracao_plano'])); ?>
                    <?php else: ?>
                        Sem expiração definida
                    <?php endif; ?>
                </div>
            </div>
            <p>Faça upgrade para desbloquear mais currículos, templates premium e exportações avançadas.</p>
            <a href="index.php?page=planos" class="btn-primary"><i class="fas fa-arrow-up"></i> Fazer Upgrade</a>
        </section>

        <section class="profile-card" id="excluir-conta">
            <h2><i class="fas fa-user-slash"></i> Excluir Conta</h2>
            <p class="danger-text">Esta ação é permanente. Seus currículos e dados vinculados serão removidos.</p>
            <button type="button" class="btn-danger" id="openDeleteModal"><i class="fas fa-trash-alt"></i> Excluir Conta</button>
        </section>
    </div>

    <section class="profile-card payments-card">
        <h2><i class="fas fa-receipt"></i> Histórico de Pagamentos</h2>
        <?php if (!empty($payments)): ?>
            <div class="table-responsive">
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Plano</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($payment['data_solicitacao'])); ?></td>
                                <td><?php echo number_format((float)$payment['valor_kwanza'], 2, ',', '.'); ?> Kz</td>
                                <td><?php echo htmlspecialchars($planLabels[$payment['plano_comprado']] ?? ucfirst($payment['plano_comprado'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($payment['status']); ?>">
                                        <?php echo htmlspecialchars($statusLabels[$payment['status']] ?? ucfirst($payment['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-credit-card"></i>
                <p>Nenhum pagamento encontrado até o momento.</p>
                <a href="index.php?page=planos" class="btn-secondary">Ver Planos</a>
            </div>
        <?php endif; ?>
    </section>
</div>

<div class="modal-overlay" id="deleteAccountModal" aria-hidden="true">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="deleteAccountTitle">
        <button type="button" class="modal-close" id="closeDeleteModal" aria-label="Fechar modal">&times;</button>
        <h3 id="deleteAccountTitle"><i class="fas fa-triangle-exclamation"></i> Confirmar exclusão da conta</h3>
        <p>Para confirmar, digite <strong>EXCLUIR</strong>. Esta ação não poderá ser desfeita.</p>
        <form action="index.php?page=excluir-conta" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <div class="form-group">
                <label for="confirmacao">Confirmação</label>
                <input type="text" id="confirmacao" name="confirmacao" required placeholder="EXCLUIR">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-outline" id="cancelDeleteModal">Cancelar</button>
                <button type="submit" class="btn-danger"><i class="fas fa-trash-alt"></i> Excluir definitivamente</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteAccountModal');
    const openBtn = document.getElementById('openDeleteModal');
    const closeBtn = document.getElementById('closeDeleteModal');
    const cancelBtn = document.getElementById('cancelDeleteModal');

    function openModal() {
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
});
</script>

<style>
.profile-container { max-width: 1180px; margin: 0 auto; padding: 40px 20px; }
.profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; }
.profile-header h1 { color: #2c3e66; font-size: 34px; margin-bottom: 6px; }
.profile-avatar { width: 92px; height: 92px; border-radius: 50%; background: #eef2f7; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 4px solid #e67e22; flex-shrink: 0; }
.profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
.profile-avatar i { font-size: 64px; color: #2c3e66; }
.profile-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; margin-bottom: 24px; }
.profile-grid-secondary { align-items: stretch; }
.profile-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 8px 28px rgba(0,0,0,0.06); border: 1px solid #edf0f4; }
.profile-card h2 { color: #2c3e66; font-size: 22px; margin-bottom: 20px; }
.profile-form .form-group { margin-bottom: 16px; }
.profile-form label, .modal-box label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; }
.profile-form input, .profile-form textarea, .modal-box input { width: 100%; border: 1px solid #d9dee8; border-radius: 10px; padding: 12px 14px; font: inherit; }
.profile-form textarea { resize: vertical; }
.profile-form small { color: #6c757d; display: block; margin-top: 5px; }
.form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.plan-summary { display: flex; align-items: center; gap: 18px; margin-bottom: 16px; flex-wrap: wrap; }
.plan-badge, .status-badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 999px; font-weight: 700; font-size: 13px; }
.plan-gratuito { background: #eef2f7; color: #2c3e66; }
.plan-premium { background: #fff2df; color: #b85f00; }
.plan-profissional { background: #e8f5ee; color: #147a3d; }
.status-pendente { background: #fff3cd; color: #856404; }
.status-aprovado { background: #d4edda; color: #155724; }
.status-falhou { background: #f8d7da; color: #721c24; }
.status-reembolsado { background: #e2e3e5; color: #383d41; }
.danger-text { color: #8a1f2d; margin-bottom: 18px; }
.btn-danger { background: #dc3545; color: white; border: none; padding: 12px 22px; border-radius: 8px; cursor: pointer; font-size: 16px; }
.btn-danger:hover { background: #bb2d3b; }
.btn-outline { background: white; color: #2c3e66; border: 2px solid #2c3e66; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
.table-responsive { overflow-x: auto; }
.payments-table { width: 100%; border-collapse: collapse; }
.payments-table th, .payments-table td { padding: 14px 12px; border-bottom: 1px solid #eef0f3; text-align: left; }
.payments-table th { color: #2c3e66; background: #f8f9fa; }
.empty-state { text-align: center; padding: 30px; color: #6c757d; }
.empty-state i { font-size: 42px; color: #e67e22; margin-bottom: 12px; }
.empty-state p { margin-bottom: 16px; }
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(17, 24, 39, 0.65); z-index: 3000; align-items: center; justify-content: center; padding: 20px; }
.modal-overlay.active { display: flex; }
.modal-box { background: white; width: min(520px, 100%); border-radius: 16px; padding: 26px; position: relative; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-box h3 { color: #8a1f2d; margin-bottom: 12px; }
.modal-close { position: absolute; top: 12px; right: 16px; border: none; background: transparent; font-size: 28px; cursor: pointer; color: #666; }
.modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px; flex-wrap: wrap; }
.alert ul { margin-left: 22px; }
@media (max-width: 800px) { .profile-grid, .form-row { grid-template-columns: 1fr; } .profile-header { align-items: flex-start; } }
</style>
