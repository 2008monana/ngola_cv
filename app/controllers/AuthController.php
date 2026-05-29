<?php
// app/controllers/AuthController.php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct($db) {
        $this->userModel = new User($db);
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $confirmar_senha = $_POST['confirmar_senha'] ?? '';
            
            $erros = [];
            
            if (strlen($nome) < 3) {
                $erros[] = "Nome deve ter pelo menos 3 caracteres";
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erros[] = "E-mail inválido";
            }
            
            if (strlen($senha) < 6) {
                $erros[] = "Senha deve ter pelo menos 6 caracteres";
            }
            
            if ($senha !== $confirmar_senha) {
                $erros[] = "As senhas não coincidem";
            }
            
            if (!preg_match('/[A-Z]/', $senha)) {
                $erros[] = "Senha deve conter pelo menos uma letra maiúscula";
            }
            if (!preg_match('/[0-9]/', $senha)) {
                $erros[] = "Senha deve conter pelo menos um número";
            }
            
            if ($this->userModel->emailExists($email)) {
                $erros[] = "E-mail já cadastrado";
            }
            
            if (empty($erros)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $dados = [
                    'nome_completo' => $nome,
                    'email' => $email,
                    'senha_hash' => $senha_hash
                ];
                
                if ($this->userModel->create($dados)) {
                    $_SESSION['success'] = "Cadastro realizado com sucesso! Faça login.";
                    header('Location: index.php?page=login');
                    exit();
                } else {
                    $erros[] = "Erro ao cadastrar. Tente novamente.";
                }
            }
            
            $_SESSION['errors'] = $erros;
            header('Location: index.php?page=cadastro');
            exit();
        }
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user && password_verify($senha, $user['senha_hash'])) {
                Auth::login($user);
                
                // Verificar se plano expirou
                if ($user['plano'] != 'gratuito' && !empty($user['data_expiracao_plano'])) {
                    $data_expiracao = new DateTime($user['data_expiracao_plano']);
                    $hoje = new DateTime();
                    if ($hoje > $data_expiracao) {
                        $this->userModel->updatePlano($user['id'], 'gratuito', null);
                        $_SESSION['user_plano'] = 'gratuito';
                        $_SESSION['warning'] = "Seu plano expirou. Faça upgrade para continuar com os benefícios.";
                    }
                }
                
                header('Location: index.php?page=dashboard');
                exit();
            } else {
                $_SESSION['error'] = "E-mail ou senha incorretos";
                header('Location: index.php?page=login');
                exit();
            }
        }
    }
    
    public function logout() {
        Auth::logout();
        header('Location: index.php');
        exit();
    }
}
?>