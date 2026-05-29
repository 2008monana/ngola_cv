<?php
// app/models/Stats.php

class Stats {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Estatísticas principais exibidas nos cards do dashboard.
     */
    public function getSummary($usuario_id, $plano) {
        $sql = "SELECT COUNT(*) as total_cvs, COALESCE(SUM(downloads), 0) as total_downloads
                FROM resumes
                WHERE usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        $resumeStats = $stmt->fetch();

        $planos = ['gratuito' => 1, 'premium' => 2, 'profissional' => 3];
        $nivel = $planos[$plano] ?? 1;

        $sql = "SELECT COUNT(*) as total_templates
                FROM templates
                WHERE ativo = 1
                AND CASE plano_requerido
                    WHEN 'gratuito' THEN 1
                    WHEN 'premium' THEN 2
                    WHEN 'profissional' THEN 3
                END <= :nivel";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nivel' => $nivel]);
        $templateStats = $stmt->fetch();

        return [
            'total_cvs' => (int)($resumeStats['total_cvs'] ?? 0),
            'total_downloads' => (int)($resumeStats['total_downloads'] ?? 0),
            'templates_disponiveis' => (int)($templateStats['total_templates'] ?? 0)
        ];
    }

    /**
     * Currículos criados por mês nos últimos 6 meses.
     */
    public function getResumesByMonth($usuario_id, $months = 6) {
        $labels = [];
        $indexByMonth = [];
        $values = [];
        $start = new DateTime('first day of this month');
        $start->modify('-' . ($months - 1) . ' months');

        for ($i = 0; $i < $months; $i++) {
            $date = clone $start;
            $date->modify('+' . $i . ' months');
            $key = $date->format('Y-m');
            $labels[] = $date->format('m/Y');
            $indexByMonth[$key] = $i;
            $values[$i] = 0;
        }

        $sql = "SELECT DATE_FORMAT(data_criacao, '%Y-%m') as mes, COUNT(*) as total
                FROM resumes
                WHERE usuario_id = :usuario_id
                AND data_criacao >= :start_date
                GROUP BY mes
                ORDER BY mes ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':start_date' => $start->format('Y-m-01 00:00:00')
        ]);

        foreach ($stmt->fetchAll() as $row) {
            if (isset($indexByMonth[$row['mes']])) {
                $values[$indexByMonth[$row['mes']]] = (int)$row['total'];
            }
        }

        return ['labels' => $labels, 'values' => array_values($values)];
    }

    /**
     * Downloads agrupados por template usado nos currículos do usuário.
     */
    public function getDownloadsByTemplate($usuario_id) {
        $sql = "SELECT t.nome, COALESCE(SUM(r.downloads), 0) as total
                FROM resumes r
                JOIN templates t ON r.template_id = t.id
                WHERE r.usuario_id = :usuario_id
                GROUP BY t.id, t.nome
                HAVING total > 0
                ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            return ['labels' => ['Sem downloads'], 'values' => [0]];
        }

        return [
            'labels' => array_map(fn($row) => $row['nome'], $rows),
            'values' => array_map(fn($row) => (int)$row['total'], $rows)
        ];
    }

    /**
     * Atividade diária nos últimos 30 dias com base em criações e edições de currículos.
     */
    public function getActivityLast30Days($usuario_id) {
        $labels = [];
        $indexByDay = [];
        $values = [];
        $start = new DateTime('-29 days');
        $start->setTime(0, 0, 0);

        for ($i = 0; $i < 30; $i++) {
            $date = clone $start;
            $date->modify('+' . $i . ' days');
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $indexByDay[$key] = $i;
            $values[$i] = 0;
        }

        $sql = "SELECT dia, SUM(total) as total FROM (
                    SELECT DATE(data_criacao) as dia, COUNT(*) as total
                    FROM resumes
                    WHERE usuario_id = :usuario_id_criacao AND data_criacao >= :start_criacao
                    GROUP BY DATE(data_criacao)
                    UNION ALL
                    SELECT DATE(ultima_versao) as dia, COUNT(*) as total
                    FROM resumes
                    WHERE usuario_id = :usuario_id_edicao AND ultima_versao >= :start_edicao
                    GROUP BY DATE(ultima_versao)
                ) atividades
                GROUP BY dia
                ORDER BY dia ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id_criacao' => $usuario_id,
            ':start_criacao' => $start->format('Y-m-d H:i:s'),
            ':usuario_id_edicao' => $usuario_id,
            ':start_edicao' => $start->format('Y-m-d H:i:s')
        ]);

        foreach ($stmt->fetchAll() as $row) {
            if (isset($indexByDay[$row['dia']])) {
                $values[$indexByDay[$row['dia']]] = (int)$row['total'];
            }
        }

        return ['labels' => $labels, 'values' => array_values($values)];
    }

    /**
     * Top 3 currículos por visualizações e downloads.
     */
    public function getTopResumes($usuario_id, $limit = 3) {
        $sql = "SELECT r.id, r.titulo, r.visualizacoes, r.downloads, t.nome as template_nome
                FROM resumes r
                JOIN templates t ON r.template_id = t.id
                WHERE r.usuario_id = :usuario_id
                ORDER BY (r.visualizacoes + r.downloads) DESC, r.ultima_versao DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Últimas atividades do usuário derivadas dos dados já existentes no sistema.
     */
    public function getRecentActivities($usuario_id, $limit = 8) {
        $sql = "SELECT tipo, titulo, detalhe, data_evento FROM (
                    SELECT 'criou_curriculo' as tipo, titulo, 'Currículo criado' as detalhe, data_criacao as data_evento
                    FROM resumes
                    WHERE usuario_id = :usuario_id_criacao
                    UNION ALL
                    SELECT 'editou_curriculo' as tipo, titulo, 'Currículo atualizado' as detalhe, ultima_versao as data_evento
                    FROM resumes
                    WHERE usuario_id = :usuario_id_edicao
                    UNION ALL
                    SELECT 'pagamento' as tipo, CONCAT('Plano ', plano_comprado) as titulo, status as detalhe, data_solicitacao as data_evento
                    FROM payments
                    WHERE usuario_id = :usuario_id_pagamento
                ) atividades
                ORDER BY data_evento DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id_criacao', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id_edicao', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id_pagamento', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
