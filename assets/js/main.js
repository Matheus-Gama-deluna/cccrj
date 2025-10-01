// Função para rolar até a seção de cotações
function scrollToCotacao() {
    document.getElementById('cotacao').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

// Animação de entrada dos elementos
window.addEventListener('load', function() {
    const elements = document.querySelectorAll('.card-hover');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.transform = 'translateY(0)';
            el.style.opacity = '1';
        }, index * 200);
    });
});

// Inicializar elementos com animação
document.querySelectorAll('.card-hover').forEach(el => {
    el.style.transform = 'translateY(50px)';
    el.style.opacity = '0';
    el.style.transition = 'all 0.6s ease';
});

// Inicializar as cotações de café ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    // Carregar o componente de cotações
    if (typeof CoffeeQuotes !== 'undefined') {
        new CoffeeQuotes();
    } else {
        // Carregar o script dinamicamente caso não tenha sido carregado
        const script = document.createElement('script');
        script.src = 'assets/js/components/quotes.js';
        script.onload = () => {
            new CoffeeQuotes();
        };
        document.head.appendChild(script);
    }
});