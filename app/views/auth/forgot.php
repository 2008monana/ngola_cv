<?php
// app/views/auth/forgot.php
$page_title = 'Recuperar Senha - Ngola CV';
include __DIR__ . '/../partials/header.php';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$warning = $_SESSION['warning'] ?? '';
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['warning']);
?>

<div class="auth-container">
    <div class="auth-box" style="max-width: 450px;">
        <div class="auth-header">
            <i class="fas fa-key fa-3x"></i>
            <h2>Esqueceu sua senha?</h2>
            <p>Digite seu e-mail e enviaremos um link para redefinir sua senha</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($warning): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($warning); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?page=esqueci-senha" class="auth-form">
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> E-mail
                </label>
                <input type="email" id="email" name="email" required 
                       placeholder="seu@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <button type="submit" class="btn-primary btn-block">
                <i class="fas fa-paper-plane"></i> Enviar Link de Recuperação
            </button>
            
            <p class="auth-footer">
                <a href="index.php?page=login">
                    <i class="fas fa-arrow-left"></i> Voltar para o login
                </a>
            </p>
        </form>
    </div>
</div>

<style>
.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>