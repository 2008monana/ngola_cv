<?php
// public/index.php - Roteador principal

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Resume.php';
require_once __DIR__ . '/../app/models/Template.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/PasswordController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/PaymentController.php';

$db = (new Database())->getConnection();
$authController = new AuthController($db);
$profileController = new ProfileController($db);
$paymentController = new PaymentController($db);

$page = $_GET['page'] ?? 'home';

// Processar ações de autenticação
if ($page === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->register();
    exit();
}

if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login();
    exit();
}

if ($page === 'logout') {
    $authController->logout();
    exit();
}

// API Routes
if ($page === 'api-preview' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: text/html; charset=utf-8');
    
    $dados = $_POST['dados'] ?? [];
    
    // Se vier como string JSON, decodificar
    if (is_string($dados)) {
        $dados = json_decode($dados, true);
    }
    
    // Garantir que é um array
    if (!is_array($dados)) {
        $dados = [];
    }
    
    // Garantir que os arrays existem
    $dados['experiencias'] = $dados['experiencias'] ?? [];
    $dados['educacoes'] = $dados['educacoes'] ?? [];
    $dados['habilidades'] = $dados['habilidades'] ?? [];
    
    $template_id = $_POST['template_id'] ?? 1;
    
    $templateModel = new Template($db);
    $template = $templateModel->findById($template_id);
    
    if ($template) {
        $html = processTemplatePreview($template['html_estrutura'], $dados);
        $css = $template['css_estilo'];
        echo getPreviewHTML($html, $css);
    } else {
        echo '<div class="preview-error">
            <i class="fas fa-exclamation-triangle"></i>
            <p>Template não encontrado</p>
        </div>';
    }
    exit();
}

if ($page === 'api-salvar-curriculo' && $_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    header('Content-Type: application/json');
    
    $user = Auth::getUser();
    
    // Log de TODOS os dados recebidos
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0775, true);
    }
    $log_file = $log_dir . '/save_debug.log';
    file_put_contents($log_file, "\n=== " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
    file_put_contents($log_file, "POST recebido:\n", FILE_APPEND);
    file_put_contents($log_file, print_r($_POST, true), FILE_APPEND);
    
    $id = $_POST['id'] ?? null;
    $titulo = $_POST['titulo'] ?? 'Currículo';
    $template_id = $_POST['template_id'] ?? null;
    $dados_json = $_POST['dados_json'] ?? '{}';
    
    file_put_contents($log_file, "Template ID extraído: " . var_export($template_id, true) . "\n", FILE_APPEND);
    
    // Garantir que template_id é número
    $template_id = (int)$template_id;
    
    if ($template_id === 0) {
        file_put_contents($log_file, "ERRO: template_id é zero!\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Template ID inválido']);
        exit();
    }
    
    try {
        if ($id && $id != 'null' && $id != '') {
            $sql = "UPDATE resumes SET template_id = :template_id, titulo = :titulo, dados_json = :dados_json, ultima_versao = NOW() WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':id' => $id,
                ':usuario_id' => $user['id'],
                ':template_id' => $template_id,
                ':titulo' => $titulo,
                ':dados_json' => $dados_json
            ]);
            file_put_contents($log_file, "UPDATE resultado: " . ($result ? "SUCESSO" : "FALHA") . "\n", FILE_APPEND);
            echo json_encode(['success' => $result, 'id' => $id, 'template_salvo' => $template_id]);
        } else {
            $sql = "INSERT INTO resumes (usuario_id, template_id, titulo, dados_json) VALUES (:usuario_id, :template_id, :titulo, :dados_json)";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':usuario_id' => $user['id'],
                ':template_id' => $template_id,
                ':titulo' => $titulo,
                ':dados_json' => $dados_json
            ]);
            $newId = $result ? $db->lastInsertId() : null;
            echo json_encode(['success' => $result, 'id' => $newId, 'template_salvo' => $template_id]);
        }
    } catch (Exception $e) {
        file_put_contents($log_file, "EXCEÇÃO: " . $e->getMessage() . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}


if ($page === 'api-exportar-pdf' && Auth::isLoggedIn()) {
    $resume_id = $_GET['id'] ?? 0;
    $user = Auth::getUser();
    
    // Buscar o currículo com o template_id correto
    $sql = "SELECT r.*, t.html_estrutura, t.css_estilo, t.nome as template_nome 
            FROM resumes r 
            JOIN templates t ON r.template_id = t.id 
            WHERE r.id = :id AND r.usuario_id = :usuario_id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $resume_id, ':usuario_id' => $user['id']]);
    $resume = $stmt->fetch();
    
    if ($resume) {
        require_once __DIR__ . '/../app/helpers/PDFGenerator.php';
        
        // Log do template usado
        error_log("Exportando PDF - Resume ID: $resume_id, Template: " . ($resume['template_nome'] ?? 'desconhecido') . " (ID: " . ($resume['template_id'] ?? 'null') . ")");
        
        $resumeModel = new Resume($db);
        $resumeModel->incrementDownload($resume_id);
        
        try {
            $pdf = PDFGenerator::generate($resume, $resume['html_estrutura'], $resume['css_estilo']);
            
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="curriculo_' . $resume['id'] . '.pdf"');
            echo $pdf;
        } catch (Exception $e) {
            echo "Erro ao gerar PDF: " . $e->getMessage();
        }
    } else {
        echo "Currículo não encontrado.";
    }
    exit();
}

// Ações de currículo
if ($page === 'duplicar-curriculo' && Auth::isLoggedIn()) {
    $resume_id = $_GET['id'] ?? 0;
    $user = Auth::getUser();
    
    $resumeModel = new Resume($db);
    if ($resumeModel->duplicate($resume_id, $user['id'])) {
        $_SESSION['success'] = "Currículo duplicado com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao duplicar currículo.";
    }
    header('Location: index.php?page=meus-curriculos');
    exit();
}

if ($page === 'excluir-curriculo' && Auth::isLoggedIn()) {
    $resume_id = $_GET['id'] ?? 0;
    $user = Auth::getUser();
    
    $resumeModel = new Resume($db);
    if ($resumeModel->delete($resume_id, $user['id'])) {
        $_SESSION['success'] = "Currículo excluído com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao excluir currículo.";
    }
    header('Location: index.php?page=meus-curriculos');
    exit();
}

// Ações do perfil do usuário
if ($page === 'atualizar-perfil' && $_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    $profileController->updateProfile();
    exit();
}

if ($page === 'alterar-senha' && $_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    $profileController->changePassword();
    exit();
}

if ($page === 'excluir-conta' && $_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    $profileController->deleteAccount();
    exit();
}

// Ações de pagamento simulado
if ($page === 'processar-pagamento' && $_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    $paymentController->processPayment();
    exit();
}

if ($page === 'webhook-pagamento') {
    $paymentController->webhook();
    exit();
}

if ($page === 'plano-expirado') {
    $paymentController->expirePlans();
    exit();
}

// Rotas públicas
$public_pages = ['home', 'login', 'cadastro', 'templates', 'planos', 'sobre', 'faq', 'termos', 'privacidade', 'webhook-pagamento', 'plano-expirado'];
$auth_pages = ['dashboard', 'meus-curriculos', 'editor', 'perfil', 'checkout'];

// Verificar se precisa de autenticação
if (!in_array($page, $public_pages) && !Auth::isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}

// DEFINIR SE VAI USAR HEADER/FOOTER (API e ações não usam)
$use_layout = true;

// Páginas que NÃO devem usar o layout padrão
$no_layout_pages = ['api-preview', 'api-salvar-curriculo', 'api-exportar-pdf', 'webhook-pagamento', 'plano-expirado', 'processar-pagamento'];
if (in_array($page, $no_layout_pages)) {
    $use_layout = false;
}

// Incluir header APENAS se for página com layout
if ($use_layout) {
    include __DIR__ . '/../app/views/partials/header.php';
}
$passwordController = new PasswordController($db);

// Roteamento das páginas
switch($page) {
    case 'home':
        $page_title = 'Ngola CV - Crie seu currículo profissional em Angola';
        include __DIR__ . '/../app/views/home.php';
        break;
    case 'login':
        include __DIR__ . '/../app/views/auth/login.php';
        break;
    case 'cadastro':
        include __DIR__ . '/../app/views/auth/register.php';
        break;
    case 'dashboard':
        $page_title = 'Dashboard - Ngola CV';
        include __DIR__ . '/../app/views/dashboard/index.php';
        break;
    case 'meus-curriculos':
        $page_title = 'Meus Currículos - Ngola CV';
        include __DIR__ . '/../app/views/resumes/index.php';
        break;
    case 'editor':
        $page_title = 'Editor de Currículo - Ngola CV';
        include __DIR__ . '/../app/views/editor/index.php';
        break;
    case 'templates':
        include __DIR__ . '/../app/views/templates/index.php';
        break;
    case 'planos':
        include __DIR__ . '/../app/views/plans/index.php';
        break;
    case 'checkout':
        $page_title = 'Checkout - Ngola CV';
        $checkoutData = $paymentController->getCheckoutData($_GET['plano'] ?? '');
        extract($checkoutData);
        include __DIR__ . '/../app/views/plans/checkout.php';
        break;
    case 'perfil':
        $page_title = 'Meu Perfil - Ngola CV';
        $profileData = $profileController->getProfileData(Auth::getUser()['id']);
        extract($profileData);
        include __DIR__ . '/../app/views/profile/index.php';
        break;
    case 'esqueci-senha':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $passwordController->forgot();
        exit();
    }
    include __DIR__ . '/../app/views/auth/forgot.php';
    break;

case 'redefinir-senha':
    $token = $_GET['token'] ?? '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $passwordController->reset($token);
        exit();
    }
    include __DIR__ . '/../app/views/auth/reset.php';
    break;

    default:
        $page_title = 'Ngola CV - Página não encontrada';
        include __DIR__ . '/../app/views/404.php';
}

// Incluir footer APENAS se for página com layout
if ($use_layout) {
    include __DIR__ . '/../app/views/partials/footer.php';
}

// Funções auxiliares para preview
function processTemplatePreview($template, $dados) {
    if (is_string($dados)) {
        $dados = json_decode($dados, true);
    }
    if (!is_array($dados)) {
        $dados = [];
    }
    
    // Garantir que todos os campos existem
    $dados['nome'] = $dados['nome'] ?? '';
    $dados['profissao'] = $dados['profissao'] ?? '';
    $dados['email'] = $dados['email'] ?? '';
    $dados['telefone'] = $dados['telefone'] ?? '';
    $dados['endereco'] = $dados['endereco'] ?? '';
    $dados['sobre'] = $dados['sobre'] ?? '';
    $dados['experiencias'] = $dados['experiencias'] ?? [];
    $dados['educacoes'] = $dados['educacoes'] ?? [];
    $dados['habilidades'] = $dados['habilidades'] ?? [];
    $dados['foto_url'] = $dados['foto_url'] ?? '';
    
    $html = $template;
    
    // Processar foto
    $foto_html = '';
    if (!empty($dados['foto_url'])) {
        $foto_html = '<img src="' . htmlspecialchars($dados['foto_url']) . '" alt="Foto">';
    } else {
        $foto_html = '<i class="fas fa-user-circle"></i>';
    }
    $html = str_replace('{{foto}}', $foto_html, $html);
    
    // Dados pessoais
    $html = str_replace('{{nome}}', htmlspecialchars($dados['nome'] ?: 'Seu Nome'), $html);
    $html = str_replace('{{profissao}}', htmlspecialchars($dados['profissao'] ?: 'Sua Profissão'), $html);
    $html = str_replace('{{email}}', htmlspecialchars($dados['email'] ?: 'seu@email.com'), $html);
    $html = str_replace('{{telefone}}', htmlspecialchars($dados['telefone'] ?: '(00) 00000-0000'), $html);
    $html = str_replace('{{endereco}}', htmlspecialchars($dados['endereco'] ?: 'Seu Endereço'), $html);
    $html = str_replace('{{sobre}}', nl2br(htmlspecialchars($dados['sobre'] ?: 'Sobre você...')), $html);
    
    // Processar experiências
    $experiencias_html = '';
    if (!empty($dados['experiencias']) && is_array($dados['experiencias'])) {
        foreach ($dados['experiencias'] as $exp) {
            if (!empty($exp['cargo'])) {
                $experiencias_html .= '<div class="experiencia-item">';
                $experiencias_html .= '<h4>' . htmlspecialchars($exp['cargo'] ?? '') . '</h4>';
                $experiencias_html .= '<p class="empresa">' . htmlspecialchars($exp['empresa'] ?? '') . '</p>';
                $experiencias_html .= '<p class="periodo">' . htmlspecialchars($exp['periodo'] ?? '') . '</p>';
                $experiencias_html .= '<p>' . nl2br(htmlspecialchars($exp['descricao'] ?? '')) . '</p>';
                $experiencias_html .= '</div>';
            }
        }
    }
    if (empty($experiencias_html)) {
        $experiencias_html = '<p class="empty-message"><i class="fas fa-info-circle"></i> Nenhuma experiência adicionada</p>';
    }
    $html = str_replace('{{experiencias}}', $experiencias_html, $html);
    
    // Processar educação
    $educacoes_html = '';
    if (!empty($dados['educacoes']) && is_array($dados['educacoes'])) {
        foreach ($dados['educacoes'] as $edu) {
            if (!empty($edu['curso'])) {
                $educacoes_html .= '<div class="educacao-item">';
                $educacoes_html .= '<h4>' . htmlspecialchars($edu['curso'] ?? '') . '</h4>';
                $educacoes_html .= '<p class="instituicao">' . htmlspecialchars($edu['instituicao'] ?? '') . '</p>';
                $educacoes_html .= '<p class="periodo">' . htmlspecialchars($edu['periodo'] ?? '') . '</p>';
                $educacoes_html .= '</div>';
            }
        }
    }
    if (empty($educacoes_html)) {
        $educacoes_html = '<p class="empty-message"><i class="fas fa-info-circle"></i> Nenhuma formação adicionada</p>';
    }
    $html = str_replace('{{educacoes}}', $educacoes_html, $html);
    
    // Processar habilidades - ESSA É A PARTE IMPORTANTE!
    $habilidades_html = '';
    if (!empty($dados['habilidades']) && is_array($dados['habilidades'])) {
        // Verificar se é array de strings
        $habilidades = $dados['habilidades'];
        if (count($habilidades) > 0) {
            $habilidades_html .= '<div class="habilidades-container"><ul>';
            foreach ($habilidades as $hab) {
                if (!empty($hab) && is_string($hab)) {
                    $habilidades_html .= '<li>' . htmlspecialchars(trim($hab)) . '</li>';
                }
            }
            $habilidades_html .= '</ul></div>';
        }
    }
    
    // Se não houver habilidades, mostrar mensagem
    if (empty($habilidades_html) || $habilidades_html == '<div class="habilidades-container"><ul></ul></div>') {
        $habilidades_html = '<p class="empty-message"><i class="fas fa-info-circle"></i> Nenhuma habilidade adicionada</p>';
    }
    $html = str_replace('{{habilidades}}', $habilidades_html, $html);
    
    return $html;
}

function getPreviewHTML($content, $custom_css) {
    return '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Pré-visualização - Ngola CV</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                line-height: 1.6;
                background: #f5f5f5;
            }
            .preview-wrapper {
                max-width: 900px;
                margin: 0 auto;
                background: white;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            }
            ' . $custom_css . '
            
            /* Estilos gerais para preview */
            .experiencia-item, .educacao-item {
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #eee;
            }
            .experiencia-item:last-child, .educacao-item:last-child {
                border-bottom: none;
            }
            .empresa, .instituicao {
                font-weight: 500;
                color: #555;
                margin: 5px 0;
            }
            .periodo {
                color: #888;
                font-size: 13px;
                margin-bottom: 10px;
            }
            .habilidades-container ul {
                list-style: none;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 10px;
            }
            .habilidades-container li {
                background: #f0f0f0;
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 13px;
            }
            .empty-message {
                color: #999;
                font-style: italic;
                padding: 10px;
                text-align: center;
                background: #f9f9f9;
                border-radius: 8px;
            }
            .preview-error {
                text-align: center;
                padding: 50px;
                color: #e74c3c;
            }
            /* Estilos para o preview */
            .experiencia-item, .educacao-item {
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #eee;
            }
            .experiencia-item:last-child, .educacao-item:last-child {
                border-bottom: none;
            }
            .empresa, .instituicao {
                font-weight: 500;
                color: #e67e22;
                margin: 5px 0;
            }
            .periodo {
                color: #888;
                font-size: 13px;
                margin-bottom: 10px;
            }
            .habilidades-container ul {
                list-style: none;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin: 10px 0 0;
                padding: 0;
            }
            .habilidades-container li {
                background: #2c3e66;
                color: white;
                padding: 6px 15px;
                border-radius: 20px;
                font-size: 13px;
            }
            .empty-message {
                color: #999;
                font-style: italic;
                padding: 10px;
                text-align: center;
                background: #f9f9f9;
                border-radius: 8px;
            }
            .cv-sobre p, .sobre p {
                line-height: 1.6;
                color: #444;
            }
        </style>
    </head>
    <body>
        <div class="preview-wrapper">' . $content . '</div>
    </body>
    </html>';
}?>