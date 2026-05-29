<?php
// app/controllers/PasswordController.php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Mailer.php';

class PasswordController {
    private $userModel;
    
    public function __construct($db) {
        $this->userModel = new User($db);
    }
    
    public function forgot() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                $_SESSION['error'] = "Digite seu e-mail";
                header('Location: index.php?page=esqueci-senha');
                exit();
            }
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user) {
                // Gerar token único
                $token = bin2hex(random_bytes(32));
                $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Salvar no banco
                $sql = "UPDATE users SET reset_token = :token, reset_token_expira = :expira WHERE id = :id";
                $stmt = $this->userModel->db->prepare($sql);
                $stmt->execute([
                    ':token' => $token,
                    ':expira' => $expira,
                    ':id' => $user['id']
                ]);
                
                // Enviar e-mail
                $enviado = Mailer::sendResetPassword($user['email'], $user['nome_completo'], $token);
                
                if ($enviado) {
                    $_SESSION['success'] = "Link de recuperação enviado para seu e-mail!";
                } else {
                    $_SESSION['warning'] = "Não foi possível enviar o e-mail. Tente novamente.";
                }
            } else {
                // Não revelar se o e-mail existe por segurança
                $_SESSION['success'] = "Se o e-mail existir, você receberá o link de recuperação.";
            }
            
            header('Location: index.php?page=esqueci-senha');
            exit();
        }
    }
    
    public function reset($token) {
        // Verificar se token é válido
        $sql = "SELECT * FROM users WHERE reset_token = :token AND reset_token_expira > NOW()";
        $stmt = $this->userModel->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $_SESSION['error'] = "Link inválido ou expirado. Solicite uma nova recuperação.";
            header('Location: index.php?page=esqueci-senha');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $senha = $_POST['senha'] ?? '';
            $confirmar = $_POST['confirmar_senha'] ?? '';
            
            $erros = [];
            
            if (strlen($senha) < 6) {
                $erros[] = "Senha deve ter pelo menos 6 caracteres";
            }
            if (!preg_match('/[A-Z]/', $senha)) {
                $erros[] = "Senha deve conter pelo menos uma letra maiúscula";
            }
            if (!preg_match('/[0-9]/', $senha)) {
                $erros[] = "Senha deve conter pelo menos um número";
            }
            if ($senha !== $confirmar) {
                $erros[] = "As senhas não coincidem";
            }
            
            if (empty($erros)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                $sql = "UPDATE users SET senha_hash = :senha, reset_token = NULL, reset_token_expira = NULL WHERE id = :id";
                $stmt = $this->userModel->db->prepare($sql);
                $stmt->execute([':senha' => $senha_hash, ':id' => $user['id']]);
                
                $_SESSION['success'] = "Senha alterada com sucesso! Faça login com sua nova senha.";
                header('Location: index.php?page=login');
                exit();
            }
            
            $_SESSION['errors'] = $erros;
            header('Location: index.php?page=redefinir-senha&token=' . $token);
            exit();
        }
    }
}
?>