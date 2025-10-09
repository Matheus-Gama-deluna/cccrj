// Componente PublicationsManager em assets/js/components/publications.js
class PublicationsManager {
    constructor() {
        this.publications = [];
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.hasMorePublications = true;
        this.apiUrl = 'api/json/publications/list.php'; // Novo endpoint JSON
        this.init();
    }

    async init() {
        await this.loadPublications();
        this.bindEvents();
    }

    async loadPublications() {
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
            this.publications = [...this.publications, ...data.data];
            this.hasMorePublications = (this.publications.length < data.total);
            
            this.renderPublications();
            
        } catch (error) {
            console.error('Erro ao carregar publicações:', error);
            this.showError(true, 'Não foi possível carregar as publicações. Tente novamente mais tarde.');
        }
    }

    renderPublications() {
        const publicationsContainer = document.getElementById('publications-container');
        if (!publicationsContainer) return;

        this.publications.forEach(publication => {
            const publicationCard = this.createPublicationCard(publication);
            publicationsContainer.appendChild(publicationCard);
        });

        // Atualizar botão de carregar mais
        const loadMoreButton = document.getElementById('carregar-mais-publications');
        if (loadMoreButton) {
            loadMoreButton.classList.toggle('hidden', !this.hasMorePublications);
        }
    }

    createPublicationCard(publication) {
        const article = document.createElement('article');
        article.className = 'bg-gradient-to-br from-white to-[#F5F0E8] rounded-2xl shadow-lg card-hover border border-[#F5F0E8] opacity-0 translate-y-4 transition-all duration-500';
        
        // Gradientes por tipo de publicação
        const gradients = {
            'revista': 'from-[#8B2635] to-[#992D3D]',
            'boletim': 'from-[#4A6B8A] to-[#8B2635]',
            'outro': 'from-[#D4A574] to-[#8B2635]'
        };
        
        const gradientClass = gradients[publication.type] || 'from-[#8B2635] to-[#992D3D]';
        
        article.innerHTML = `
            <div class="h-48 bg-gradient-to-br ${gradientClass} relative flex items-center justify-center">
                <div class="text-center">
                    <span class="material-icons text-white text-6xl">${publication.type === 'revista' ? 'menu_book' : 'description'}</span>
                    <div class="mt-2 text-white text-sm">${publication.type === 'revista' ? 'Revista' : 'Boletim'}</div>
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(publication.date)} • Edição ${publication.number || 'N/A'}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${publication.title}</h3>
                <p class="text-[#8B2635] mb-4">${publication.description || 'Publicação histórica do CCCRJ'}</p>
                <button class="w-full bg-[#8B2635] text-white py-2 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium flex items-center justify-center download-publication" data-file="${publication.file_path}">
                    <span class="material-icons mr-2">download</span> Baixar ${publication.type === 'revista' ? 'Revista' : 'Boletim'}
                </button>
            </div>
        `;
        
        // Adicionar evento de clique para download
        const downloadButton = article.querySelector('.download-publication');
        if (downloadButton) {
            downloadButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.downloadPublication(publication.file_path);
            });
        }
        
        // Adicionar animação de entrada
        setTimeout(() => {
            article.classList.remove('opacity-0', 'translate-y-4');
        }, 100);

        return article;
    }

    async downloadPublication(filePath) {
        try {
            // Abrir o arquivo em uma nova aba para download
            window.open(`api/json/publications/download.php?file=${encodeURIComponent(filePath)}`, '_blank');
        } catch (error) {
            console.error('Erro ao baixar publicação:', error);
            alert('Não foi possível baixar a publicação. Tente novamente mais tarde.');
        }
    }

    async loadMorePublications() {
        this.currentPage++;
        await this.loadPublications();
    }

    bindEvents() {
        const loadMoreButton = document.getElementById('carregar-mais-publications');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => this.loadMorePublications());
        }
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? 'Data não disponível' : date.toLocaleDateString('pt-BR', options);
    }

    calculateReadingTime(text) {
        const wordsPerMinute = 200;
        const words = text.split(/\s+/).length;
        const minutes = Math.ceil(words / wordsPerMinute);
        return `${minutes} min de leitura`;
    }

    showError(show, message = null) {
        const error = document.getElementById('publications-error');
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
    // Apenas inicializar se o elemento publications-container existir na página
    if (document.getElementById('publications-container')) {
        new PublicationsManager();
    }
});