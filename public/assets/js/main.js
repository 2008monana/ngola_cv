// public/assets/js/main.js

// Notificações
$(document).ready(function() {
    // Auto-fechar alertas após 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Tooltips
    $('[data-tooltip]').each(function() {
        $(this).attr('title', $(this).data('tooltip'));
    });
});

// Função para mostrar loading
function showLoading() {
    $('#loadingOverlay').fadeIn();
}

function hideLoading() {
    $('#loadingOverlay').fadeOut();
}

// Validação de formulários
function validateForm(formId) {
    let isValid = true;
    $(`#${formId} input[required], #${formId} textarea[required]`).each(function() {
        if (!$(this).val().trim()) {
            $(this).addClass('error');
            isValid = false;
        } else {
            $(this).removeClass('error');
        }
    });
    return isValid;
}

// Máscaras para campos
function applyMasks() {
    // Telefone angolano
    $('#telefone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 9) value = value.slice(0, 9);
        $(this).val(value);
    });
}

// Debounce para evitar muitas requisições
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}