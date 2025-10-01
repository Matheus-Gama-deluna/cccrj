// Lógica para as notícias
class NewsManager {
    constructor() {
        this.newsContainer = document.getElementById('news-container');
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.isLoading = false;
        this.hasMoreNews = true;
        this.newsData = [];
        this.apiUrl = 'api/news_scraper.php?get_static_news=1'; // Nova URL para obter notícias estáticas
        this.init();
    }

    async init() {
        await this.loadNewsData();
        this.loadNews(true);
        
        // Adicionar evento para carregar mais notícias
        const loadMoreButton = document.getElementById('carregar-mais');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => this.loadNews());
        }
    }

    async loadNewsData() {
        try {
            // Mostrar indicador de carregamento
            this.toggleLoading(true);
            
            // Chamar API para obter notícias estáticas
            const response = await fetch(this.apiUrl);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const data = await response.json();
            this.newsData = data.noticias || [];
            
            // Atualizar informações de última atualização (opcional)
            this.updateLastUpdateInfo(data.ultima_atualizacao);
        } catch (error) {
            console.error('Erro ao carregar dados das notícias:', error);
            this.showError(true, 'Não foi possível carregar as notícias. Tente novamente mais tarde.');
        } finally {
            this.toggleLoading(false);
        }
    }

    updateLastUpdateInfo(date) {
        const lastUpdateElement = document.getElementById('last-news-update');
        if (lastUpdateElement && date) {
            lastUpdateElement.textContent = `Última atualização: ${new Date(date).toLocaleString('pt-BR')}`;
        }
    }

    loadNews(clear = false) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.toggleLoading(true);
        this.showError(false);
        
        try {
            if (clear) {
                if (this.newsContainer) this.newsContainer.innerHTML = '';
                this.currentPage = 1;
            }
            
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            const newsToShow = this.newsData.slice(startIndex, endIndex);
            
            this.hasMoreNews = endIndex < this.newsData.length;
            
            newsToShow.forEach(noticia => {
                const card = this.createNewsCard(noticia);
                if (this.newsContainer) this.newsContainer.appendChild(card);
                
                // Adicionar evento de clique para ler mais
                const readMoreButton = card.querySelector('.read-more-button');
                if (readMoreButton) {
                    readMoreButton.addEventListener('click', () => this.showFullNews(noticia.link));
                }
                
                // Animar entrada do card
                setTimeout(() => {
                    card.classList.remove('opacity-0', 'translate-y-4');
                }, 100);
            });
            
            const loadMoreButton = document.getElementById('carregar-mais');
            if (loadMoreButton) {
                loadMoreButton.classList.toggle('hidden', !this.hasMoreNews);
            }
            
            this.currentPage++;
        } catch (error) {
            console.error('Erro ao carregar notícias:', error);
            this.showError(true, 'Ocorreu um erro ao carregar as notícias.');
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
                <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(noticia.data)} • ${this.calculateReadingTime(noticia.resumo)}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${noticia.titulo}</h3>
                <p class="text-[#8B2635] mb-4">${noticia.resumo}</p>
                <button class="read-more-button text-[#8B2635] hover:text-[#992D3D] font-medium flex items-center group">
                    Leia mais 
                    <span class="material-icons ml-1 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>
        `;
        
        return article;
    }

    async showFullNews(newsUrl) {
        try {
            // Mostrar modal ou nova página com conteúdo completo
            // Esta função pode ser expandida para mostrar o conteúdo em um modal
            window.open(newsUrl, '_blank');
        } catch (error) {
            console.error('Erro ao carregar notícia completa:', error);
            alert('Não foi possível carregar a notícia completa. Você será redirecionado para o site original.');
            window.open(newsUrl, '_blank');
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

    toggleLoading(show) {
        const loading = document.getElementById('noticias-loading');
        if (loading) {
            loading.classList.toggle('hidden', !show);
        }
        if (this.newsContainer) {
            this.newsContainer.classList.toggle('opacity-50', show);
        }
    }

    showError(show, message = null) {
        const error = document.getElementById('noticias-error');
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
    new NewsManager();
});