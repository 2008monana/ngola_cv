<?php
// app/controllers/AdminController.php

require_once __DIR__ . '/../models/Admin.php';

class AdminController {
    private $adminModel;

    public function __construct($db) {
        $this->adminModel = new Admin($db);
    }

    public function requireAdmin() {
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit();
        }
        $user = Auth::getUser();
        if (!$this->adminModel->isAdmin($user['id'])) {
            $_SESSION['error'] = 'Acesso restrito a administradores.';
            header('Location: index.php?page=dashboard');
            exit();
        }
    }

    public function dashboardData() {
        $this->requireAdmin();
        return [
            'stats' => $this->adminModel->getDashboardStats(),
            'newUsersChart' => $this->adminModel->getNewUsersByMonth(),
            'revenueChart' => $this->adminModel->getMonthlyRevenue()
        ];
    }

    public function usersData() {
        $this->requireAdmin();
        $search = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['p'] ?? 1));
        $selectedUser = null;
        $userResumes = [];
        if (!empty($_GET['editar'])) {
            $selectedUser = $this->adminModel->findUser((int)$_GET['editar']);
        }
        if (!empty($_GET['curriculos'])) {
            $selectedUser = $this->adminModel->findUser((int)$_GET['curriculos']);
            $userResumes = $this->adminModel->getUserResumes((int)$_GET['curriculos']);
        }
        return [
            'usersResult' => $this->adminModel->getUsers($search, $page),
            'search' => $search,
            'selectedUser' => $selectedUser,
            'userResumes' => $userResumes,
            'csrfToken' => $this->getCsrfToken()
        ];
    }

    public function templatesData() {
        $this->requireAdmin();
        $editingTemplate = null;
        if (!empty($_GET['editar'])) {
            $editingTemplate = $this->adminModel->findTemplate((int)$_GET['editar']);
        }
        return [
            'templates' => $this->adminModel->getTemplates(),
            'editingTemplate' => $editingTemplate,
            'previewTemplate' => !empty($_GET['preview']) ? $this->adminModel->findTemplate((int)$_GET['preview']) : null,
            'csrfToken' => $this->getCsrfToken()
        ];
    }

    public function paymentsData() {
        $this->requireAdmin();
        $status = $_GET['status'] ?? '';
        return [
            'payments' => $this->adminModel->getPayments($status),
            'status' => $status,
            'csrfToken' => $this->getCsrfToken()
        ];
    }

    public function reportsData() {
        $this->requireAdmin();
        return [
            'stats' => $this->adminModel->getDashboardStats(),
            'revenueChart' => $this->adminModel->getMonthlyRevenue(12)
        ];
    }

    public function saveUser() {
        $this->requireAdmin();
        $this->requireValidCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $dados = [
            'nome_completo' => trim($_POST['nome_completo'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'plano' => $_POST['plano'] ?? 'gratuito',
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
            'is_admin' => isset($_POST['is_admin']) ? 1 : 0
        ];
        if ($id <= 0 || strlen($dados['nome_completo']) < 3 || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Dados do usuário inválidos.';
        } else {
            $this->adminModel->updateUser($id, $dados);
            $_SESSION['success'] = 'Usuário atualizado com sucesso.';
        }
        header('Location: index.php?page=admin/usuarios');
        exit();
    }

    public function toggleUser() {
        $this->requireAdmin();
        $this->adminModel->toggleUserStatus((int)($_GET['id'] ?? 0));
        $_SESSION['success'] = 'Estado do usuário atualizado.';
        header('Location: index.php?page=admin/usuarios');
        exit();
    }

    public function deleteUser() {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $current = Auth::getUser();
        if ($id === (int)$current['id']) {
            $_SESSION['error'] = 'Você não pode excluir a própria conta administrativa.';
        } else {
            $this->adminModel->deleteUser($id);
            $_SESSION['success'] = 'Usuário excluído com sucesso.';
        }
        header('Location: index.php?page=admin/usuarios');
        exit();
    }

    public function saveTemplate() {
        $this->requireAdmin();
        $this->requireValidCsrf();
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $dados = [
            'nome' => trim($_POST['nome'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'plano_requerido' => $_POST['plano_requerido'] ?? 'gratuito',
            'html_estrutura' => $_POST['html_estrutura'] ?? '',
            'css_estilo' => $_POST['css_estilo'] ?? '',
            'miniatura_url' => trim($_POST['miniatura_url'] ?? ''),
            'ordem' => (int)($_POST['ordem'] ?? 0),
            'ativo' => isset($_POST['ativo']) ? 1 : 0
        ];
        if ($dados['nome'] === '' || $dados['slug'] === '' || $dados['html_estrutura'] === '' || $dados['css_estilo'] === '') {
            $_SESSION['error'] = 'Preencha nome, slug, HTML e CSS do template.';
        } else {
            $this->adminModel->saveTemplate($dados, $id);
            $_SESSION['success'] = 'Template salvo com sucesso.';
        }
        header('Location: index.php?page=admin/templates');
        exit();
    }

    public function deleteTemplate() {
        $this->requireAdmin();
        if ($this->adminModel->deleteTemplate((int)($_GET['id'] ?? 0))) {
            $_SESSION['success'] = 'Template excluído com sucesso.';
        } else {
            $_SESSION['error'] = 'Não é possível excluir template usado por currículos.';
        }
        header('Location: index.php?page=admin/templates');
        exit();
    }

    public function updatePaymentStatus($status) {
        $this->requireAdmin();
        $allowed = ['aprovado', 'reembolsado', 'falhou'];
        if (in_array($status, $allowed, true)) {
            $this->adminModel->updatePaymentStatus((int)($_GET['id'] ?? 0), $status);
            $_SESSION['success'] = 'Pagamento atualizado.';
        }
        header('Location: index.php?page=admin/pagamentos');
        exit();
    }

    public function exportCsv($type) {
        $this->requireAdmin();
        $rows = $type === 'pagamentos' ? $this->adminModel->getAllPaymentsForCsv() : $this->adminModel->getAllUsersForCsv();
        $filename = $type === 'pagamentos' ? 'relatorio_pagamentos.csv' : 'relatorio_usuarios.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        if (!empty($rows)) {
            fputcsv($out, array_keys($rows[0]));
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
        }
        fclose($out);
        exit();
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
            $_SESSION['error'] = 'Token inválido. Tente novamente.';
            header('Location: index.php?page=admin/dashboard');
            exit();
        }
    }
}
?>
