<?php
// app/views/auth/login.php
$page_title = 'Login - Ngola CV';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <i class="fas fa-sign-in-alt fa-3x"></i>
            <h2>Bem-vindo de volta</h2>
            <p>Faça login para acessar seus currículos</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?page=login" class="auth-form">
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> E-mail
                </label>
                <input type="email" id="email" name="email" required 
                       placeholder="seu@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="senha">
                    <i class="fas fa-lock"></i> Senha
                </label>
                <input type="password" id="senha" name="senha" required>
                <a href="index.php?page=recuperar-senha" class="forgot-password">
                    <i class="fas fa-question-circle"></i> Esqueceu a senha?
                </a>
            </div>
            
            <button type="submit" class="btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
            
            <div class="social-login">
                <p>Ou entre com</p>
                <div class="social-buttons">
                    <a href="#" class="social-btn google">
                        <i class="fab fa-google"></i> Google
                    </a>
                    <a href="#" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                </div>
            </div>
            
            <p class="auth-footer">
                Não tem uma conta? <a href="index.php?page=cadastro">Cadastre-se grátis</a>
            </p>
        </form>
    </div>
</div>

<style>
.auth-container {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.auth-box {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    padding: 40px;
    width: 100%;
    max-width: 450px;
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header i {
    color: #2c3e66;
    margin-bottom: 15px;
}

.auth-header h2 {
    color: #2c3e66;
    margin-bottom: 10px;
    font-size: 28px;
}

.auth-header p {
    color: #666;
    font-size: 14px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
    font-size: 14px;
}

.form-group label i {
    margin-right: 8px;
    color: #e67e22;
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #e67e22;
    box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.1);
}

.forgot-password {
    display: inline-block;
    font-size: 12px;
    margin-top: 8px;
    color: #e67e22;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

.btn-block {
    width: 100%;
    padding: 14px;
    font-size: 16px;
    font-weight: 600;
    margin-top: 10px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: #2c3e66;
    color: white;
}

.btn-primary:hover {
    background: #1a2a4a;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.social-login {
    margin-top: 25px;
    text-align: center;
}

.social-login p {
    color: #999;
    font-size: 12px;
    margin-bottom: 15px;
    position: relative;
}

.social-login p:before,
.social-login p:after {
    content: "";
    position: absolute;
    top: 50%;
    width: 30%;
    height: 1px;
    background: #e0e0e0;
}

.social-login p:before {
    left: 0;
}

.social-login p:after {
    right: 0;
}

.social-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.social-btn {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.social-btn.google {
    background: #fff;
    border: 1px solid #ddd;
    color: #333;
}

.social-btn.google:hover {
    background: #f5f5f5;
    border-color: #ccc;
}

.social-btn.facebook {
    background: #3b5998;
    color: white;
    border: none;
}

.social-btn.facebook:hover {
    background: #344e86;
}

.auth-footer {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    color: #666;
    font-size: 14px;
}

.auth-footer a {
    color: #e67e22;
    text-decoration: none;
    font-weight: 500;
}

.auth-footer a:hover {
    text-decoration: underline;
}

.alert {
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Responsivo */
@media (max-width: 480px) {
    .auth-box {
        padding: 30px 20px;
    }
    
    .auth-header h2 {
        font-size: 24px;
    }
    
    .social-buttons {
        flex-direction: column;
    }
    
    .social-btn {
        padding: 8px;
    }
}
</style>

<script>
// Prevenir envio duplicado do formulário
document.querySelector('.auth-form')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Entrando...';
    }
});

// Limpar mensagens de erro ao digitar
document.querySelectorAll('.form-group input').forEach(input => {
    input.addEventListener('input', function() {
        const alert = document.querySelector('.alert-danger');
        if (alert) {
            alert.style.display = 'none';
        }
    });
});
</script>

