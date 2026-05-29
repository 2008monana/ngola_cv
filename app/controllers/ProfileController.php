<?php
// app/controllers/ProfileController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Payment.php';

class ProfileController {
    private $db;
    private $userModel;
    private $paymentModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
        $this->paymentModel = new Payment($db);
    }

    /**
     * Dados necessários para renderizar a página de perfil.
     */
    public function getProfileData($user_id) {
        return [
            'profileUser' => $this->userModel->findById($user_id),
            'payments' => $this->paymentModel->getByUser($user_id),
            'csrfToken' => $this->getCsrfToken()
        ];
    }

    /**
     * Atualiza dados pessoais editáveis do usuário.
     */
    public function updateProfile() {
        $this->requireValidCsrf();
        $user = Auth::getUser();

        $nome = trim($_POST['nome_completo'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $avatar_url = trim($_POST['avatar_url'] ?? '');

        $errors = [];
        if (strlen($nome) < 3) {
            $errors[] = 'Nome completo deve ter pelo menos 3 caracteres.';
        }
        if ($avatar_url !== '' && !filter_var($avatar_url, FILTER_VALIDATE_URL)) {
            $errors[] = 'URL do avatar inválida.';
        }
        if (strlen($telefone) > 20) {
            $errors[] = 'Telefone deve ter no máximo 20 caracteres.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?page=perfil');
            exit();
        }

        $updated = $this->userModel->updateProfile($user['id'], [
            'nome_completo' => $nome,
            'telefone' => $telefone,
            'endereco' => $endereco,
            'avatar_url' => $avatar_url
        ]);

        if ($updated) {
            $_SESSION['success'] = 'Perfil atualizado com sucesso.';
            $_SESSION['user_nome'] = $nome;
        } else {
            $_SESSION['error'] = 'Não foi possível atualizar o perfil. Tente novamente.';
        }

        header('Location: index.php?page=perfil');
        exit();
    }

    /**
     * Altera a senha validando a senha atual e a política mínima da nova senha.
     */
    public function changePassword() {
        $this->requireValidCsrf();
        $userSession = Auth::getUser();
        $user = $this->userModel->findById($userSession['id']);

        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';

        $errors = [];
        if (!$user || !password_verify($senha_atual, $user['senha_hash'])) {
            $errors[] = 'Senha atual incorreta.';
        }
        if (strlen($nova_senha) < 6) {
            $errors[] = 'Nova senha deve ter pelo menos 6 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $nova_senha)) {
            $errors[] = 'Nova senha deve conter pelo menos uma letra maiúscula.';
        }
        if (!preg_match('/[0-9]/', $nova_senha)) {
            $errors[] = 'Nova senha deve conter pelo menos um número.';
        }
        if ($nova_senha !== $confirmar_senha) {
            $errors[] = 'A confirmação da nova senha não coincide.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?page=perfil#alterar-senha');
            exit();
        }

        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        if ($this->userModel->updatePassword($userSession['id'], $senha_hash)) {
            $_SESSION['success'] = 'Senha alterada com sucesso.';
        } else {
            $_SESSION['error'] = 'Não foi possível alterar a senha. Tente novamente.';
        }

        header('Location: index.php?page=perfil#alterar-senha');
        exit();
    }

    /**
     * Exclui dados vinculados e remove a conta do usuário autenticado.
     */
    public function deleteAccount() {
        $this->requireValidCsrf();
        $user = Auth::getUser();
        $confirmacao = $_POST['confirmacao'] ?? '';

        if ($confirmacao !== 'EXCLUIR') {
            $_SESSION['error'] = 'Digite EXCLUIR para confirmar a remoção definitiva da conta.';
            header('Location: index.php?page=perfil#excluir-conta');
            exit();
        }

        try {
            $this->userModel->deleteAccount($user['id']);
            Auth::logout();
            session_start();
            $_SESSION['success'] = 'Conta excluída com sucesso.';
            header('Location: index.php?page=home');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Não foi possível excluir a conta: ' . $e->getMessage();
            header('Location: index.php?page=perfil#excluir-conta');
            exit();
        }
    }

    private function getCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function requireValidCsrf() {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['error'] = 'Sessão expirada ou token inválido. Tente novamente.';
            header('Location: index.php?page=perfil');
            exit();
        }
    }
}
?>
