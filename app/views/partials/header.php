<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $page_title ?? 'Ngola CV - Seu currículo profissional em Angola'; ?></title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">

    <?php if (($page ?? '') === 'dashboard'): ?>
        <!-- Chart.js para gráficos do dashboard -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
</head>
<body>

<!-- Barra de Navegação -->
<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">
            <i class="fas fa-file-alt"></i> Ngola CV
        </a>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php?page=home"><i class="fas fa-home"></i> Início</a></li>
            <li><a href="index.php?page=templates"><i class="fas fa-layer-group"></i> Templates</a></li>
            <li><a href="index.php?page=planos"><i class="fas fa-tags"></i> Planos</a></li>
            
            <?php if (Auth::isLoggedIn()): ?>
                <li><a href="index.php?page=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="index.php?page=meus-curriculos"><i class="fas fa-file-alt"></i> Meus CVs</a></li>
                <li class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle">
                        <i class="fas fa-user-circle"></i> 
                        <?php 
                            $user_nome = $_SESSION['user_nome'] ?? 'Usuário';
                            $primeiro_nome = explode(' ', $user_nome)[0];
                            echo htmlspecialchars($primeiro_nome); 
                        ?>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="index.php?page=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="index.php?page=meus-curriculos"><i class="fas fa-file-alt"></i> Meus Currículos</a></li>
                        <li><a href="index.php?page=perfil"><i class="fas fa-user-edit"></i> Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="index.php?page=logout" onclick="return confirm('Deseja realmente sair?')"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="index.php?page=login" class="btn-login"><i class="fas fa-sign-in-alt"></i> Entrar</a></li>
                <li><a href="index.php?page=cadastro" class="btn-register"><i class="fas fa-user-plus"></i> Cadastrar</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 10px;">
        <i class="fas fa-spinner fa-spin fa-2x" style="color: #e67e22;"></i>
        <p>Carregando...</p>
    </div>
</div>

<!-- Conteúdo Principal -->
<main>