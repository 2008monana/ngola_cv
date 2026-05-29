<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Seu currículo profissional <span class="highlight">começa aqui</span></h1>
            <p>Crie currículos impressionantes em minutos com os templates da Ngola CV. Pré-visualização em tempo real, exportação para PDF e gestão completa.</p>
            <div class="hero-buttons">
                <a href="index.php?page=cadastro" class="btn-primary"><i class="fas fa-rocket"></i> Começar Grátis</a>
                <a href="index.php?page=templates" class="btn-outline"><i class="fas fa-eye"></i> Ver Templates</a>
            </div>
        </div>
        <div class="hero-image">
            <i class="fas fa-file-alt fa-8x" style="color: #2c3e66;"></i>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features">
    <div class="container">
        <h2 class="section-title"><i class="fas fa-star"></i> Por que escolher a Ngola CV?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-edit fa-3x"></i>
                <h3>Editor Intuitivo</h3>
                <p>Editor simples com pré-visualização em tempo real. Veja as mudanças instantaneamente.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-file-pdf fa-3x"></i>
                <h3>Exportação PDF</h3>
                <p>Baixe seu currículo em PDF com alta qualidade, pronto para enviar a recrutadores.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-layer-group fa-3x"></i>
                <h4>Templates Profissionais</h4>
                <p>Designs modernos e elegantes para todas as áreas profissionais.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-credit-card fa-3x"></i>
                <h4>Pagamento em Kwanza</h4>
                <p>Planos acessíveis com pagamento via Multicaixa Express.</p>
            </div>
        </div>
    </div>
</section>

<!-- Templates Preview -->
<section class="templates-preview">
    <div class="container">
        <h2 class="section-title"><i class="fas fa-layer-group"></i> Templates Disponíveis</h2>
        <div class="templates-grid">
            <div class="template-card">
                <i class="fas fa-file-alt fa-4x"></i>
                <h3>Moderno Azul</h3>
                <p>Limpo e profissional</p>
                <span class="badge-free"><i class="fas fa-gift"></i> Grátis</span>
            </div>
            <div class="template-card">
                <i class="fas fa-scroll fa-4x"></i>
                <h3>Clássico Elegante</h3>
                <p>Tradicional e sofisticado</p>
                <span class="badge-free"><i class="fas fa-gift"></i> Grátis</span>
            </div>
            <div class="template-card premium">
                <i class="fas fa-crown fa-4x"></i>
                <h3>Executivo Premium</h3>
                <p>Para altos executivos</p>
                <span class="badge-premium"><i class="fas fa-gem"></i> Premium</span>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta">
    <div class="container">
        <h2><i class="fas fa-chart-line"></i> Pronto para destacar sua carreira?</h2>
        <p>Junte-se a milhares de angolanos que já melhoraram sua apresentação profissional.</p>
        <a href="index.php?page=cadastro" class="btn-secondary"><i class="fas fa-user-plus"></i> Cadastrar Agora</a>
    </div>
</section>

<style>
.hero {
    padding: 80px 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
.hero .container {
    display: flex;
    align-items: center;
    gap: 50px;
}
.hero-content h1 {
    font-size: 48px;
    margin-bottom: 20px;
}
.hero-content .highlight {
    color: #e67e22;
}
.hero-buttons {
    margin-top: 30px;
    display: flex;
    gap: 20px;
}
.btn-outline {
    border: 2px solid #2c3e66;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    color: #2c3e66;
}
.section-title {
    text-align: center;
    margin-bottom: 50px;
    font-size: 36px;
}
.features-grid, .templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}
.feature-card, .template-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}
.feature-card:hover, .template-card:hover {
    transform: translateY(-5px);
}
.badge-free, .badge-premium {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-top: 10px;
}
.badge-free {
    background: #27ae60;
    color: white;
}
.badge-premium {
    background: #e67e22;
    color: white;
}
.cta {
    background: #2c3e66;
    color: white;
    text-align: center;
    padding: 80px 0;
    margin-top: 60px;
}
</style>