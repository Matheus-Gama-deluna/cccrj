<?php
// implement_components.php

// Script para implementar os novos componentes no sistema atual

echo "Implementando novos componentes no sistema atual...\n\n";

// 1. Criar componentes JavaScript
echo "1. Criando componentes JavaScript...\n";

function createJsComponents() {
    $componentsDir = 'assets/js/components';
    if (!is_dir($componentsDir)) {
        mkdir($componentsDir, 0755, true);
    }
    
    // Componente de Clipping
    $clippingComponent = <<<JS
// Componente ClippingManager em assets/js/components/clipping.js
class ClippingManager {
    constructor() {
        this.clippings = [];
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.hasMoreClippings = true;
        this.apiUrl = 'api/clipping/list.php';
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
            
            const response = await fetch(`\${this.apiUrl}?\${params}`);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: \${response.status}`);
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
        
        article.innerHTML = \`
            <div class="h-48 bg-gradient-to-br \${gradientClass} relative">
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="material-icons text-white text-6xl">article</span>
                </div>
                <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded-full text-sm font-medium text-[#8B2635]">
                    \${clipping.category || 'Clipping'}
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">\${this.formatDate(clipping.date)} • \${this.calculateReadingTime(clipping.content)}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">\${clipping.title}</h3>
                <p class="text-[#8B2635] mb-4">\${clipping.summary || clipping.content.substring(0, 150) + '...'}</p>
                <a href="\${clipping.source_url || '#'}" target="_blank" class="read-more-button text-[#8B2635] hover:text-[#992D3D] font-medium flex items-center group">
                    Leia mais 
                    <span class="material-icons ml-1 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
        \`;
        
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
        return `\${date.getDate()} de \${month} de \${date.getFullYear()}`;
    }

    calculateReadingTime(text) {
        const wordsPerMinute = 200;
        const words = text.split(/\\s+/).length;
        const minutes = Math.ceil(words / wordsPerMinute);
        return `\${minutes} min de leitura`;
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
JS;

    file_put_contents("$componentsDir/clipping.js", $clippingComponent);
    echo "✓ Componente de clipping criado\n";
    
    // Componente de Publicações
    $publicationsComponent = <<<JS
// Componente PublicationsManager em assets/js/components/publications.js
class PublicationsManager {
    constructor() {
        this.publications = [];
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.hasMorePublications = true;
        this.apiUrl = 'api/publications/list.php';
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
            
            const response = await fetch(`\${this.apiUrl}?\${params}`);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: \${response.status}`);
            }
            
            const data = await response.json();
            this.publications = [...this.publications, ...data.data];
            this.hasMorePublications = data.hasMore;
            
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
        
        article.innerHTML = \`
            <div class="h-48 bg-gradient-to-br \${gradientClass} relative flex items-center justify-center">
                <div class="text-center">
                    <span class="material-icons text-white text-6xl">\${publication.type === 'revista' ? 'menu_book' : 'description'}</span>
                    <div class="mt-2 text-white text-sm">\${publication.type === 'revista' ? 'Revista' : 'Boletim'}</div>
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">\${this.formatDate(publication.date)} • Edição \${publication.number || 'N/A'}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">\${publication.title}</h3>
                <p class="text-[#8B2635] mb-4">\${publication.description || 'Publicação histórica do CCCRJ'}</p>
                <button class="w-full bg-[#8B2635] text-white py-2 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium flex items-center justify-center download-publication" data-file="\${publication.file_path}">
                    <span class="material-icons mr-2">download</span> Baixar \${publication.type === 'revista' ? 'Revista' : 'Boletim'}
                </button>
            </div>
        \`;
        
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
            window.open(\`api/publications/download.php?file=\${encodeURIComponent(filePath)}\`, '_blank');
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
        const words = text.split(/\\s+/).length;
        const minutes = Math.ceil(words / wordsPerMinute);
        return `\${minutes} min de leitura`;
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
JS;

    file_put_contents("$componentsDir/publications.js", $publicationsComponent);
    echo "✓ Componente de publicações criado\n";
    
    // Componente de História
    $historyComponent = <<<JS
// Componente HistoryManager em assets/js/components/history.js
class HistoryManager {
    constructor() {
        this.events = [];
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.hasMoreEvents = true;
        this.apiUrl = 'api/history/list.php';
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
            
            const response = await fetch(`\${this.apiUrl}?\${params}`);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: \${response.status}`);
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
        
        div.innerHTML = \`
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-12 h-12 rounded-full bg-[#8B2635] flex items-center justify-center">
                        <span class="material-icons text-white">history</span>
                    </div>
                </div>
                <div class="flex-grow">
                    <div class="text-sm font-semibold text-[#8B2635]">\${this.formatDate(event.date)} • \${event.event_type}</div>
                    <h3 class="text-xl font-bold text-[#6B4423] mt-1 mb-2">\${event.title}</h3>
                    <p class="text-[#8B2635]">\${event.description || event.content.substring(0, 200) + '...'}</p>
                    \${event.image_url ? \`<div class="mt-4"><img src="\${event.image_url}" alt="\${event.title}" class="rounded-lg max-w-full h-auto"></div>\` : ''}
                </div>
            </div>
        \`;
        
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
        return `\${date.getDate()} de \${month} de \${date.getFullYear()}`;
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
JS;

    file_put_contents("$componentsDir/history.js", $historyComponent);
    echo "✓ Componente de história criado\n";
    
    // Componente Sobre Nós
    $aboutComponent = <<<JS
// Componente AboutManager em assets/js/components/about.js
class AboutManager {
    constructor() {
        this.sections = [];
        this.apiUrl = 'api/about/list.php';
        this.init();
    }

    async init() {
        await this.loadSections();
        this.bindEvents();
    }

    async loadSections() {
        try {
            const response = await fetch(this.apiUrl);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: \${response.status}`);
            }
            
            const data = await response.json();
            this.sections = data.data;
            
            this.renderSections();
            
        } catch (error) {
            console.error('Erro ao carregar seções sobre:', error);
            this.showError(true, 'Não foi possível carregar as seções sobre. Tente novamente mais tarde.');
        }
    }

    renderSections() {
        const aboutContainer = document.getElementById('about-container');
        if (!aboutContainer) return;

        this.sections.forEach(section => {
            const sectionElement = this.createSectionElement(section);
            aboutContainer.appendChild(sectionElement);
        });
    }

    createSectionElement(section) {
        const div = document.createElement('div');
        div.className = 'about-section bg-white p-6 rounded-lg shadow mb-6 border border-[#F5F0E8] opacity-0 translate-y-4 transition-all duration-500';
        
        div.innerHTML = \`
            <h3 class="text-2xl font-bold text-[#6B4423] mb-4">\${section.title}</h3>
            <div class="text-[#8B2635] mb-4">
                \${section.content}
            </div>
            \${section.image_url ? \`<div class="mt-4"><img src="\${section.image_url}" alt="\${section.title}" class="rounded-lg max-w-full h-auto"></div>\` : ''}
        \`;
        
        // Adicionar animação de entrada
        setTimeout(() => {
            div.classList.remove('opacity-0', 'translate-y-4');
        }, 100);

        return div;
    }

    bindEvents() {
        // Eventos específicos para a seção sobre
    }

    showError(show, message = null) {
        const error = document.getElementById('about-error');
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
    // Apenas inicializar se o elemento about-container existir na página
    if (document.getElementById('about-container')) {
        new AboutManager();
    }
});
JS;

    file_put_contents("$componentsDir/about.js", $aboutComponent);
    echo "✓ Componente sobre nós criado\n";
}

createJsComponents();
echo "\n";

// 2. Atualizar main.js para incluir novos componentes
echo "2. Atualizando main.js para incluir novos componentes...\n";

$mainJs = file_get_contents('assets/js/main.js');
if ($mainJs) {
    // Verificar se os componentes já estão registrados
    if (strpos($mainJs, 'ClippingManager') === false) {
        // Adicionar inicialização dos novos componentes
        $newInitCode = "
    // Inicializar componentes adicionais se os elementos existirem
    if (typeof ClippingManager !== 'undefined' && document.getElementById('clipping-container')) {
        new ClippingManager();
    }
    
    if (typeof PublicationsManager !== 'undefined' && document.getElementById('publications-container')) {
        new PublicationsManager();
    }
    
    if (typeof HistoryManager !== 'undefined' && document.getElementById('history-container')) {
        new HistoryManager();
    }
    
    if (typeof AboutManager !== 'undefined' && document.getElementById('about-container')) {
        new AboutManager();
    }
";
        
        // Inserir antes do fechamento da função DOMContentLoaded
        $mainJs = str_replace(
            "// Inicializar outros componentes se os elementos existirem na página",
            "// Inicializar outros componentes se os elementos existirem na página" . $newInitCode,
            $mainJs
        );
        
        file_put_contents('assets/js/main.js', $mainJs);
        echo "✓ main.js atualizado com novos componentes\n";
    } else {
        echo "✓ main.js já contém os novos componentes\n";
    }
} else {
    echo "✗ Erro ao ler main.js\n";
}
echo "\n";

// 3. Atualizar index.html para incluir novas seções
echo "3. Atualizando index.html para incluir novas seções...\n";

$indexHtml = file_get_contents('index.html');
if ($indexHtml) {
    // Verificar se a seção "Sobre Nós" já existe
    if (strpos($indexHtml, 'id="sobre"') === false) {
        // Adicionar seção "Sobre Nós" antes da seção de notícias
        $sobreSection = '
    <!-- Sobre Nós -->
    <section class="py-20 bg-white" id="sobre">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-[#6B4423] mb-4">Sobre o CCCRJ</h2>
                <p class="text-xl text-[#8B2635]">Conheça a história e atuação do Centro de Comércio do Café do Rio de Janeiro</p>
                <div class="w-24 h-1 bg-[#8B2635] mx-auto mt-4"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
                <div>
                    <h3 class="text-2xl font-bold text-[#6B4423] mb-4">Nossa História</h3>
                    <p class="text-[#8B2635] mb-4">O Centro do Comércio do Café do Rio de Janeiro foi fundado em 1901 com o objetivo de defender os direitos e interesses do comércio de café. Ao longo de mais de um século de existência, temos atuado de forma constante na política cafeeira, sempre buscando medidas que sejam benéficas ao setor.</p>
                    <p class="text-[#8B2635]">O CCCRJ foi uma presença constante em todos os momentos de crise ou de euforia cafeeira. E continua atuante nesta época em que o café tenta conquistar os seus mercados e o Porto do Rio de Janeiro volta a ser um importante porto de escoamento do café brasileiro para o exterior.</p>
                </div>
                <div class="flex justify-center">
                    <img src="assets/images/cccrj-historia.jpg" alt="Sede do CCCRJ" class="rounded-lg shadow-lg max-w-full h-auto">
                </div>
            </div>

            <div class="mb-12">
                <h3 class="text-2xl font-bold text-[#6B4423] mb-6 text-center">Estatutos e Diretoria</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-[#F5F0E8] to-white p-6 rounded-xl shadow-md">
                        <div class="text-center">
                            <span class="material-icons text-4xl text-[#8B2635] mb-3">gavel</span>
                            <h4 class="font-bold text-[#6B4423] mb-2">Estatuto</h4>
                            <p class="text-[#8B2635] text-sm">Conheça as diretrizes que regem nossa instituição</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-[#F5F0E8] to-white p-6 rounded-xl shadow-md">
                        <div class="text-center">
                            <span class="material-icons text-4xl text-[#8B2635] mb-3">group</span>
                            <h4 class="font-bold text-[#6B4423] mb-2">Diretoria</h4>
                            <p class="text-[#8B2635] text-sm">Conheça os membros da atual diretoria</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-[#F5F0E8] to-white p-6 rounded-xl shadow-md">
                        <div class="text-center">
                            <span class="material-icons text-4xl text-[#8B2635] mb-3">history_edu</span>
                            <h4 class="font-bold text-[#6B4423] mb-2">História</h4>
                            <p class="text-[#8B2635] text-sm">Nossa trajetória ao longo dos anos</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="about-container" class="mt-12">
                <!-- Conteúdo sobre o CCCRJ será carregado via JavaScript -->
            </div>
        </div>
    </section>

';
        
        // Inserir a seção antes da seção de notícias
        $indexHtml = str_replace(
            '<!-- Notícias Modernas -->',
            $sobreSection . "\n<!-- Notícias Modernas -->",
            $indexHtml
        );
        
        file_put_contents('index.html', $indexHtml);
        echo "✓ Seção 'Sobre Nós' adicionada ao index.html\n";
    } else {
        echo "✓ Seção 'Sobre Nós' já existe no index.html\n";
    }
    
    // Verificar se os scripts dos novos componentes já estão incluídos
    if (strpos($indexHtml, 'clipping.js') === false) {
        // Adicionar scripts dos novos componentes
        $newScripts = '
    <script src="assets/js/components/clipping.js"></script>
    <script src="assets/js/components/publications.js"></script>
    <script src="assets/js/components/history.js"></script>
    <script src="assets/js/components/about.js"></script>';
        
        // Inserir antes do fechamento do body
        $indexHtml = str_replace(
            '</body>',
            $newScripts . "\n</body>",
            $indexHtml
        );
        
        file_put_contents('index.html', $indexHtml);
        echo "✓ Scripts dos novos componentes adicionados ao index.html\n";
    } else {
        echo "✓ Scripts dos novos componentes já estão incluídos no index.html\n";
    }
} else {
    echo "✗ Erro ao ler index.html\n";
}
echo "\n";

// 4. Atualizar menu de navegação
echo "4. Atualizando menu de navegação...\n";

$indexHtml = file_get_contents('index.html');
if ($indexHtml) {
    // Verificar se o link para "Sobre" já existe
    if (strpos($indexHtml, 'href="#sobre"') === false) {
        // Adicionar link para "Sobre" no menu
        $sobreLink = '<a class="text-[#6B4423] hover:text-[#8B2635] transition-colors duration-300 font-medium" href="#sobre">Sobre</a>';
        
        // Inserir antes do link de "Cotações"
        $indexHtml = str_replace(
            '<a class="text-[#6B4423] hover:text-[#8B2635] transition-colors duration-300 font-medium" href="#cotacao">Cotações</a>',
            $sobreLink . "\n                <a class=\"text-[#6B4423] hover:text-[#8B2635] transition-colors duration-300 font-medium\" href=\"#cotacao\">Cotações</a>",
            $indexHtml
        );
        
        file_put_contents('index.html', $indexHtml);
        echo "✓ Link para 'Sobre' adicionado ao menu de navegação\n";
    } else {
        echo "✓ Link para 'Sobre' já existe no menu de navegação\n";
    }
} else {
    echo "✗ Erro ao ler index.html\n";
}
echo "\n";

echo "✅ Implementação dos novos componentes concluída!\n";
echo "\nComponentes implementados:\n";
echo "- Clipping Manager (clipping.js)\n";
echo "- Publications Manager (publications.js)\n";
echo "- History Manager (history.js)\n";
echo "- About Manager (about.js)\n";
echo "\nSeções adicionadas:\n";
echo "- Sobre Nós\n";
echo "\nRecursos atualizados:\n";
echo "- main.js (inicialização de novos componentes)\n";
echo "- index.html (nova seção e scripts)\n";
echo "- Menu de navegação (link para Sobre)\n";

?>