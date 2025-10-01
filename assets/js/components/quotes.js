// Lógica para as cotações de café
class CoffeeQuotes {
    constructor() {
        this.prices = {
            'arabica': 1250.75,
            'conilon': 980.50,
            'especial': 1800.00
        };
        this.init();
    }

    init() {
        // Carregar cotações estáticas com atualização automática
        this.updateDynamicPrices();
        // Atualizar preços automaticamente a cada 10 segundos
        setInterval(() => {
            this.updatePricesRandomly();
            this.updateDynamicPrices();
        }, 10000);
    }

    updatePricesRandomly() {
        // Alterar os preços levemente para simular variação
        for (let key in this.prices) {
            // Variar entre -2% e +2%
            const variation = 1 + (Math.random() * 0.04 - 0.02);
            this.prices[key] = parseFloat((this.prices[key] * variation).toFixed(2));
        }
    }

    updateDynamicPrices() {
        const quotesContainer = document.getElementById('coffee-quotes-container');
        
        if (quotesContainer) {
            // Limpar o container antes de adicionar os novos cards
            quotesContainer.innerHTML = '';
            
            // Configurar grid com base no número de cafés
            const numQuotes = Object.keys(this.prices).length;
            let gridClass = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8';
            
            if (numQuotes === 1) {
                gridClass = 'grid grid-cols-1 gap-8';
            } else if (numQuotes === 2) {
                gridClass = 'grid grid-cols-1 md:grid-cols-2 gap-8';
            }
            
            quotesContainer.className = gridClass;
            
            // Criar cards para cada tipo de café
            const coffeeTypes = {
                'arabica': { name: 'Café Arábica', icon: 'grain', color: 'text-[#D4A574]' },
                'conilon': { name: 'Café Conilon', icon: 'eco', color: 'text-[#A63545]' },
                'especial': { name: 'Café Especial', icon: 'star', color: 'text-[#8B2635]' }
            };
            
            Object.keys(coffeeTypes).forEach(key => {
                const coffee = coffeeTypes[key];
                const card = this.createCoffeeCard(key, { 
                    name: coffee.name, 
                    price: this.prices[key],
                    icon: coffee.icon,
                    color: coffee.color
                });
                quotesContainer.appendChild(card);
            });
        } else {
            console.error('Container de cotações de café não encontrado');
        }
    }

    createCoffeeCard(key, quote) {
        // Criar elementos para o card de cotação
        const card = document.createElement('div');
        card.className = 'bg-gradient-to-br from-white to-[#F5F0E8] p-8 rounded-2xl shadow-lg card-hover border border-[#F5F0E8]';
        
        // Montar o HTML do card
        card.innerHTML = `
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-[#6B4423]">${quote.name}</h3>
                    <p class="text-[#8B2635]">Cotação Atual</p>
                </div>
                <div class="p-3 bg-[#D4A574] bg-opacity-20 rounded-full">
                    <span class="material-icons ${quote.color} text-2xl">${quote.icon}</span>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="text-4xl font-bold text-[#8B2635] price-animation mb-2">
                    R$ <span id="${key}-price">${quote.price.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                    <span class="text-lg text-[#6B4423] font-normal">/ saca 60kg</span>
                </div>
            </div>
            
            <!-- Mini gráfico -->
            <div class="h-16 bg-gradient-to-r from-[#F5F0E8] to-white rounded-lg flex items-end justify-between p-2 mb-4">
                <div class="w-2 bg-[#8B2635] rounded-t" style="height: 60%"></div>
                <div class="w-2 bg-[#992D3D] rounded-t" style="height: 70%"></div>
                <div class="w-2 bg-[#8B2635] rounded-t" style="height: 50%"></div>
                <div class="w-2 bg-[#992D3D] rounded-t" style="height: 80%"></div>
                <div class="w-2 bg-[#A63545] rounded-t" style="height: 90%"></div>
            </div>
            
            <button class="w-full bg-[#8B2635] text-white py-3 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium">
                Ver Histórico Detalhado
            </button>
        `;
        
        return card;
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new CoffeeQuotes();
});