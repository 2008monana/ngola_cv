<?php
// app/helpers/Auth.php

// Verificar se a sessão já está ativa antes de iniciar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome_completo'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_plano'] = $user['plano'];
        $_SESSION['logged_in'] = true;
        
        // Atualizar último login
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE users SET ultimo_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
    }
    
    public static function logout() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public static function getUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'nome' => $_SESSION['user_nome'],
                'email' => $_SESSION['user_email'],
                'plano' => $_SESSION['user_plano']
            ];
        }
        return null;
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit();
        }
    }
    
    public static function hasPlan($requiredPlan) {
        $planos = ['gratuito' => 1, 'premium' => 2, 'profissional' => 3];
        $userPlan = $_SESSION['user_plano'] ?? 'gratuito';
        return $planos[$userPlan] >= $planos[$requiredPlan];
    }
    
    public static function requirePlan($requiredPlan) {
        if (!self::hasPlan($requiredPlan)) {
            header('Location: index.php?page=planos&error=plano_necessario');
            exit();
        }
    }
    
    public static function getPlanLimit() {
        $plan = $_SESSION['user_plano'] ?? 'gratuito';
        $limits = [
            'gratuito' => 1,
            'premium' => 5,
            'profissional' => 9999
        ];
        return $limits[$plan];
    }
}
?>