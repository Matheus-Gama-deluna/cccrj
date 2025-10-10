// Componente ClippingManager em assets/js/components/clipping.js
class ClippingManager {
    constructor() {
        this.clippings = [];
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.hasMoreClippings = true;
        this.apiUrl = 'api/json/clipping/list.php'; // Novo endpoint JSON
        this.init();
    }

    async init() {
        await this.loadClippings();
        this.bindEvents();
    }

    async loadClippings() {
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
            this.clippings = [...this.clippings, ...data.data];
            this.hasMoreClippings = data.hasMore;
            
            this.renderClippings();
            
        } catch (error) {
            console.error('Erro ao carregar clipping:', error);
            this.showError(true, 'Não foi possível carregar o clipping. Tente novamente mais tarde.');
        }
    }

    renderClippings() {
        const clippingContainer = document.getElementById('clipping-container');
        if (!clippingContainer) return;

        this.clippings.forEach(clipping => {
            const clippingCard = this.createClippingCard(clipping);
            clippingContainer.appendChild(clippingCard);
        });

        // Atualizar botão de carregar mais
        const loadMoreButton = document.getElementById('carregar-mais-clipping');
        if (loadMoreButton) {
            loadMoreButton.classList.toggle('hidden', !this.hasMoreClippings);
        }
    }

    createClippingCard(clipping) {
        const article = document.createElement('article');
        article.className = 'bg-white rounded-2xl shadow-lg overflow-hidden card-hover opacity-0 translate-y-4 transition-all duration-500';
        
        // Gradientes por categoria
        const gradients = {
            'Notícia': 'from-[#4A6B8A] to-[#8B2635]',
            'Artigo': 'from-[#D4A574] to-[#8B2635]',
            'Reportagem': 'from-[#8B2635] to-[#992D3D]',
            'Entrevista': 'from-[#6B4423] to-[#8B2635]'
        };
        
        const gradientClass = gradients[clipping.category] || 'from-[#8B2635] to-[#992D3D]';
        
        // Criar elementos manualmente
        const gradientDiv = document.createElement('div');
        gradientDiv.className = `h-48 bg-gradient-to-br ${gradientClass} relative`;
        
        const centerDiv = document.createElement('div');
        centerDiv.className = 'absolute inset-0 flex items-center justify-center';
        
        const iconSpan = document.createElement('span');
        iconSpan.className = 'material-icons text-white text-6xl';
        iconSpan.textContent = 'article';
        
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'absolute top-4 left-4 bg-white px-3 py-1 rounded-full text-sm font-medium text-[#8B2635]';
        categoryDiv.textContent = clipping.category || 'Clipping';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'p-6';
        
        const dateDiv = document.createElement('div');
        dateDiv.className = 'text-sm text-[#6B4423] mb-2';
        dateDiv.textContent = `${this.formatDate(clipping.date)} • ${this.calculateReadingTime(clipping.content)}`;
        
        const titleH3 = document.createElement('h3');
        titleH3.className = 'text-xl font-bold text-[#6B4423] mb-3';
        titleH3.textContent = clipping.title;
        
        // Criar elementos restantes
        const summaryP = document.createElement('p');
        summaryP.className = 'text-[#8B2635] mb-4';
        summaryP.textContent = clipping.summary || 
            (clipping.content ? clipping.content.substring(0, 150) + '...' : '');
        
        const readMoreLink = document.createElement('a');
        readMoreLink.href = clipping.source_url || '#';
        readMoreLink.target = '_blank';
        readMoreLink.className = 'read-more-button text-[#8B2635] hover:text-[#992D3D] font-medium flex items-center group';
        readMoreLink.textContent = 'Leia mais ';
        
        // Criar ícone de seta
        const arrowIcon = document.createElement('span');
        arrowIcon.className = 'material-icons text-sm ml-1 transform group-hover:translate-x-1 transition-transform';
        arrowIcon.textContent = 'arrow_forward';
        
        // Adicionar ícone ao link
        readMoreLink.appendChild(arrowIcon);
        
        // Montar a estrutura
        centerDiv.appendChild(iconSpan);
        
        gradientDiv.appendChild(centerDiv);
        gradientDiv.appendChild(categoryDiv);
        
        contentDiv.appendChild(dateDiv);
        contentDiv.appendChild(titleH3);
        contentDiv.appendChild(summaryP);
        contentDiv.appendChild(readMoreLink);
        
        article.appendChild(gradientDiv);
        article.appendChild(contentDiv);
        
        // Adicionar animação de entrada
        setTimeout(() => {
            article.classList.remove('opacity-0', 'translate-y-4');
        }, 100);

        return article;
    }

    async loadMoreClippings() {
        this.currentPage++;
        await this.loadClippings();
    }

    bindEvents() {
        const loadMoreButton = document.getElementById('carregar-mais-clipping');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => this.loadMoreClippings());
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

    calculateReadingTime(text) {
        const wordsPerMinute = 200;
        const words = text.split(/\s+/).length;
        const minutes = Math.ceil(words / wordsPerMinute);
        return `${minutes} min de leitura`;
    }

    showError(show, message = null) {
        const error = document.getElementById('clipping-error');
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
    // Apenas inicializar se o elemento clipping-container existir na página
    if (document.getElementById('clipping-container')) {
        new ClippingManager();
    }
});