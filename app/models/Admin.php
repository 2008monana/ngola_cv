<?php
// app/models/Admin.php

class Admin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function isAdmin($user_id) {
        $stmt = $this->db->prepare("SELECT is_admin FROM users WHERE id = :id AND ativo = 1");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();
        return $user && (int)$user['is_admin'] === 1;
    }

    public function getDashboardStats() {
        $stats = [];
        $stats['total_usuarios'] = (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['total_curriculos'] = (int)$this->db->query("SELECT COUNT(*) FROM resumes")->fetchColumn();
        $stats['receita_total'] = (float)$this->db->query("SELECT COALESCE(SUM(valor_kwanza), 0) FROM payments WHERE status = 'aprovado'")->fetchColumn();
        $stats['pagamentos_pendentes'] = (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status = 'pendente'")->fetchColumn();
        return $stats;
    }

    public function getNewUsersByMonth($months = 6) {
        $labels = [];
        $values = [];
        $indexByMonth = [];
        $start = new DateTime('first day of this month');
        $start->modify('-' . ($months - 1) . ' months');

        for ($i = 0; $i < $months; $i++) {
            $date = clone $start;
            $date->modify('+' . $i . ' months');
            $key = $date->format('Y-m');
            $labels[] = $date->format('m/Y');
            $values[$i] = 0;
            $indexByMonth[$key] = $i;
        }

        $sql = "SELECT DATE_FORMAT(data_cadastro, '%Y-%m') as mes, COUNT(*) as total
                FROM users
                WHERE data_cadastro >= :start_date
                GROUP BY mes
                ORDER BY mes ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':start_date' => $start->format('Y-m-01 00:00:00')]);
        foreach ($stmt->fetchAll() as $row) {
            if (isset($indexByMonth[$row['mes']])) {
                $values[$indexByMonth[$row['mes']]] = (int)$row['total'];
            }
        }

        return ['labels' => $labels, 'values' => array_values($values)];
    }

    public function getMonthlyRevenue($months = 6) {
        $labels = [];
        $values = [];
        $indexByMonth = [];
        $start = new DateTime('first day of this month');
        $start->modify('-' . ($months - 1) . ' months');

        for ($i = 0; $i < $months; $i++) {
            $date = clone $start;
            $date->modify('+' . $i . ' months');
            $key = $date->format('Y-m');
            $labels[] = $date->format('m/Y');
            $values[$i] = 0;
            $indexByMonth[$key] = $i;
        }

        $sql = "SELECT DATE_FORMAT(COALESCE(data_confirmacao, data_solicitacao), '%Y-%m') as mes,
                       COALESCE(SUM(valor_kwanza), 0) as total
                FROM payments
                WHERE status = 'aprovado'
                AND COALESCE(data_confirmacao, data_solicitacao) >= :start_date
                GROUP BY mes
                ORDER BY mes ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':start_date' => $start->format('Y-m-01 00:00:00')]);
        foreach ($stmt->fetchAll() as $row) {
            if (isset($indexByMonth[$row['mes']])) {
                $values[$indexByMonth[$row['mes']]] = (float)$row['total'];
            }
        }

        return ['labels' => $labels, 'values' => array_values($values)];
    }

    public function getUsers($search = '', $page = 1, $perPage = 10) {
        $offset = max(0, ($page - 1) * $perPage);
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE nome_completo LIKE :search OR email LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM users $where");
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT * FROM users $where ORDER BY data_cadastro DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return ['data' => $stmt->fetchAll(), 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function findUser($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUserResumes($user_id) {
        $sql = "SELECT r.*, t.nome as template_nome FROM resumes r JOIN templates t ON r.template_id = t.id WHERE r.usuario_id = :id ORDER BY r.ultima_versao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function updateUser($id, $dados) {
        $sql = "UPDATE users SET nome_completo = :nome, email = :email, plano = :plano, ativo = :ativo, is_admin = :is_admin WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome' => $dados['nome_completo'],
            ':email' => $dados['email'],
            ':plano' => $dados['plano'],
            ':ativo' => (int)$dados['ativo'],
            ':is_admin' => (int)$dados['is_admin'],
            ':id' => $id
        ]);
    }

    public function toggleUserStatus($id) {
        $stmt = $this->db->prepare("UPDATE users SET ativo = CASE WHEN ativo = 1 THEN 0 ELSE 1 END WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function deleteUser($id) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("DELETE FROM payments WHERE usuario_id = :id");
            $stmt->execute([':id' => $id]);
            $stmt = $this->db->prepare("DELETE FROM notifications WHERE usuario_id = :id");
            $stmt->execute([':id' => $id]);
            $stmt = $this->db->prepare("DELETE FROM resumes WHERE usuario_id = :id");
            $stmt->execute([':id' => $id]);
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $result = $stmt->execute([':id' => $id]);
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getTemplates() {
        return $this->db->query("SELECT * FROM templates ORDER BY ordem ASC, id ASC")->fetchAll();
    }

    public function findTemplate($id) {
        $stmt = $this->db->prepare("SELECT * FROM templates WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function saveTemplate($dados, $id = null) {
        if ($id) {
            $sql = "UPDATE templates SET nome = :nome, slug = :slug, descricao = :descricao, plano_requerido = :plano, html_estrutura = :html, css_estilo = :css, miniatura_url = :miniatura, ordem = :ordem, ativo = :ativo WHERE id = :id";
        } else {
            $sql = "INSERT INTO templates (nome, slug, descricao, plano_requerido, html_estrutura, css_estilo, miniatura_url, ordem, ativo) VALUES (:nome, :slug, :descricao, :plano, :html, :css, :miniatura, :ordem, :ativo)";
        }
        $stmt = $this->db->prepare($sql);
        $params = [
            ':nome' => $dados['nome'],
            ':slug' => $dados['slug'],
            ':descricao' => $dados['descricao'],
            ':plano' => $dados['plano_requerido'],
            ':html' => $dados['html_estrutura'],
            ':css' => $dados['css_estilo'],
            ':miniatura' => $dados['miniatura_url'] ?: null,
            ':ordem' => (int)$dados['ordem'],
            ':ativo' => (int)$dados['ativo']
        ];
        if ($id) {
            $params[':id'] = $id;
        }
        return $stmt->execute($params);
    }

    public function templateUsageCount($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM resumes WHERE template_id = :id");
        $stmt->execute([':id' => $id]);
        return (int)$stmt->fetchColumn();
    }

    public function deleteTemplate($id) {
        if ($this->templateUsageCount($id) > 0) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM templates WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getPayments($status = '') {
        $where = '';
        $params = [];
        if ($status !== '') {
            $where = "WHERE p.status = :status";
            $params[':status'] = $status;
        }
        $sql = "SELECT p.*, u.nome_completo, u.email FROM payments p JOIN users u ON p.usuario_id = u.id $where ORDER BY p.data_solicitacao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updatePaymentStatus($id, $status) {
        $sql = "UPDATE payments SET status = :status, data_confirmacao = CASE WHEN :status_confirm = 'aprovado' THEN NOW() ELSE data_confirmacao END WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':status_confirm' => $status, ':id' => $id]);
    }

    public function getAllUsersForCsv() {
        return $this->db->query("SELECT id, nome_completo, email, telefone, plano, ativo, is_admin, data_cadastro, ultimo_login FROM users ORDER BY data_cadastro DESC")->fetchAll();
    }

    public function getAllPaymentsForCsv() {
        $sql = "SELECT p.id, u.nome_completo, u.email, p.valor_kwanza, p.plano_comprado, p.metodo_pagamento, p.status, p.referencia_multicaixa, p.data_solicitacao, p.data_confirmacao FROM payments p JOIN users u ON p.usuario_id = u.id ORDER BY p.data_solicitacao DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
?>
