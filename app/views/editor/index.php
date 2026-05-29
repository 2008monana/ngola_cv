<?php
// app/views/editor/index.php - Editor de Currículo
$user = Auth::getUser();
$db = (new Database())->getConnection();

$templateModel = new Template($db);
$resumeModel = new Resume($db);

// Buscar templates disponíveis
$templates = $templateModel->getByPlan($user['plano']);

$resume_id = $_GET['id'] ?? null;
$resume_data = null;
$dados = [
    'nome' => '',
    'profissao' => '',
    'email' => $user['email'],
    'telefone' => '',
    'endereco' => '',
    'sobre' => '',
    'foto_url' => '',
    'experiencias' => [],
    'educacoes' => [],
    'habilidades' => []
];

$selected_template_id = $templates[0]['id'] ?? 1;

if ($resume_id) {
    $resume_data = $resumeModel->findById($resume_id, $user['id']);
    if ($resume_data) {
        $selected_template_id = $resume_data['template_id'];
        $dados = json_decode($resume_data['dados_json'], true);
        if (!is_array($dados)) {
            $dados = [];
        }
        $dados['experiencias'] = $dados['experiencias'] ?? [];
        $dados['educacoes'] = $dados['educacoes'] ?? [];
        $dados['habilidades'] = $dados['habilidades'] ?? [];
        $dados['foto_url'] = $dados['foto_url'] ?? '';
    }
}
?>

<div class="editor-container">
    <div class="editor-toolbar">
        <div class="editor-actions">
            <button type="button" id="saveBtn" class="btn-primary">
                <i class="fas fa-save"></i> Salvar Currículo
            </button>
            <button type="button" id="previewBtn" class="btn-secondary">
                <i class="fas fa-eye"></i> Atualizar Preview
            </button>
            <button type="button" id="exportPdfBtn" class="btn-pdf" <?php echo !$resume_id ? 'disabled' : ''; ?>>
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </div>
        
        <div class="template-selector">
            <label><i class="fas fa-layer-group"></i> Template:</label>
            <select id="templateSelect">
                <?php foreach ($templates as $template): ?>
                <option value="<?php echo $template['id']; ?>" 
                    data-plan="<?php echo $template['plano_requerido']; ?>"
                    <?php echo ($selected_template_id == $template['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($template['nome']); ?>
                    <?php if ($template['plano_requerido'] != 'gratuito'): ?>
                    (<?php echo ucfirst($template['plano_requerido']); ?>)
                    <?php endif; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="editor-content">
        <!-- Formulário de Edição -->
        <div class="editor-form">
            <form id="curriculoForm">
                <input type="hidden" id="resumeId" value="<?php echo $resume_id; ?>">
                
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Dados Pessoais</h3>
                    
                    <div class="form-group">
                        <label><i class="fas fa-camera"></i> URL da Foto (opcional)</label>
                        <input type="text" id="foto_url" name="foto_url" 
                               value="<?php echo htmlspecialchars($dados['foto_url'] ?? ''); ?>"
                               placeholder="Ex: https://exemplo.com/minha-foto.jpg">
                    </div>
                    
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" id="nome" name="nome" 
                               value="<?php echo htmlspecialchars($dados['nome'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Profissão / Cargo</label>
                        <input type="text" id="profissao" name="profissao" 
                               value="<?php echo htmlspecialchars($dados['profissao'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($dados['email'] ?? $user['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" id="telefone" name="telefone" 
                                   value="<?php echo htmlspecialchars($dados['telefone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" id="endereco" name="endereco" 
                               value="<?php echo htmlspecialchars($dados['endereco'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Sobre / Resumo Profissional</label>
                        <textarea id="sobre" rows="4"><?php echo htmlspecialchars($dados['sobre'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-briefcase"></i> Experiência Profissional</h3>
                    <div id="experienciasContainer">
                        <?php if (!empty($dados['experiencias'])): ?>
                            <?php foreach ($dados['experiencias'] as $index => $exp): ?>
                            <div class="item-card">
                                <div class="item-header">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?php echo htmlspecialchars($exp['cargo'] ?? 'Nova Experiência'); ?></span>
                                    <button type="button" class="remove-item"><i class="fas fa-trash"></i></button>
                                </div>
                                <div class="item-body">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Cargo</label>
                                            <input type="text" name="experiencias[<?php echo $index; ?>][cargo]" 
                                                   value="<?php echo htmlspecialchars($exp['cargo'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Empresa</label>
                                            <input type="text" name="experiencias[<?php echo $index; ?>][empresa]" 
                                                   value="<?php echo htmlspecialchars($exp['empresa'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Período</label>
                                        <input type="text" name="experiencias[<?php echo $index; ?>][periodo]" 
                                               value="<?php echo htmlspecialchars($exp['periodo'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Descrição</label>
                                        <textarea name="experiencias[<?php echo $index; ?>][descricao]" rows="3"><?php echo htmlspecialchars($exp['descricao'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="addExperiencia" class="btn-add">
                        <i class="fas fa-plus"></i> Adicionar Experiência
                    </button>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-graduation-cap"></i> Formação Acadêmica</h3>
                    <div id="educacoesContainer">
                        <?php if (!empty($dados['educacoes'])): ?>
                            <?php foreach ($dados['educacoes'] as $index => $edu): ?>
                            <div class="item-card">
                                <div class="item-header">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span><?php echo htmlspecialchars($edu['curso'] ?? 'Nova Formação'); ?></span>
                                    <button type="button" class="remove-item"><i class="fas fa-trash"></i></button>
                                </div>
                                <div class="item-body">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Curso</label>
                                            <input type="text" name="educacoes[<?php echo $index; ?>][curso]" 
                                                   value="<?php echo htmlspecialchars($edu['curso'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Instituição</label>
                                            <input type="text" name="educacoes[<?php echo $index; ?>][instituicao]" 
                                                   value="<?php echo htmlspecialchars($edu['instituicao'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Período</label>
                                        <input type="text" name="educacoes[<?php echo $index; ?>][periodo]" 
                                               value="<?php echo htmlspecialchars($edu['periodo'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="addEducacao" class="btn-add">
                        <i class="fas fa-plus"></i> Adicionar Formação
                    </button>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-code"></i> Habilidades</h3>
                    <div class="form-group">
                        <label>Digite as habilidades separadas por vírgula</label>
                        <input type="text" id="habilidadesInput" 
                               value="<?php echo isset($dados['habilidades']) ? implode(', ', $dados['habilidades']) : ''; ?>"
                               placeholder="Ex: PHP, JavaScript, Gestão de Projetos">
                    </div>
                </div>
            </form>
        </div>
        
        <div class="editor-preview">
            <h3><i class="fas fa-eye"></i> Pré-visualização</h3>
            <div id="previewContent" class="preview-content">
                <div class="loading-preview">
                    <i class="fas fa-spinner fa-spin"></i> Carregando...
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let previewTimeout;
    
    function getFormData() {
        const habilidades = $('#habilidadesInput').val().split(',').map(h => h.trim()).filter(h => h);
        
        const experiencias = [];
        $('#experienciasContainer .item-card').each(function(index) {
            const cargo = $(this).find('input[name*="[cargo]"]').val();
            if (cargo) {
                experiencias.push({
                    cargo: cargo,
                    empresa: $(this).find('input[name*="[empresa]"]').val(),
                    periodo: $(this).find('input[name*="[periodo]"]').val(),
                    descricao: $(this).find('textarea').val()
                });
            }
        });
        
        const educacoes = [];
        $('#educacoesContainer .item-card').each(function(index) {
            const curso = $(this).find('input[name*="[curso]"]').val();
            if (curso) {
                educacoes.push({
                    curso: curso,
                    instituicao: $(this).find('input[name*="[instituicao]"]').val(),
                    periodo: $(this).find('input[name*="[periodo]"]').val()
                });
            }
        });
        
        return {
            nome: $('#nome').val(),
            profissao: $('#profissao').val(),
            email: $('#email').val(),
            telefone: $('#telefone').val(),
            endereco: $('#endereco').val(),
            sobre: $('#sobre').val(),
            foto_url: $('#foto_url').val(),
            experiencias: experiencias,
            educacoes: educacoes,
            habilidades: habilidades
        };
    }
    
    function updatePreview() {
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(function() {
            const data = getFormData();
            const templateId = $('#templateSelect').val();
            
            $('#previewContent').html('<div class="loading-preview"><i class="fas fa-spinner fa-spin"></i> Atualizando...</div>');
            
            $.ajax({
                url: 'index.php?page=api-preview',
                method: 'POST',
                data: {
                    dados: data,
                    template_id: templateId
                },
                success: function(response) {
                    $('#previewContent').html(response);
                },
                error: function() {
                    $('#previewContent').html('<div class="loading-preview"><i class="fas fa-exclamation-triangle"></i> Erro ao carregar preview</div>');
                }
            });
        }, 500);
    }
    
    function saveResume() {
    const data = getFormData();
    const resumeId = $('#resumeId').val();
    const templateId = $('#templateSelect').val();
    const titulo = data.nome ? data.nome + ' - Currículo' : 'Meu Currículo';
    
    console.log('=== DADOS ENVIADOS ===');
    console.log('Resume ID:', resumeId);
    console.log('Template ID SELECIONADO:', templateId);
    console.log('Título:', titulo);
        
        if (!data.nome) {
            alert('⚠️ Por favor, preencha o nome completo.');
            return;
        }
        
        const saveBtn = $('#saveBtn');
        const originalText = saveBtn.html();
        saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Salvando...').prop('disabled', true);
        
        console.log('Salvando - Template ID:', templateId);
        
        $.ajax({
            url: 'index.php?page=api-salvar-curriculo',
            method: 'POST',
            data: {
                id: resumeId,
                titulo: titulo,
                template_id: templateId,
                dados_json: JSON.stringify(data)
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('✅ Currículo salvo com sucesso!');
                    if (!resumeId || resumeId === 'null') {
                        window.location.href = 'index.php?page=editor&id=' + response.id;
                    } else {
                        $('#resumeId').val(response.id);
                        $('#exportPdfBtn').prop('disabled', false);
                    }
                } else {
                    alert('❌ Erro ao salvar: ' + (response.error || 'Tente novamente'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro:', error);
                alert('❌ Erro de conexão: ' + error);
            },
            complete: function() {
                saveBtn.html(originalText).prop('disabled', false);
            }
        });
    }
    
    function exportPDF() {
        const resumeId = $('#resumeId').val();
        if (!resumeId || resumeId === 'null') {
            alert('⚠️ Salve o currículo primeiro antes de exportar PDF.');
            return;
        }
        window.open('index.php?page=api-exportar-pdf&id=' + resumeId, '_blank');
    }
    
    // Eventos
    $('#saveBtn').click(saveResume);
    $('#previewBtn').click(updatePreview);
    $('#exportPdfBtn').click(exportPDF);
    $('#templateSelect').on('change', function() {
        console.log('Template alterado para:', $(this).val());
        updatePreview();
    });
    $('input, textarea').on('input', updatePreview);
    
    // Adicionar/Remover experiências
    $('#addExperiencia').click(function() {
        const index = Date.now();
        const html = `
            <div class="item-card">
                <div class="item-header">
                    <i class="fas fa-briefcase"></i>
                    <span>Nova Experiência</span>
                    <button type="button" class="remove-item"><i class="fas fa-trash"></i></button>
                </div>
                <div class="item-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Cargo</label>
                            <input type="text" name="experiencias[${index}][cargo]" placeholder="Ex: Desenvolvedor">
                        </div>
                        <div class="form-group">
                            <label>Empresa</label>
                            <input type="text" name="experiencias[${index}][empresa]" placeholder="Ex: Empresa XYZ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Período</label>
                        <input type="text" name="experiencias[${index}][periodo]" placeholder="Ex: Jan 2020 - Dez 2023">
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="experiencias[${index}][descricao]" rows="3"></textarea>
                    </div>
                </div>
            </div>
        `;
        $('#experienciasContainer').append(html);
        updatePreview();
    });
    
    $('#addEducacao').click(function() {
        const index = Date.now();
        const html = `
            <div class="item-card">
                <div class="item-header">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Nova Formação</span>
                    <button type="button" class="remove-item"><i class="fas fa-trash"></i></button>
                </div>
                <div class="item-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Curso</label>
                            <input type="text" name="educacoes[${index}][curso]" placeholder="Ex: Engenharia">
                        </div>
                        <div class="form-group">
                            <label>Instituição</label>
                            <input type="text" name="educacoes[${index}][instituicao]" placeholder="Ex: Universidade">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Período</label>
                        <input type="text" name="educacoes[${index}][periodo]" placeholder="Ex: 2015 - 2019">
                    </div>
                </div>
            </div>
        `;
        $('#educacoesContainer').append(html);
        updatePreview();
    });
    
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.item-card').remove();
        updatePreview();
    });
    
    $(document).on('click', '.item-header', function(e) {
        if (!$(e.target).closest('.remove-item').length) {
            $(this).siblings('.item-body').toggleClass('collapsed');
        }
    });
    
    // Inicializar
    updatePreview();
});
</script>

<style>
.editor-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
.editor-toolbar { background: white; border-radius: 12px; padding: 15px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.editor-actions { display: flex; gap: 15px; }
.btn-pdf { background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
.btn-pdf:disabled { opacity: 0.5; cursor: not-allowed; }
.template-selector select { padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; }
.editor-content { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
.editor-form { background: white; border-radius: 12px; padding: 25px; max-height: calc(100vh - 150px); overflow-y: auto; }
.form-section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
.form-section h3 { color: #2c3e66; margin-bottom: 20px; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
.form-group input, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.item-card { background: #f9f9f9; border-radius: 10px; margin-bottom: 15px; overflow: hidden; }
.item-header { background: #f0f0f0; padding: 12px 15px; display: flex; align-items: center; gap: 10px; cursor: pointer; }
.item-header span { flex: 1; font-weight: 500; }
.remove-item { background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
.item-body { padding: 15px; }
.item-body.collapsed { display: none; }
.btn-add { background: #2c3e66; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
.editor-preview { background: white; border-radius: 12px; padding: 25px; position: sticky; top: 100px; max-height: calc(100vh - 150px); overflow-y: auto; }
.preview-content { min-height: 400px; background: #fafafa; border-radius: 8px; padding: 20px; }
.loading-preview { text-align: center; padding: 50px; color: #999; }
@media (max-width: 1024px) { .editor-content { grid-template-columns: 1fr; } .editor-preview { position: static; } }
</style>