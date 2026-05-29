<?php
// app/helpers/PDFGenerator.php

$composer_autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($composer_autoload)) {
    require_once $composer_autoload;
} else {
    die('Erro: Instale o Dompdf via Composer: composer require dompdf/dompdf');
}

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFGenerator {
    
    public static function generate($resume_data, $template_html, $template_css) {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        $dompdf = new Dompdf($options);
        
        $dados = json_decode($resume_data['dados_json'], true);
        if (!is_array($dados)) {
            $dados = [];
        }
        
        // Garantir campos padrão
        $dados = self::normalizeData($dados);
        
        // Processar template para PDF
        $html = self::processTemplateForPDF($template_html, $dados);
        $full_html = self::getFullHTMLForPDF($html, $template_css);
        
        $dompdf->loadHtml($full_html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }
    
    private static function normalizeData($dados) {
        return [
            'nome' => $dados['nome'] ?? 'Sem nome',
            'profissao' => $dados['profissao'] ?? '',
            'email' => $dados['email'] ?? '',
            'telefone' => $dados['telefone'] ?? '',
            'endereco' => $dados['endereco'] ?? '',
            'sobre' => $dados['sobre'] ?? '',
            'foto_url' => $dados['foto_url'] ?? '',
            'experiencias' => $dados['experiencias'] ?? [],
            'educacoes' => $dados['educacoes'] ?? [],
            'habilidades' => $dados['habilidades'] ?? []
        ];
    }
    
    private static function processTemplateForPDF($template, $dados) {
        $html = $template;

        // Usar o mesmo HTML base da pré-visualização para que o PDF fique fiel ao template escolhido.
        $foto_html = '';
        if (!empty($dados['foto_url'])) {
            $foto_html = '<img src="' . htmlspecialchars($dados['foto_url']) . '" alt="Foto">';
        } else {
            $foto_html = '<span class="foto-placeholder">👤</span>';
        }
        $html = str_replace('{{foto}}', $foto_html, $html);

        // Dados pessoais
        $html = str_replace('{{nome}}', htmlspecialchars($dados['nome'] ?: 'Seu Nome'), $html);
        $html = str_replace('{{profissao}}', htmlspecialchars($dados['profissao'] ?: 'Sua Profissão'), $html);
        $html = str_replace('{{email}}', htmlspecialchars($dados['email'] ?: 'seu@email.com'), $html);
        $html = str_replace('{{telefone}}', htmlspecialchars($dados['telefone'] ?: '(00) 00000-0000'), $html);
        $html = str_replace('{{endereco}}', htmlspecialchars($dados['endereco'] ?: 'Seu Endereço'), $html);
        $html = str_replace('{{sobre}}', nl2br(htmlspecialchars($dados['sobre'] ?: 'Sobre você...')), $html);

        // Experiências: mesmas classes usadas no preview e nos templates salvos no banco.
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
            $experiencias_html = '<p class="empty-message">Nenhuma experiência adicionada</p>';
        }
        $html = str_replace('{{experiencias}}', $experiencias_html, $html);

        // Educação
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
            $educacoes_html = '<p class="empty-message">Nenhuma formação adicionada</p>';
        }
        $html = str_replace('{{educacoes}}', $educacoes_html, $html);

        // Habilidades
        $habilidades_html = '';
        if (!empty($dados['habilidades']) && is_array($dados['habilidades'])) {
            $habilidades_html .= '<div class="habilidades-container"><ul>';
            foreach ($dados['habilidades'] as $hab) {
                if (!empty($hab) && is_string($hab)) {
                    $habilidades_html .= '<li>' . htmlspecialchars(trim($hab)) . '</li>';
                }
            }
            $habilidades_html .= '</ul></div>';
        }
        if (empty($habilidades_html) || $habilidades_html == '<div class="habilidades-container"><ul></ul></div>') {
            $habilidades_html = '<p class="empty-message">Nenhuma habilidade adicionada</p>';
        }
        $html = str_replace('{{habilidades}}', $habilidades_html, $html);

        // Evitar placeholders não preenchidos em templates premium/profissional.
        return preg_replace('/{{[^}]+}}/', '', $html);
    }

    private static function getFullHTMLForPDF($content, $custom_css) {
        // CSS compatível com Dompdf (sem flexbox/grid modernos)
        $pdf_css = '
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: "DejaVu Sans", Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                color: #333;
                padding: 20px;
            }
            .pdf-wrapper {
                max-width: 100%;
                background: white;
            }
            
            /* Template Moderno Azul - CSS compatível */
            .cv-moderno {
                width: 100%;
                background: white;
            }
            .cv-moderno .cv-header {
                background: #2c3e66;
                color: white;
                padding: 20px;
                text-align: center;
                margin-bottom: 15px;
            }
            .cv-moderno .cv-foto {
                width: 100px;
                height: 100px;
                margin: 0 auto 10px;
                text-align: center;
            }
            .cv-moderno .cv-foto .foto-placeholder {
                font-size: 60px;
            }
            .cv-moderno .cv-titulo h1 {
                font-size: 24px;
                margin-bottom: 5px;
            }
            .cv-moderno .cv-titulo p {
                font-size: 14px;
                opacity: 0.9;
            }
            .cv-moderno h3 {
                color: #2c3e66;
                font-size: 16px;
                margin: 15px 0 10px;
                padding-bottom: 5px;
                border-bottom: 2px solid #e67e22;
            }
            .cv-moderno .cv-sobre,
            .cv-moderno .cv-experiencias,
            .cv-moderno .cv-educacao,
            .cv-moderno .cv-habilidades {
                padding: 0 20px;
                margin-bottom: 15px;
            }
            .cv-moderno .item {
                margin-bottom: 12px;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }
            .cv-moderno .item-title {
                font-weight: bold;
                font-size: 14px;
                color: #333;
            }
            .cv-moderno .item-subtitle {
                color: #e67e22;
                font-weight: 500;
                margin: 3px 0;
            }
            .cv-moderno .item-date {
                color: #888;
                font-size: 10px;
                margin-bottom: 5px;
            }
            .cv-moderno .item-description {
                font-size: 11px;
                color: #555;
            }
            .cv-moderno .skills-list {
                display: table;
                width: 100%;
            }
            .cv-moderno .skill-tag {
                display: inline-block;
                background: #2c3e66;
                color: white;
                padding: 4px 10px;
                border-radius: 15px;
                font-size: 10px;
                margin: 0 5px 5px 0;
            }
            .cv-moderno .cv-footer {
                background: #f8f9fa;
                padding: 12px 20px;
                text-align: center;
                font-size: 10px;
                color: #666;
                margin-top: 15px;
            }
            
            /* Template Clássico Elegante - CSS compatível */
            .cv-classico {
                width: 100%;
                background: white;
            }
            .cv-classico .cv-sidebar {
                width: 33%;
                float: left;
                background: #2c3e66;
                color: white;
                padding: 20px;
                min-height: 100%;
            }
            .cv-classico .cv-main {
                width: 67%;
                float: left;
                padding: 20px;
            }
            .cv-classico:after {
                content: "";
                display: table;
                clear: both;
            }
            .cv-classico .cv-foto {
                text-align: center;
                margin-bottom: 15px;
            }
            .cv-classico .cv-foto .foto-placeholder {
                font-size: 60px;
            }
            .cv-classico .cv-sidebar h2 {
                text-align: center;
                font-size: 18px;
                margin: 10px 0;
            }
            .cv-classico .cv-sidebar .profissao {
                text-align: center;
                font-size: 12px;
                opacity: 0.9;
            }
            .cv-classico .cv-sidebar hr {
                margin: 15px 0;
                border-color: rgba(255,255,255,0.2);
            }
            .cv-classico .cv-sidebar h4 {
                font-size: 14px;
                margin: 15px 0 10px;
                border-bottom: 2px solid #e67e22;
                display: inline-block;
            }
            .cv-classico .contato p {
                margin: 8px 0;
                font-size: 10px;
            }
            .cv-classico .habilidades-sidebar .skill-tag {
                background: rgba(255,255,255,0.2);
                display: inline-block;
                padding: 4px 8px;
                border-radius: 5px;
                font-size: 10px;
                margin: 0 3px 5px 0;
            }
            .cv-classico .cv-main h3 {
                color: #2c3e66;
                font-size: 16px;
                margin: 15px 0 10px;
                padding-bottom: 5px;
                border-bottom: 2px solid #e67e22;
            }
            .cv-classico .cv-main .sobre {
                margin-top: 0;
            }
            .cv-classico .experiencia-item,
            .cv-classico .educacao-item {
                margin-bottom: 12px;
            }
            .cv-classico .experiencia-item h4,
            .cv-classico .educacao-item h4 {
                font-weight: bold;
                font-size: 13px;
                color: #333;
                margin-bottom: 4px;
            }
            .cv-classico .empresa,
            .cv-classico .instituicao {
                color: #e67e22;
                margin: 3px 0;
                font-weight: 500;
            }
            .cv-classico .periodo {
                color: #888;
                font-size: 10px;
                margin-bottom: 5px;
            }

            .experiencia-item,
            .educacao-item {
                margin-bottom: 14px;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }
            .experiencia-item:last-child,
            .educacao-item:last-child {
                border-bottom: none;
            }
            .experiencia-item h4,
            .educacao-item h4 {
                font-size: 14px;
                color: #333;
                margin-bottom: 4px;
            }
            .empresa,
            .instituicao {
                font-weight: 500;
                color: #e67e22;
                margin: 3px 0;
            }
            .periodo {
                color: #888;
                font-size: 10px;
                margin-bottom: 6px;
            }
            .habilidades-container ul {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .habilidades-container li {
                display: inline-block;
                background: #2c3e66;
                color: white;
                padding: 4px 10px;
                border-radius: 15px;
                font-size: 10px;
                margin: 0 5px 5px 0;
            }
            .cv-classico .habilidades-container li {
                background: rgba(255,255,255,0.2);
                border-radius: 5px;
            }
            .cv-foto img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .foto-placeholder {
                font-size: 48px;
                line-height: 1;
            }
            
            .empty-message {
                color: #999;
                font-style: italic;
                padding: 10px;
                text-align: center;
                background: #f9f9f9;
                margin: 10px 0;
            }
        ';
        
        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Currículo - Ngola CV</title>
            <style>' . $pdf_css . $custom_css . '</style>
        </head>
        <body>
            <div class="pdf-wrapper">' . $content . '</div>
        </body>
        </html>';
    }
}
?>