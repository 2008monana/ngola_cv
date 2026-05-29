<?php
// helpers/Functions.php

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function getUserPlan() {
    return $_SESSION['user_plano'] ?? 'gratuito';
}

function hasPremiumAccess() {
    $plan = getUserPlan();
    return $plan === 'premium' || $plan === 'profissional';
}

function hasProfessionalAccess() {
    return getUserPlan() === 'profissional';
}

function showAlert($type, $message) {
    $types = [
        'success' => 'fa-circle-check',
        'error' => 'fa-circle-exclamation',
        'warning' => 'fa-triangle-exclamation',
        'info' => 'fa-circle-info'
    ];
    $icon = $types[$type] ?? 'fa-circle-info';
    echo "<div class='alert alert-{$type}'><i class='fas {$icon}'></i> {$message}</div>";
}
?>