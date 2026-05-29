<?php
$user = Auth::getUser();
$current_plan = $user ? $user['plano'] : 'gratuito';

$page_title = 'Planos - Ngola CV';

?>

<div class="plans-container">
    <div class="plans-header">
        <h1><i class="fas fa-tags"></i> Escolha o Plano Ideal</h1>
        <p>Comece grátis e faça upgrade quando precisar de mais recursos</p>
    </div>
    
    <div class="pricing-grid">
        <!-- Plano Grátis -->
        <div class="pricing-card <?php echo $current_plan == 'gratuito' ? 'active' : ''; ?>">
            <div class="pricing-header">
                <h3>Grátis</h3>
                <div class="price">
                    <span class="amount">0 Kz</span>
                    <span class="period">/sempre</span>
                </div>
            </div>
            <div class="pricing-body">
                <ul>
                    <li><i class="fas fa-check"></i> 1 currículo ativo</li>
                    <li><i class="fas fa-check"></i> 2 templates básicos</li>
                    <li><i class="fas fa-check"></i> Exportar PDF com marca d'água</li>
                    <li><i class="fas fa-check"></i> Pré-visualização em tempo real</li>
                    <li><i class="fas fa-times"></i> Sem marca d'água</li>
                    <li><i class="fas fa-times"></i> Templates premium</li>
                    <li><i class="fas fa-times"></i> Suporte prioritário</li>
                </ul>
            </div>
            <div class="pricing-footer">
                <?php if ($current_plan == 'gratuito'): ?>
                    <button class="btn-current" disabled><i class="fas fa-check"></i> Plano Atual</button>
                <?php else: ?>
                    <a href="index.php?page=dashboard" class="btn-outline">Voltar ao Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Plano Premium -->
        <div class="pricing-card featured <?php echo $current_plan == 'premium' ? 'active' : ''; ?>">
            <div class="popular-badge">Mais Popular</div>
            <div class="pricing-header">
                <h3>Premium</h3>
                <div class="price">
                    <span class="amount">2.500 Kz</span>
                    <span class="period">/mês</span>
                </div>
                <div class="or-price">ou 15.000 Kz/ano</div>
            </div>
            <div class="pricing-body">
                <ul>
                    <li><i class="fas fa-check"></i> 5 currículos ativos</li>
                    <li><i class="fas fa-check"></i> 4 templates completos</li>
                    <li><i class="fas fa-check"></i> Exportar PDF sem marca d'água</li>
                    <li><i class="fas fa-check"></i> Pré-visualização em tempo real</li>
                    <li><i class="fas fa-check"></i> Remover logo Ngola CV</li>
                    <li><i class="fas fa-check"></i> Estatísticas básicas</li>
                    <li><i class="fas fa-times"></i> Suporte prioritário</li>
                </ul>
            </div>
            <div class="pricing-footer">
                <?php if ($current_plan == 'premium'): ?>
                    <button class="btn-current" disabled><i class="fas fa-check"></i> Plano Atual</button>
                <?php elseif ($current_plan == 'profissional'): ?>
                    <button class="btn-current" disabled><i class="fas fa-check"></i> Plano Superior</button>
                <?php else: ?>
                    <a href="index.php?page=checkout&plano=premium" class="btn-primary">Assinar Premium</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Plano Profissional -->
        <div class="pricing-card <?php echo $current_plan == 'profissional' ? 'active' : ''; ?>">
            <div class="pricing-header">
                <h3>Profissional</h3>
                <div class="price">
                    <span class="amount">5.000 Kz</span>
                    <span class="period">/mês</span>
                </div>
                <div class="or-price">ou 30.000 Kz/ano</div>
            </div>
            <div class="pricing-body">
                <ul>
                    <li><i class="fas fa-check"></i> Currículos ilimitados</li>
                    <li><i class="fas fa-check"></i> 4 templates completos</li>
                    <li><i class="fas fa-check"></i> Exportar PDF sem marca d'água</li>
                    <li><i class="fas fa-check"></i> Pré-visualização em tempo real</li>
                    <li><i class="fas fa-check"></i> Remover logo Ngola CV</li>
                    <li><i class="fas fa-check"></i> Estatísticas completas</li>
                    <li><i class="fas fa-check"></i> Suporte prioritário 24/7</li>
                </ul>
            </div>
            <div class="pricing-footer">
                <?php if ($current_plan == 'profissional'): ?>
                    <button class="btn-current" disabled><i class="fas fa-check"></i> Plano Atual</button>
                <?php else: ?>
                    <a href="index.php?page=checkout&plano=profissional" class="btn-primary">Assinar Profissional</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="payment-methods">
        <h3><i class="fas fa-credit-card"></i> Métodos de Pagamento Aceitos</h3>
        <div class="methods">
            <span class="method"><i class="fas fa-mobile-alt"></i> Multicaixa Express</span>
            <span class="method"><i class="fas fa-university"></i> Transferência Bancária</span>
            <span class="method"><i class="fas fa-credit-card"></i> Cartão de Crédito</span>
        </div>
    </div>
    
    <div class="faq-section">
        <h3><i class="fas fa-question-circle"></i> Perguntas Frequentes</h3>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>Posso cancelar a qualquer momento?</h4>
                <p>Sim! Você pode cancelar sua assinatura a qualquer momento sem multa.</p>
            </div>
            <div class="faq-item">
                <h4>Como funciona o pagamento?</h4>
                <p>Aceitamos Multicaixa Express, transferência bancária e cartão de crédito.</p>
            </div>
            <div class="faq-item">
                <h4>O que acontece se eu cancelar?</h4>
                <p>Seus currículos continuam disponíveis, mas você perde acesso a recursos premium.</p>
            </div>
        </div>
    </div>
</div>

<style>
.plans-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 40px 20px;
}

.plans-header {
    text-align: center;
    margin-bottom: 50px;
}

.plans-header h1 {
    font-size: 36px;
    color: #2c3e66;
    margin-bottom: 15px;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.pricing-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s;
    position: relative;
}

.pricing-card:hover {
    transform: translateY(-5px);
}

.pricing-card.featured {
    border: 2px solid #e67e22;
    transform: scale(1.02);
}

.pricing-card.featured:hover {
    transform: scale(1.02) translateY(-5px);
}

.popular-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: #e67e22;
    color: white;
    padding: 8px 20px;
    font-size: 12px;
    font-weight: bold;
    border-bottom-left-radius: 12px;
}

.pricing-header {
    background: linear-gradient(135deg, #2c3e66, #1a2a4a);
    color: white;
    padding: 30px;
    text-align: center;
}

.pricing-header h3 {
    font-size: 24px;
    margin-bottom: 15px;
}

.price .amount {
    font-size: 36px;
    font-weight: bold;
}

.price .period {
    font-size: 14px;
    opacity: 0.8;
}

.or-price {
    font-size: 12px;
    opacity: 0.8;
    margin-top: 5px;
}

.pricing-body {
    padding: 30px;
}

.pricing-body ul {
    list-style: none;
}

.pricing-body li {
    margin-bottom: 12px;
    color: #555;
}

.pricing-body li i {
    width: 25px;
    margin-right: 10px;
}

.pricing-body li .fa-check {
    color: #27ae60;
}

.pricing-body li .fa-times {
    color: #e74c3c;
}

.pricing-footer {
    padding: 20px 30px 30px;
    text-align: center;
}

.btn-current {
    background: #27ae60;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    width: 100%;
    cursor: default;
}

.btn-outline {
    border: 2px solid #2c3e66;
    padding: 10px 24px;
    border-radius: 8px;
    text-decoration: none;
    color: #2c3e66;
    display: inline-block;
}

.payment-methods {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 40px;
}

.methods {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.method {
    background: white;
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.faq-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.faq-item h4 {
    color: #2c3e66;
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .pricing-grid {
        grid-template-columns: 1fr;
    }
    
    .pricing-card.featured {
        transform: scale(1);
    }
}
</style>

