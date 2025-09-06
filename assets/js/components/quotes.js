// Lógica para as cotações de café
class CoffeeQuotes {
    constructor() {
        this.arabicaElement = document.getElementById('arabica-price');
        this.conilonElement = document.getElementById('conilon-price');
        this.especialElement = document.getElementById('especial-price');
        this.init();
    }

    init() {
        // Iniciar atualização automática a cada 10 segundos
        setInterval(() => this.updatePrices(), 10000);
        // Atualizar imediatamente ao carregar
        this.updatePrices();
    }

    updatePrices() {
        // Simulação de variações de preços
        const arabicaVariation = (Math.random() - 0.5) * 10;
        const conilonVariation = (Math.random() - 0.5) * 8;
        const especialVariation = (Math.random() - 0.5) * 15;

        if (this.arabicaElement) {
            const currentPrice = parseFloat(this.arabicaElement.textContent.replace(',', '.')) || 1250.75;
            const newPrice = Math.max(0, currentPrice + arabicaVariation);
            this.arabicaElement.textContent = newPrice.toLocaleString('pt-BR', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
        }

        if (this.conilonElement) {
            const currentPrice = parseFloat(this.conilonElement.textContent.replace(',', '.')) || 980.50;
            const newPrice = Math.max(0, currentPrice + conilonVariation);
            this.conilonElement.textContent = newPrice.toLocaleString('pt-BR', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
        }

        if (this.especialElement) {
            const currentPrice = parseFloat(this.especialElement.textContent.replace(',', '.')) || 1800.00;
            const newPrice = Math.max(0, currentPrice + especialVariation);
            this.especialElement.textContent = newPrice.toLocaleString('pt-BR', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new CoffeeQuotes();
});