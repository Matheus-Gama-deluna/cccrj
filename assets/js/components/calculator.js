// Lógica para a calculadora de conversão
class CoffeeCalculator {
    constructor() {
        this.sacasInput = document.getElementById('sacas');
        this.tipoCafeSelect = document.getElementById('tipo-cafe');
        this.valorTotalElement = document.getElementById('valor-total');
        this.init();
    }

    init() {
        // Adicionar eventos aos elementos
        if (this.sacasInput) {
            this.sacasInput.addEventListener('input', () => this.calculate());
        }
        
        if (this.tipoCafeSelect) {
            this.tipoCafeSelect.addEventListener('change', () => this.calculate());
        }
        
        // Calcular valor inicial
        this.calculate();
    }

    calculate() {
        if (!this.sacasInput || !this.tipoCafeSelect || !this.valorTotalElement) return;
        
        const sacas = parseFloat(this.sacasInput.value) || 0;
        const preco = parseFloat(this.tipoCafeSelect.value) || 0;
        const total = sacas * preco;
        
        this.valorTotalElement.textContent = total.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Verificar se os elementos existem antes de inicializar
    if (document.getElementById('sacas') && document.getElementById('tipo-cafe') && document.getElementById('valor-total')) {
        new CoffeeCalculator();
    }
});