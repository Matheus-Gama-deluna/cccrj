// Lógica para as notícias
class NewsManager {
    constructor() {
        this.newsContainer = document.getElementById('news-container');
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.isLoading = false;
        this.hasMoreNews = true;
        this.init();
    }

    init() {
        // Carregar notícias iniciais
        this.loadNews(true);
        
        // Adicionar evento para carregar mais notícias
        const loadMoreButton = document.getElementById('carregar-mais');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => this.loadNews());
        }
    }

    async loadNews(clear = false) {
        if (this.isLoading || (!this.hasMoreNews && !clear)) return;
        
        this.isLoading = true;
        this.toggleLoading(true);
        this.showError(false);
        
        try {
            // Simular chamada à API
            // Em implementação real, substituir por fetch real
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Dados mockados para teste
            const data = {
                noticias: [
                    {
                        id: 1,
                        categoria: 'Produção',
                        data: '2024-07-20',
                        titulo: 'Colheita de café no Rio de Janeiro tem melhores resultados do ano',
                        resumo: 'As condições climáticas favoráveis têm acelerado o ritmo da colheita na principal região produtora...',
                        conteudo: 'Texto completo da notícia...',
                        icone: 'agriculture'
                    },
                    {
                        id: 2,
                        categoria: 'Exportação',
                        data: '2024-07-18',
                        titulo: 'Exportações registram alta de 15%',
                        resumo: 'Os números recentes apontam para um aumento significativo nas exportações brasileiras...',
                        conteudo: 'Texto completo da notícia...',
                        icone: 'article'
                    },
                    {
                        id: 3,
                        categoria: 'Evento',
                        data: '2024-07-15',
                        titulo: 'Futuro dos cafés especiais em debate',
                        resumo: 'Especialistas discutem tendências e desafios do mercado premium...',
                        conteudo: 'Texto completo da notícia...',
                        icone: 'event'
                    }
                ],
                temMais: false
            };
            
            if (clear) {
                if (this.newsContainer) this.newsContainer.innerHTML = '';
                this.currentPage = 1;
            }
            
            data.noticias.forEach(noticia => {
                const card = this.createNewsCard(noticia);
                if (this.newsContainer) this.newsContainer.appendChild(card);
                
                // Animar entrada do card
                setTimeout(() => {
                    card.querySelector('article').classList.remove('opacity-0', 'translate-y-4');
                }, 100);
            });
            
            this.hasMoreNews = data.temMais;
            const loadMoreButton = document.getElementById('carregar-mais');
            if (loadMoreButton) {
                loadMoreButton.classList.toggle('hidden', !this.hasMoreNews);
            }
            
            if (!clear) this.currentPage++;
        } catch (error) {
            console.error('Erro ao carregar notícias:', error);
            this.showError(true);
        } finally {
            this.toggleLoading(false);
            this.isLoading = false;
        }
    }

    createNewsCard(noticia) {
        // Criar elemento para o card da notícia
        const article = document.createElement('article');
        article.className = 'bg-white rounded-2xl shadow-lg overflow-hidden card-hover opacity-0 translate-y-4 transition-all duration-500';
        
        // Gradientes por categoria
        const gradientes = {
            'Produção': 'from-[#8B2635] to-[#992D3D]',
            'Exportação': 'from-blue-400 to-indigo-500',
            'Evento': 'from-[#4A6B8A] to-[#8B2635]'
        };
        
        const gradientClass = gradientes[noticia.categoria] || 'from-[#8B2635] to-[#992D3D]';
        
        article.innerHTML = `
            <div class="h-48 bg-gradient-to-br ${gradientClass} relative">
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="material-icons text-white text-6xl">${noticia.icone || 'article'}</span>
                </div>
                <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded-full text-sm font-medium text-[#8B2635]">
                    ${noticia.categoria}
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(noticia.data)} • ${this.calculateReadingTime(noticia.conteudo)}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${noticia.titulo}</h3>
                <p class="text-[#8B2635] mb-4">${noticia.resumo}</p>
                <button class="text-[#8B2635] hover:text-[#992D3D] font-medium flex items-center group">
                    Leia mais 
                    <span class="material-icons ml-1 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>
        `;
        
        return article;
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('pt-BR', options);
    }

    calculateReadingTime(text) {
        const wordsPerMinute = 200;
        const words = text.split(/\s+/).length;
        const minutes = Math.ceil(words / wordsPerMinute);
        return `${minutes} min de leitura`;
    }

    toggleLoading(show) {
        const loading = document.getElementById('noticias-loading');
        if (loading) {
            loading.classList.toggle('hidden', !show);
        }
        if (this.newsContainer) {
            this.newsContainer.classList.toggle('opacity-50', show);
        }
    }

    showError(show) {
        const error = document.getElementById('noticias-error');
        if (error) {
            error.classList.toggle('hidden', !show);
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new NewsManager();
});