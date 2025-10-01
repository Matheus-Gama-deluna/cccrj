// Lógica para as cotações de café com API de IA
class CoffeeQuotes {
    constructor() {
        this.init();
    }

    init() {
        // Carregar cotações do endpoint de IA
        this.updateDynamicPrices();
        // Atualizar preços automaticamente a cada 30 segundos
        setInterval(() => {
            this.updateDynamicPrices();
        }, 30000);
    }

    // Função para obter as cotações de café via API de IA
    async fetchCoffeeQuotes() {
        try {
            const response = await fetch('../api/get_quotes_openrouter.php');
            const result = await response.json();
            
            if (result.success && result.data) {
                // Converter dados do novo formato para o formato esperado pela interface
                const quotes = [];
                
                // Processar cotações do grupo I
                if (result.data.quotes && result.data.quotes.grupo_i) {
                    for (const [id, quote] of Object.entries(result.data.quotes.grupo_i)) {
                        quotes.push({
                            id: id,
                            name: quote.name,
                            price: parseFloat(quote.price),
                            unit: quote.unit,
                            group: 'Grupo I (Varginha/MG)',
                            icon: 'grain',
                            color: 'text-[#D4A574]'
                        });
                    }
                }
                
                // Processar cotações do grupo II
                if (result.data.quotes && result.data.quotes.grupo_ii) {
                    for (const [id, quote] of Object.entries(result.data.quotes.grupo_ii)) {
                        quotes.push({
                            id: id,
                            name: quote.name,
                            price: parseFloat(quote.price),
                            unit: quote.unit,
                            group: 'Grupo II (Vitória)',
                            icon: 'eco',
                            color: 'text-[#A63545]'
                        });
                    }
                }
                
                // Processar cotações do Conillon
                if (result.data.quotes && result.data.quotes.conillon) {
                    for (const [id, quote] of Object.entries(result.data.quotes.conillon)) {
                        quotes.push({
                            id: id,
                            name: quote.name,
                            price: parseFloat(quote.price),
                            unit: quote.unit,
                            group: 'Conillon (Vitória)',
                            icon: 'nature',
                            color: 'text-[#8B2635]'
                        });
                    }
                }
                
                return {
                    success: true,
                    data: {
                        date: result.data.date || new Date().toLocaleDateString('pt-BR'),
                        base: result.data.base,
                        quotes: quotes
                    }
                };
            } else {
                // Em caso de falha na API, usar cotações simuladas como fallback
                console.warn('Usando cotações simuladas como fallback:', result.error);
                
                // Usar as mesmas cotações simuladas do backend
                const fallbackQuotes = [
                    { 
                        id: 'tipo_c_interno_600_def_11',
                        name: "Tipo C. Interno $600 DEF. 11+$", 
                        price: 1250.75, 
                        unit: "R$/saca 60kg",
                        group: 'Grupo I (Varginha/MG)',
                        icon: 'grain',
                        color: 'text-[#D4A574]'
                    },
                    { 
                        id: 'tipo_6_7_bc_duro',
                        name: "Tipo 6/7, BC Duro", 
                        price: 1100.00, 
                        unit: "R$/saca 60kg",
                        group: 'Grupo I (Varginha/MG)',
                        icon: 'grain',
                        color: 'text-[#D4A574]'
                    },
                    { 
                        id: 'tipo_6_bc_fino',
                        name: "Tipo 6, BC Fino", 
                        price: 1150.50, 
                        unit: "R$/saca 60kg",
                        group: 'Grupo I (Varginha/MG)',
                        icon: 'grain',
                        color: 'text-[#D4A574]'
                    },
                    { 
                        id: 'tipo_c_interno_600_def',
                        name: "Tipo C. Interno 600 DEF.", 
                        price: 1200.00, 
                        unit: "R$/saca 60kg",
                        group: 'Grupo II (Vitória)',
                        icon: 'eco',
                        color: 'text-[#A63545]'
                    },
                    { 
                        id: 'tipo_7_bica',
                        name: "Tipo 7, Bica", 
                        price: 1180.00, 
                        unit: "R$/saca 60kg",
                        group: 'Grupo II (Vitória)',
                        icon: 'eco',
                        color: 'text-[#A63545]'
                    },
                    { 
                        id: 'tipo_5_6_15_16_pronto_embarque',
                        name: "Tipo 5/6 15/16 (Pronto Embarque)", 
                        price: 1160.00, 
                        unit: "R$/saca 60kg",
                        group: 'Grupo II (Vitória)',
                        icon: 'eco',
                        color: 'text-[#A63545]'
                    },
                    { 
                        id: 'tipo_2_3_17_18_pronto_embarque',
                        name: "Tipo 2/3 17/18 (Pronto Embarque)", 
                        price: 1140.00, 
                        unit: "R$/saca 60kg",
                        group: 'Grupo II (Vitória)',
                        icon: 'eco',
                        color: 'text-[#A63545]'
                    },
                    { 
                        id: 'tipo_7_bica_corrida',
                        name: "Tipo 7 Bica Corrida", 
                        price: 980.50, 
                        unit: "R$/saca 60kg",
                        group: 'Conillon (Vitória)',
                        icon: 'nature',
                        color: 'text-[#8B2635]'
                    },
                    { 
                        id: 'tipo_5_6_13_up_pronto_embarque',
                        name: "Tipo 5/6 13 UP Pronto Embarque", 
                        price: 960.00, 
                        unit: "R$/saca 60kg",
                        group: 'Conillon (Vitória)',
                        icon: 'nature',
                        color: 'text-[#8B2635]'
                    }
                ];
                
                return {
                    success: true,
                    data: {
                        date: new Date().toLocaleDateString('pt-BR'),
                        base: 'Varginha/MG ou Vitória (Fictício)',
                        quotes: fallbackQuotes
                    }
                };
            }
        } catch (error) {
            console.error('Erro ao buscar cotações:', error);
            
            // Em caso de erro de rede, usar cotações simuladas
            const fallbackQuotes = [
                { 
                    id: 'tipo_c_interno_600_def_11',
                    name: "Tipo C. Interno $600 DEF. 11+$", 
                    price: 1250.75, 
                    unit: "R$/saca 60kg",
                    group: 'Grupo I (Varginha/MG)',
                    icon: 'grain',
                    color: 'text-[#D4A574]'
                },
                { 
                    id: 'tipo_6_7_bc_duro',
                    name: "Tipo 6/7, BC Duro", 
                    price: 1100.00, 
                    unit: "R$/saca 60kg",
                    group: 'Grupo I (Varginha/MG)',
                    icon: 'grain',
                    color: 'text-[#D4A574]'
                },
                { 
                    id: 'tipo_6_bc_fino',
                    name: "Tipo 6, BC Fino", 
                    price: 1150.50, 
                    unit: "R$/saca 60kg",
                    group: 'Grupo I (Varginha/MG)',
                    icon: 'grain',
                    color: 'text-[#D4A574]'
                },
                { 
                    id: 'tipo_c_interno_600_def',
                    name: "Tipo C. Interno 600 DEF.", 
                    price: 1200.00, 
                    unit: "R$/saca 60kg",
                    group: 'Grupo II (Vitória)',
                    icon: 'eco',
                    color: 'text-[#A63545]'
                },
                { 
                    id: 'tipo_7_bica',
                    name: "Tipo 7, Bica", 
                    price: 1180.00, 
                    unit: "R$/saca 60kg",
                    group: 'Grupo II (Vitória)',
                    icon: 'eco',
                    color: 'text-[#A63545]'
                },
                { 
                    id: 'tipo_5_6_15_16_pronto_embarque',
                    name: "Tipo 5/6 15/16 (Pronto Embarque)", 
                    price: 1160.00, 
                    unit: "R$/saca 60kg",
                    group: 'Grupo II (Vitória)',
                    icon: 'eco',
                    color: 'text-[#A63545]'
                },
                { 
                    id: 'tipo_2_3_17_18_pronto_embarque',
                    name: "Tipo 2/3 17/18 (Pronto Embarque)", 
                    price: 1140.00, 
                    unit: "R$/saca 60kg",
                    group: 'Grupo II (Vitória)',
                    icon: 'eco',
                    color: 'text-[#A63545]'
                },
                { 
                    id: 'tipo_7_bica_corrida',
                    name: "Tipo 7 Bica Corrida", 
                    price: 980.50, 
                    unit: "R$/saca 60kg",
                    group: 'Conillon (Vitória)',
                    icon: 'nature',
                    color: 'text-[#8B2635]'
                },
                { 
                    id: 'tipo_5_6_13_up_pronto_embarque',
                    name: "Tipo 5/6 13 UP Pronto Embarque", 
                    price: 960.00, 
                    unit: "R$/saca 60kg",
                    group: 'Conillon (Vitória)',
                    icon: 'nature',
                    color: 'text-[#8B2635]'
                }
            ];
            
            return {
                success: false,
                error: 'Erro ao buscar cotações: ' + error.message,
                data: {
                    date: new Date().toLocaleDateString('pt-BR'),
                    base: 'Varginha/MG ou Vitória (Fictício)',
                    quotes: fallbackQuotes
                }
            };
        }
    }

    async updateDynamicPrices() {
        const quotesContainer = document.getElementById('coffee-quotes-container');
        const dateElement = document.getElementById('quotes-date');
        const baseElement = document.getElementById('quotes-base');
        
        if (!quotesContainer) {
            console.error('Container de cotações de café não encontrado');
            return;
        }

        const result = await this.fetchCoffeeQuotes();
        
        if (result.success) {
            const { quotes, date, base } = result.data;
            
            // Atualizar a data
            if (dateElement) {
                dateElement.textContent = `Atualizado em: ${date}`;
            }
            
            // Atualizar a base
            if (baseElement) {
                baseElement.textContent = `Base: ${base || 'Desconhecida'}`;
            }
            
            // Limpar o container antes de adicionar os novos cards
            quotesContainer.innerHTML = '';
            
            // Agrupar cotações por grupo
            const groupedQuotes = {};
            quotes.forEach(quote => {
                const group = quote.group || 'Outros';
                if (!groupedQuotes[group]) {
                    groupedQuotes[group] = [];
                }
                groupedQuotes[group].push(quote);
            });
            
            // Criar seções para cada grupo
            for (const [groupName, groupQuotes] of Object.entries(groupedQuotes)) {
                // Título do grupo
                const groupTitle = document.createElement('h3');
                groupTitle.className = 'text-xl font-bold mb-4 text-[#6B4423] pl-2 border-l-4 border-[#8B2635] col-span-full';
                groupTitle.textContent = groupName;
                quotesContainer.appendChild(groupTitle);
                
                // Distribuir as cotações do grupo em colunas
                groupQuotes.forEach(quote => {
                    const card = this.createCoffeeCard(quote.id, quote);
                    quotesContainer.appendChild(card);
                });
                
                // Adicionar espaço entre grupos
                const spacer = document.createElement('div');
                spacer.className = 'col-span-full h-6';
                quotesContainer.appendChild(spacer);
            }
            
            // Configurar grid adequado para múltiplos grupos e cotações
            quotesContainer.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8';
        } else {
            // Em caso de erro, exibir mensagem e manter cotações anteriores
            console.error('Erro ao atualizar cotações:', result.error);
            
            // Exibir mensagem de erro sem substituir as cotações existentes
            if (quotesContainer.children.length === 0) {
                // Se não houver cotações exibidas, mostrar mensagem de erro
                quotesContainer.innerHTML = `
                    <div class="col-span-full text-center py-10 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-red-600 font-medium text-lg">Erro ao carregar cotações: ${result.error}</p>
                        <p class="text-red-500 mt-2">Tentando novamente em 30 segundos...</p>
                    </div>
                `;
            }
        }
    }

    createCoffeeCard(id, quote) {
        // Criar elementos para o card de cotação
        const card = document.createElement('div');
        card.className = 'bg-gradient-to-br from-white to-[#F5F0E8] p-6 rounded-2xl shadow-lg card-hover border border-[#F5F0E8] transition-transform duration-300 hover:scale-[1.02]';
        
        // Montar o HTML do card
        card.innerHTML = `
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-[#6B4423]">${quote.name}</h3>
                    <p class="text-[#8B2635] text-sm">${quote.group}</p>
                </div>
                <div class="p-2 bg-[#D4A574] bg-opacity-20 rounded-full">
                    <span class="material-icons ${quote.color} text-xl">${quote.icon}</span>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="text-3xl font-bold text-[#8B2635] price-animation mb-1">
                    R$ <span id="${id}-price">${quote.price.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                </div>
                <div class="text-sm text-[#6B4423]">${quote.unit}</div>
            </div>
            
            <!-- Mini gráfico -->
            <div class="h-12 bg-gradient-to-r from-[#F5F0E8] to-white rounded-lg flex items-end justify-between p-2 mb-3">
                <div class="w-1.5 bg-[#8B2635] rounded-t" style="height: 60%"></div>
                <div class="w-1.5 bg-[#992D3D] rounded-t" style="height: 70%"></div>
                <div class="w-1.5 bg-[#8B2635] rounded-t" style="height: 50%"></div>
                <div class="w-1.5 bg-[#992D3D] rounded-t" style="height: 80%"></div>
                <div class="w-1.5 bg-[#A63545] rounded-t" style="height: 90%"></div>
            </div>
            
            <button class="w-full bg-[#8B2635] text-white py-2.5 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium text-sm">
                Ver Detalhes
            </button>
        `;
        
        return card;
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new CoffeeQuotes();
});