<?php
$page_title = 'Cadastro - Ngola CV';

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';
unset($_SESSION['errors'], $_SESSION['success']);
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <i class="fas fa-user-plus fa-3x"></i>
            <h2>Criar Conta</h2>
            <p>Comece a criar currículos profissionais gratuitamente</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?page=register" class="auth-form">
            <div class="form-group">
                <label for="nome">
                    <i class="fas fa-user"></i> Nome Completo
                </label>
                <input type="text" id="nome" name="nome" required 
                       placeholder="Ex: João Manuel Santos" value="<?php echo $_POST['nome'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> E-mail
                </label>
                <input type="email" id="email" name="email" required 
                       placeholder="seu@email.com" value="<?php echo $_POST['email'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="senha">
                    <i class="fas fa-lock"></i> Senha
                </label>
                <input type="password" id="senha" name="senha" required>
                <small class="password-strength" id="passwordStrength"></small>
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
                <i class="fas fa-user-plus"></i> Cadastrar
            </button>
            
            <p class="auth-footer">
                Já tem uma conta? <a href="index.php?page=login">Faça login</a>
            </p>
        </form>
    </div>
</div>

<script>
// Validação de força da senha em tempo real
const senhaInput = document.getElementById('senha');
const reqLength = document.getElementById('req-length');
const reqUpper = document.getElementById('req-upper');
const reqNumber = document.getElementById('req-number');
const strengthDiv = document.getElementById('passwordStrength');

senhaInput.addEventListener('input', function() {
    const senha = this.value;
    
    // Verificar requisitos
    const hasLength = senha.length >= 6;
    const hasUpper = /[A-Z]/.test(senha);
    const hasNumber = /[0-9]/.test(senha);
    
    // Atualizar ícones
    reqLength.className = hasLength ? 'valid' : '';
    reqUpper.className = hasUpper ? 'valid' : '';
    reqNumber.className = hasNumber ? 'valid' : '';
    
    if (hasLength && hasUpper && hasNumber) {
        strengthDiv.innerHTML = '<i class="fas fa-check-circle"></i> Senha forte';
        strengthDiv.className = 'password-strength strong';
    } else if ((hasLength && hasUpper) || (hasLength && hasNumber)) {
        strengthDiv.innerHTML = '<i class="fas fa-chart-line"></i> Senha média';
        strengthDiv.className = 'password-strength medium';
    } else if (hasLength) {
        strengthDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Senha fraca';
        strengthDiv.className = 'password-strength weak';
    } else {
        strengthDiv.innerHTML = '';
    }
});
</script>

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
    max-width: 500px;
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
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
}

.form-group label i {
    margin-right: 8px;
    color: #e67e22;
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #e67e22;
}

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

.password-strength {
    display: block;
    margin-top: 8px;
    font-size: 12px;
}

.password-strength.strong {
    color: #27ae60;
}

.password-strength.medium {
    color: #f39c12;
}

.password-strength.weak {
    color: #e74c3c;
}

.btn-block {
    width: 100%;
    padding: 14px;
    font-size: 16px;
    margin-top: 10px;
}

.auth-footer {
    text-align: center;
    margin-top: 20px;
    color: #666;
}

.auth-footer a {
    color: #e67e22;
    text-decoration: none;
}
</style>

