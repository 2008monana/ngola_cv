<?php
// app/views/plans/checkout.php - Checkout de pagamento simulado
$page_title = 'Checkout - Ngola CV';
$user = Auth::getUser();
$errors = $_SESSION['errors'] ?? [];
$error = $_SESSION['error'] ?? '';
unset($_SESSION['errors'], $_SESSION['error']);

$methodLabels = [
    'multicaixa_express' => 'Multicaixa Express',
    'cartao' => 'Cartão',
    'transferencia' => 'Transferência Bancária'
];
?>

<div class="checkout-container">
    <div class="checkout-header">
        <h1><i class="fas fa-credit-card"></i> Checkout Simulado</h1>
        <p>Finalize a assinatura do plano <?php echo htmlspecialchars($selectedPlan['nome']); ?> em ambiente de testes.</p>
    </div>

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

    <div class="checkout-grid">
        <section class="checkout-card plan-summary-card">
            <h2><i class="fas fa-file-invoice-dollar"></i> Resumo do Plano</h2>
            <div class="selected-plan">
                <span class="plan-name"><?php echo htmlspecialchars($selectedPlan['nome']); ?></span>
                <span class="plan-price"><?php echo number_format((float)$selectedPlan['valor'], 2, ',', '.'); ?> Kz</span>
                <span class="plan-period">30 dias de acesso</span>
            </div>
            <p><?php echo htmlspecialchars($selectedPlan['descricao']); ?></p>
            <ul class="checkout-benefits">
                <li><i class="fas fa-check"></i> Pagamento aprovado automaticamente no simulador</li>
                <li><i class="fas fa-check"></i> Plano ativado imediatamente após confirmação</li>
                <li><i class="fas fa-check"></i> Registro no histórico de pagamentos</li>
            </ul>
        </section>

        <section class="checkout-card">
            <h2><i class="fas fa-lock"></i> Dados de Pagamento</h2>
            <form action="index.php?page=processar-pagamento" method="POST" class="checkout-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <input type="hidden" name="plano" value="<?php echo htmlspecialchars($selectedPlanKey); ?>">

                <div class="form-group">
                    <label for="titular">Nome no cartão ou titular</label>
                    <input type="text" id="titular" name="titular" required minlength="3" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="metodo_pagamento">Método de pagamento</label>
                    <select id="metodo_pagamento" name="metodo_pagamento" required>
                        <?php foreach ($methodLabels as $key => $label): ?>
                            <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="simulator-notice">
                    <i class="fas fa-vial"></i>
                    <div>
                        <strong>Ambiente de teste</strong>
                        <p>Esta API fake aprova sempre o pagamento e ativa o plano por 30 dias.</p>
                    </div>
                </div>

                <button type="submit" class="btn-primary checkout-submit">
                    <i class="fas fa-check-circle"></i> Pagar <?php echo number_format((float)$selectedPlan['valor'], 2, ',', '.'); ?> Kz
                </button>
                <a href="index.php?page=planos" class="btn-outline"><i class="fas fa-arrow-left"></i> Voltar aos planos</a>
            </form>
        </section>
    </div>
</div>

<style>
.checkout-container { max-width: 1100px; margin: 0 auto; padding: 40px 20px; }
.checkout-header { text-align: center; margin-bottom: 34px; }
.checkout-header h1 { color: #2c3e66; font-size: 34px; margin-bottom: 8px; }
.checkout-grid { display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 24px; align-items: start; }
.checkout-card { background: white; border-radius: 16px; padding: 26px; box-shadow: 0 8px 28px rgba(0,0,0,0.06); border: 1px solid #edf0f4; }
.checkout-card h2 { color: #2c3e66; margin-bottom: 20px; font-size: 22px; }
.selected-plan { display: grid; gap: 8px; padding: 22px; border-radius: 14px; background: linear-gradient(135deg, #2c3e66, #1a2a4a); color: white; margin-bottom: 18px; }
.plan-name { font-size: 22px; font-weight: 800; }
.plan-price { font-size: 34px; font-weight: 800; color: #f6b26b; }
.plan-period { opacity: 0.85; }
.checkout-benefits { list-style: none; margin-top: 18px; }
.checkout-benefits li { margin-bottom: 10px; color: #333; }
.checkout-benefits i { color: #27ae60; margin-right: 8px; }
.checkout-form .form-group { margin-bottom: 16px; }
.checkout-form label { display: block; margin-bottom: 6px; font-weight: 700; color: #333; }
.checkout-form input, .checkout-form select { width: 100%; padding: 12px 14px; border: 1px solid #d9dee8; border-radius: 10px; font: inherit; }
.simulator-notice { display: flex; gap: 14px; background: #fff8ef; border: 1px solid #ffe1bd; border-radius: 12px; padding: 16px; margin: 18px 0; color: #6b4a23; }
.simulator-notice i { color: #e67e22; font-size: 26px; }
.checkout-submit { width: 100%; margin-bottom: 12px; }
.btn-outline { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; text-decoration: none; background: white; color: #2c3e66; border: 2px solid #2c3e66; padding: 10px 20px; border-radius: 8px; }
.alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alert ul { margin-left: 22px; }
@media (max-width: 820px) { .checkout-grid { grid-template-columns: 1fr; } }
</style>
