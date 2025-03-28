/**
 * Arquivo de validação de formulários
 * Contém funções para validar inputs de formulários
 */

$(document).ready(function() {
    // Validação do formulário de login
    $('#login-form').submit(function(e) {
        let isValid = true;
        
        // Validação do email
        const email = $('#email').val().trim();
        if (email === '') {
            showError('#email', 'O email é obrigatório');
            isValid = false;
        } else if (!isValidEmail(email)) {
            showError('#email', 'Digite um email válido');
            isValid = false;
        } else {
            removeError('#email');
        }
        
        // Validação da senha
        const senha = $('#senha').val();
        if (senha === '') {
            showError('#senha', 'A senha é obrigatória');
            isValid = false;
        } else {
            removeError('#senha');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Validação do formulário de cadastro
    $('#register-form').submit(function(e) {
        let isValid = true;
        
        // Validação do nome
        const nome = $('#nome').val().trim();
        if (nome === '') {
            showError('#nome', 'O nome é obrigatório');
            isValid = false;
        } else {
            removeError('#nome');
        }
        
        // Validação do email
        const email = $('#email').val().trim();
        if (email === '') {
            showError('#email', 'O email é obrigatório');
            isValid = false;
        } else if (!isValidEmail(email)) {
            showError('#email', 'Digite um email válido');
            isValid = false;
        } else {
            removeError('#email');
        }
        
        // Validação da senha
        const senha = $('#senha').val();
        if (senha === '') {
            showError('#senha', 'A senha é obrigatória');
            isValid = false;
        } else if (senha.length < 6) {
            showError('#senha', 'A senha deve ter pelo menos 6 caracteres');
            isValid = false;
        } else {
            removeError('#senha');
        }
        
        // Validação da confirmação de senha
        const confirmarSenha = $('#confirmar-senha').val();
        if (confirmarSenha === '') {
            showError('#confirmar-senha', 'Confirme sua senha');
            isValid = false;
        } else if (confirmarSenha !== senha) {
            showError('#confirmar-senha', 'As senhas não coincidem');
            isValid = false;
        } else {
            removeError('#confirmar-senha');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Validação do formulário de filme
    $('#filme-form').submit(function(e) {
        let isValid = true;
        
        // Validação do título
        const titulo = $('#titulo').val().trim();
        if (titulo === '') {
            showError('#titulo', 'O título é obrigatório');
            isValid = false;
        } else {
            removeError('#titulo');
        }
        
        // Validação do ano de lançamento
        const anoLancamento = $('#ano_lancamento').val().trim();
        if (anoLancamento !== '' && !isValidYear(anoLancamento)) {
            showError('#ano_lancamento', 'Digite um ano válido');
            isValid = false;
        } else {
            removeError('#ano_lancamento');
        }
        
        // Validação da duração
        const duracao = $('#duracao').val().trim();
        if (duracao !== '' && !isValidDuration(duracao)) {
            showError('#duracao', 'Digite uma duração válida em minutos');
            isValid = false;
        } else {
            removeError('#duracao');
        }
        
        // Validação da imagem
        const imagem = $('#imagem')[0].files[0];
        if (imagem) {
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!allowedTypes.includes(imagem.type)) {
                showError('#imagem', 'Formato de imagem inválido. Use JPG, PNG ou GIF');
                isValid = false;
            } else if (imagem.size > maxSize) {
                showError('#imagem', 'A imagem deve ter no máximo 5MB');
                isValid = false;
            } else {
                removeError('#imagem');
            }
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Validação do formulário de gênero
    $('#genero-form').submit(function(e) {
        let isValid = true;
        
        // Validação do nome do gênero
        const nome = $('#nome').val().trim();
        if (nome === '') {
            showError('#nome', 'O nome do gênero é obrigatório');
            isValid = false;
        } else {
            removeError('#nome');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});

// Função para exibir mensagem de erro
function showError(field, message) {
    $(field).addClass('is-invalid');
    
    // Verifica se já existe uma mensagem de erro
    if ($(field).next('.form-error').length === 0) {
        $(field).after(`<div class="form-error">${message}</div>`);
    } else {
        $(field).next('.form-error').text(message);
    }
}

// Função para remover mensagem de erro
function removeError(field) {
    $(field).removeClass('is-invalid');
    $(field).next('.form-error').remove();
}

// Função para validar email
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Função para validar ano
function isValidYear(year) {
    const currentYear = new Date().getFullYear();
    const yearNum = parseInt(year);
    return !isNaN(yearNum) && yearNum >= 1900 && yearNum <= currentYear + 5;
}

// Função para validar duração
function isValidDuration(duration) {
    const durationNum = parseInt(duration);
    return !isNaN(durationNum) && durationNum > 0 && durationNum < 1000;
}