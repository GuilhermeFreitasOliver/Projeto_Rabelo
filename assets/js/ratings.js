/**
 * Script para o sistema de avaliação por estrelas
 * Permite interação com as estrelas para classificação de filmes
 */

$(document).ready(function() {
    // Variáveis para o sistema de estrelas
    const starsContainer = $('.stars-container');
    const stars = starsContainer.find('.star');
    const starsInput = $('#estrelas');
    
    // Função para atualizar a visualização das estrelas
    function updateStars(rating) {
        stars.removeClass('active hover');
        stars.each(function(index) {
            if (index < rating) {
                $(this).addClass('active');
            }
        });
        starsInput.val(rating);
    }
    
    // Evento de hover nas estrelas
    stars.hover(
        function() {
            const value = $(this).data('value');
            stars.removeClass('hover');
            stars.each(function(index) {
                if (index < value) {
                    $(this).addClass('hover');
                }
            });
        },
        function() {
            stars.removeClass('hover');
        }
    );
    
    // Evento de clique nas estrelas
    stars.click(function() {
        const value = $(this).data('value');
        updateStars(value);
    });
    
    // Inicializa as estrelas com o valor atual (se existir)
    const initialValue = starsInput.val();
    if (initialValue > 0) {
        updateStars(initialValue);
    }
    
    // Validação do formulário de avaliação
    $('#avaliacao-form').submit(function(e) {
        const rating = parseInt(starsInput.val());
        
        if (rating < 1 || isNaN(rating)) {
            e.preventDefault();
            alert('Por favor, selecione uma classificação de 1 a 5 estrelas.');
            return false;
        }
        
        return true;
    });
});