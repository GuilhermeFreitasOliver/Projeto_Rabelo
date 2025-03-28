/**
 * Arquivo de slideshow
 * Contém funções para controlar o slideshow na página inicial
 */

$(document).ready(function() {
    // Variáveis para controlar o slideshow
    let currentSlide = 0;
    const slides = $('.slideshow-item');
    const slideCount = slides.length;
    let slideInterval;
    
    // Função para mostrar um slide específico
    function showSlide(index) {
        // Remove a classe active de todos os slides
        slides.removeClass('active');
        
        // Adiciona a classe active ao slide atual
        slides.eq(index).addClass('active');
    }
    
    // Função para avançar para o próximo slide
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slideCount;
        showSlide(currentSlide);
    }
    
    // Função para voltar para o slide anterior
    function prevSlide() {
        currentSlide = (currentSlide - 1 + slideCount) % slideCount;
        showSlide(currentSlide);
    }
    
    // Inicializa o slideshow
    function initSlideshow() {
        // Mostra o primeiro slide
        showSlide(currentSlide);
        
        // Inicia o intervalo para trocar os slides automaticamente
        slideInterval = setInterval(nextSlide, 5000);
        
        // Adiciona eventos para os botões de navegação
        $('.slideshow-prev').click(function() {
            clearInterval(slideInterval);
            prevSlide();
            slideInterval = setInterval(nextSlide, 5000);
        });
        
        $('.slideshow-next').click(function() {
            clearInterval(slideInterval);
            nextSlide();
            slideInterval = setInterval(nextSlide, 5000);
        });
        
        // Pausa o slideshow quando o mouse está sobre ele
        $('.slideshow').hover(
            function() {
                clearInterval(slideInterval);
            },
            function() {
                slideInterval = setInterval(nextSlide, 5000);
            }
        );
    }
    
    // Inicializa o slideshow se houver slides na página
    if (slideCount > 0) {
        initSlideshow();
    }
});