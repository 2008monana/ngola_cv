<?php
// app/views/auth/reset.php
$page_title = 'Redefinir Senha - Ngola CV';
include __DIR__ . '/../partials/header.php';

$token = $_GET['token'] ?? '';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

if (empty($token)) {
    header('Location: index.php?page=esqueci-senha');
    exit();
}
?>

<div class="auth-container">
    <div class="auth-box" style="max-width: 450px;">
        <div class="auth-header">
            <i class="fas fa-lock fa-3x"></i>
            <h2>Criar nova senha</h2>
            <p>Digite sua nova senha abaixo</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?page=redefinir-senha&token=<?php echo urlencode($token); ?>" class="auth-form">
            <div class="form-group">
                <label for="senha">
                    <i class="fas fa-lock"></i> Nova Senha
                </label>
                <input type="password" id="senha" name="senha" required>
                <ul class="password-requirements">
                    <li id="req-length"><i class="fas fa-circle"></i> Mínimo 6 caracteres</li>
                    <li id="req-upper"><i class="fas fa-circle"></i> Pelo menos 1 letra maiúscula</li>
                    <li id="req-number"><i class="fas fa-circle"></i> Pelo menos 1 número</li>
                </ul>
            </div>
            
            <div class="form-group">
                <label for="confirmar_senha">
                    <i class="fas fa-lock"></i> Confirmar Senha
                </label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            
            <button type="submit" class="btn-primary btn-block">
                <i class="fas fa-save"></i> Redefinir Senha
            </button>
            
            <p class="auth-footer">
                <a href="index.php?page=login">
                    <i class="fas fa-arrow-left"></i> Voltar para o login
                </a>
            </p>
        </form>
    </div>
</div>

<script>
const senhaInput = document.getElementById('senha');
const reqLength = document.getElementById('req-length');
const reqUpper = document.getElementById('req-upper');
const reqNumber = document.getElementById('req-number');

senhaInput.addEventListener('input', function() {
    const senha = this.value;
    
    reqLength.className = senha.length >= 6 ? 'valid' : '';
    reqUpper.className = /[A-Z]/.test(senha) ? 'valid' : '';
    reqNumber.className = /[0-9]/.test(senha) ? 'valid' : '';
});
</script>

<style>
.password-requirements {
    list-style: none;
    margin-top: 10px;
    font-size: 12px;
}
.password-requirements li {
    color: #999;
    margin-bottom: 5px;
}
.password-requirements li.valid {
    color: #27ae60;
}
.password-requirements li.valid i {
    color: #27ae60;
}
.password-requirements i {
    font-size: 10px;
    margin-right: 5px;
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>