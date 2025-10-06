// Componente Archive em assets/js/components/archive.js
class ArchiveManager {
    constructor() {
        this.archiveItems = [];
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.hasMoreItems = true;
        this.apiUrl = 'api/archive/list.php'; // URL da API em PHP puro
        this.init();
    }

    async init() {
        await this.loadArchiveItems();
        this.bindEvents();
    }

    async loadArchiveItems() {
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
            this.archiveItems = [...this.archiveItems, ...data.data];
            this.hasMoreItems = data.hasMore;
            
            this.renderArchiveItems();
            
        } catch (error) {
            console.error('Erro ao carregar itens do acervo:', error);
            this.showError(true, 'Não foi possível carregar os itens do acervo. Tente novamente mais tarde.');
        }
    }

    renderArchiveItems() {
        const archiveContainer = document.getElementById('archive-container');
        if (!archiveContainer) return;

        this.archiveItems.forEach(item => {
            const archiveCard = this.createArchiveCard(item);
            archiveContainer.appendChild(archiveCard);
        });

        // Atualizar botão de carregar mais
        const loadMoreButton = document.getElementById('carregar-mais-archive');
        if (loadMoreButton) {
            loadMoreButton.classList.toggle('hidden', !this.hasMoreItems);
        }
    }

    createArchiveCard(item) {
        const article = document.createElement('article');
        article.className = 'bg-gradient-to-br from-white to-[#F5F0E8] rounded-2xl shadow-lg card-hover border border-[#F5F0E8] opacity-0 translate-y-4 transition-all duration-500';
        
        // Gradientes por tipo de item
        const gradients = {
            'Documento': 'from-[#8B2635] to-[#992D3D]',
            'Foto': 'from-[#4A6B8A] to-[#8B2635]',
            'Publicação': 'from-[#D4A574] to-[#8B2635]',
            'Revista': 'from-[#6B4423] to-[#8B2635]',
            'Boletim': 'from-[#8B2635] to-[#992D3D]',
            'Outro': 'from-[#4A6B8A] to-[#D4A574]'
        };
        
        const gradientClass = gradients[item.item_type] || 'from-[#8B2635] to-[#992D3D]';
        
        article.innerHTML = `
            <div class="h-48 bg-gradient-to-br ${gradientClass} relative flex items-center justify-center">
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="material-icons text-white text-6xl">${item.item_type === 'Foto' ? 'photo_library' : item.item_type === 'Documento' ? 'description' : item.item_type === 'Publicação' ? 'menu_book' : item.item_type === 'Revista' ? 'book' : item.item_type === 'Boletim' ? 'assignment' : 'archive'}</span>
                </div>
                <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded-full text-sm font-medium text-[#8B2635]">
                    ${item.item_type || 'Acervo'}
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(item.date)} • ${this.calculateReadingTime(item.content)}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${item.title}</h3>
                <p class="text-[#8B2635] mb-4">${item.description || item.content.substring(0, 150) + '...'}</p>
                <a href="${item.file_path || '#'}" target="_blank" class="read-more-button text-[#8B2635] hover:text-[#992D3D] font-medium flex items-center group">
                    Leia mais 
                    <span class="material-icons ml-1 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
        `;
        
        // Adicionar animação de entrada
        setTimeout(() => {
            article.classList.remove('opacity-0', 'translate-y-4');
        }, 100);

        return article;
    }

    async loadMoreArchiveItems() {
        this.currentPage++;
        await this.loadArchiveItems();
    }

    bindEvents() {
        const loadMoreButton = document.getElementById('carregar-mais-archive');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => this.loadMoreArchiveItems());
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
        const error = document.getElementById('archive-error');
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
    // Apenas inicializar se o elemento archive-container existir na página
    if (document.getElementById('archive-container')) {
        new ArchiveManager();
    }
});