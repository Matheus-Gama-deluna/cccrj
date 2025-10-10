// Componente HistoryManager em assets/js/components/history.js
class HistoryManager {
    constructor() {
        this.events = [];
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.hasMoreEvents = true;
        this.apiUrl = 'api/json/history/list.php'; // Novo endpoint JSON
        this.init();
    }

    async init() {
        await this.loadEvents();
        this.bindEvents();
    }

    async loadEvents() {
        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage
            });
            
            const response = await fetch(`${this.apiUrl}?${params}`);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const data = await response.json();
            this.events = [...this.events, ...data.data];
            this.hasMoreEvents = data.hasMore;
            
            this.renderEvents();
            
        } catch (error) {
            console.error('Erro ao carregar eventos históricos:', error);
            this.showError(true, 'Não foi possível carregar os eventos históricos. Tente novamente mais tarde.');
        }
    }

    renderEvents() {
        const historyContainer = document.getElementById('history-container');
        if (!historyContainer) return;

        this.events.forEach(event => {
            const eventElement = this.createEventElement(event);
            historyContainer.appendChild(eventElement);
        });

        // Atualizar botão de carregar mais
        const loadMoreButton = document.getElementById('carregar-mais-events');
        if (loadMoreButton) {
            loadMoreButton.classList.toggle('hidden', !this.hasMoreEvents);
        }
    }

    createEventElement(event) {
        const div = document.createElement('div');
        div.className = 'history-event bg-white p-6 rounded-lg shadow mb-4 border-l-4 border-[#8B2635] opacity-0 translate-y-4 transition-all duration-500';
        
        // Criar elementos manualmente
        const flexContainer = document.createElement('div');
        flexContainer.className = 'flex items-start';
        
        // Ícone à esquerda
        const iconContainer = document.createElement('div');
        iconContainer.className = 'flex-shrink-0 mr-4';
        
        const iconCircle = document.createElement('div');
        iconCircle.className = 'w-12 h-12 rounded-full bg-[#8B2635] flex items-center justify-center';
        
        const icon = document.createElement('span');
        icon.className = 'material-icons text-white';
        icon.textContent = 'history';
        
        // Conteúdo à direita
        const contentContainer = document.createElement('div');
        contentContainer.className = 'flex-grow';
        
        // Data e tipo do evento
        const dateElement = document.createElement('div');
        dateElement.className = 'text-sm font-semibold text-[#8B2635]';
        dateElement.textContent = `${this.formatDate(event.date)} • ${event.event_type}`;
        
        // Título do evento
        const titleElement = document.createElement('h3');
        titleElement.className = 'text-xl font-bold text-[#6B4423] mt-1 mb-2';
        titleElement.textContent = event.title || '';
        
        // Descrição do evento
        const descriptionElement = document.createElement('p');
        descriptionElement.className = 'text-[#8B2635]';
        descriptionElement.textContent = event.description || 
            (event.content ? event.content.substring(0, 200) + '...' : '');
        
        // Montar a estrutura
        iconCircle.appendChild(icon);
        iconContainer.appendChild(iconCircle);
        
        contentContainer.appendChild(dateElement);
        contentContainer.appendChild(titleElement);
        contentContainer.appendChild(descriptionElement);
        
        // Adicionar imagem se existir
        if (event.image_url) {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'mt-4';
            const img = document.createElement('img');
            img.src = event.image_url;
            img.alt = event.title || '';
            img.className = 'rounded-lg max-w-full h-auto';
            imgContainer.appendChild(img);
            contentContainer.appendChild(imgContainer);
        }
        
        flexContainer.appendChild(iconContainer);
        flexContainer.appendChild(contentContainer);
        div.appendChild(flexContainer);
        
        // Adicionar animação de entrada
        setTimeout(() => {
            div.classList.remove('opacity-0', 'translate-y-4');
        }, 100);

        return div;
    }

    async loadMoreEvents() {
        this.currentPage++;
        await this.loadEvents();
    }

    bindEvents() {
        const loadMoreButton = document.getElementById('carregar-mais-events');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => this.loadMoreEvents());
        }
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const date = new Date(dateString);
        
        // Formatar mês em português
        const months = {
            'January': 'janeiro', 'February': 'fevereiro', 'March': 'março',
            'April': 'abril', 'May': 'maio', 'June': 'junho',
            'July': 'julho', 'August': 'agosto', 'September': 'setembro',
            'October': 'outubro', 'November': 'novembro', 'December': 'dezembro'
        };
        
        const month = months[date.toLocaleString('en-US', { month: 'long' })] || date.toLocaleString('pt-BR', { month: 'long' });
        return `${date.getDate()} de ${month} de ${date.getFullYear()}`;
    }

    showError(show, message = null) {
        const error = document.getElementById('history-error');
        if (error) {
            if (message) {
                const messageElement = error.querySelector('.error-message');
                if (messageElement) {
                    messageElement.textContent = message;
                }
            }
            error.classList.toggle('hidden', !show);
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Apenas inicializar se o elemento history-container existir na página
    if (document.getElementById('history-container')) {
        new HistoryManager();
    }
});