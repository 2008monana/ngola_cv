<?php
// app/views/404.php
?>
<div class="error-container">
    <div class="error-content">
        <i class="fas fa-search fa-5x"></i>
        <h1>404</h1>
        <h2>Página não encontrada</h2>
        <p>A página que você está procurando não existe ou foi movida.</p>
        <a href="index.php" class="btn-primary">
            <i class="fas fa-home"></i> Voltar para o Início
        </a>
    </div>
</div>

<style>
.error-container {
    min-height: calc(100vh - 300px);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 40px 20px;
}

.error-content i {
    color: #e67e22;
    margin-bottom: 20px;
}

.error-content h1 {
    font-size: 80px;
    color: #2c3e66;
    margin-bottom: 10px;
}

.error-content h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 15px;
}

.error-content p {
    color: #666;
    margin-bottom: 30px;
}
</style>