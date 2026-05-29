<?php
// app/controllers/PaymentController.php

require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/PaymentSimulator.php';
require_once __DIR__ . '/../helpers/Mailer.php';

class PaymentController {
    private $db;
    private $paymentModel;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->paymentModel = new Payment($db);
        $this->userModel = new User($db);
    }

    public function getCheckoutData($plano) {
        $plan = PaymentSimulator::getPlanConfig($plano);
        if (!$plan) {
            $_SESSION['error'] = 'Plano selecionado inválido.';
            header('Location: index.php?page=planos');
            exit();
        }

        return [
            'selectedPlanKey' => $plano,
            'selectedPlan' => $plan,
            'csrfToken' => $this->getCsrfToken()
        ];
    }

    /**
     * Processa o checkout usando o simulador de pagamento.
     */
    public function processPayment() {
        $this->requireValidCsrf();
        $userSession = Auth::getUser();
        $user = $this->userModel->findById($userSession['id']);

        $plano = $_POST['plano'] ?? '';
        $titular = trim($_POST['titular'] ?? '');
        $metodo = $_POST['metodo_pagamento'] ?? '';
        $plan = PaymentSimulator::getPlanConfig($plano);
        $allowedMethods = ['multicaixa_express', 'cartao', 'transferencia'];

        $errors = [];
        if (!$plan) {
            $errors[] = 'Plano selecionado inválido.';
        }
        if (strlen($titular) < 3) {
            $errors[] = 'Informe o nome do titular com pelo menos 3 caracteres.';
        }
        if (!in_array($metodo, $allowedMethods, true)) {
            $errors[] = 'Método de pagamento inválido.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?page=checkout&plano=' . urlencode($plano));
            exit();
        }

        $simulation = PaymentSimulator::charge([
            'usuario_id' => $user['id'],
            'plano' => $plano,
            'valor' => $plan['valor'],
            'metodo_pagamento' => $metodo,
            'titular' => $titular
        ]);

        $paymentId = $this->paymentModel->create([
            'usuario_id' => $user['id'],
            'valor_kwanza' => $plan['valor'],
            'plano_comprado' => $plano,
            'tipo' => 'mensal',
            'metodo_pagamento' => $metodo,
            'titular' => $titular,
            'referencia' => $simulation['reference'],
            'status' => $simulation['status']
        ]);

        if (!$paymentId) {
            $_SESSION['error'] = 'Não foi possível registrar o pagamento. Tente novamente.';
            header('Location: index.php?page=checkout&plano=' . urlencode($plano));
            exit();
        }

        if ($simulation['approved']) {
            $expirationDate = (new DateTime('+30 days'))->format('Y-m-d');
            $this->paymentModel->markApproved($paymentId);
            $this->userModel->updatePlano($user['id'], $plano, $expirationDate);
            $_SESSION['user_plano'] = $plano;

            Mailer::sendPaymentConfirmation($user['email'], $user['nome_completo'], [
                'plano' => $plan['nome'],
                'valor' => $plan['valor'],
                'referencia' => $simulation['reference'],
                'expiracao' => $expirationDate
            ]);

            $_SESSION['success'] = 'Pagamento aprovado! O plano ' . $plan['nome'] . ' está ativo até ' . date('d/m/Y', strtotime($expirationDate)) . '.';
            header('Location: index.php?page=perfil');
            exit();
        }

        $_SESSION['error'] = 'Pagamento não aprovado pelo simulador.';
        header('Location: index.php?page=checkout&plano=' . urlencode($plano));
        exit();
    }

    /**
     * Webhook de teste: /index.php?page=webhook-pagamento&ref=REFERENCIA
     */
    public function webhook() {
        header('Content-Type: application/json; charset=utf-8');
        $reference = $_GET['ref'] ?? '';
        if ($reference === '') {
            echo json_encode(['success' => false, 'error' => 'Referência não informada.']);
            return;
        }

        $payment = $this->paymentModel->findByReference($reference);
        if (!$payment) {
            echo json_encode(['success' => false, 'error' => 'Pagamento não encontrado.']);
            return;
        }

        $plan = PaymentSimulator::getPlanConfig($payment['plano_comprado']);
        $expirationDate = (new DateTime('+30 days'))->format('Y-m-d');
        $this->paymentModel->markApproved($payment['id']);
        $this->userModel->updatePlano($payment['usuario_id'], $payment['plano_comprado'], $expirationDate);

        echo json_encode([
            'success' => true,
            'message' => 'Webhook simulado processado.',
            'payment_id' => (int)$payment['id'],
            'plano' => $plan['nome'] ?? $payment['plano_comprado'],
            'expiracao' => $expirationDate
        ]);
    }

    /**
     * Cron simulado: rebaixa planos expirados para gratuito.
     */
    public function expirePlans() {
        header('Content-Type: application/json; charset=utf-8');
        $sql = "UPDATE users
                SET plano = 'gratuito', data_expiracao_plano = NULL
                WHERE plano <> 'gratuito'
                AND data_expiracao_plano IS NOT NULL
                AND data_expiracao_plano < CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'Verificação de planos expirados concluída.',
            'usuarios_rebaixados' => $stmt->rowCount()
        ]);
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
            header('Location: index.php?page=planos');
            exit();
        }
    }
}
?>
