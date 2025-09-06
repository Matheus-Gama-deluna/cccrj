// Função para rolar até a seção de cotações
function scrollToCotacao() {
    document.getElementById('cotacao').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

// Simulação de atualização de preços em tempo real
function atualizarPrecos() {
    const arabica = document.getElementById('arabica-price');
    const conilon = document.getElementById('conilon-price');
    const especial = document.getElementById('especial-price');
    
    // Simulação de pequenas variações nos preços
    const variacao1 = (Math.random() - 0.5) * 5;
    const variacao2 = (Math.random() - 0.5) * 3;
    const variacao3 = (Math.random() - 0.5) * 10;
    
    if (arabica) arabica.textContent = (1250.75 + variacao1).toFixed(2);
    if (conilon) conilon.textContent = (980.50 + variacao2).toFixed(2);
    if (especial) especial.textContent = (1800.00 + variacao3).toFixed(2);
}

// Atualizar preços a cada 10 segundos (simulação)
setInterval(atualizarPrecos, 10000);

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