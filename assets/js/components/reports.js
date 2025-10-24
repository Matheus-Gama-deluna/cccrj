// Lógica para os boletins em PDF
// Variável global para controlar inicialização dos boletins
let reportsInitialized = false;

class ReportsManager {
    constructor() {
        this.reportsContainer = document.getElementById('reports-container');
        this.apiUrl = 'api/reports/list.php?type=boletins'; // URL da API PHP
        this.downloadUrl = 'api/reports/download.php?file='; // URL para download
        this.currentCategory = 'all'; // Adicionando filtro por categoria
        this.init();
    }

    init() {
        // Previne múltiplas inicializações
        if (reportsInitialized || !this.reportsContainer) {
            return;
        }

        reportsInitialized = true;

        // Carregar relatórios iniciais
        this.loadReports();

        // Adicionar funcionalidade de filtro por categoria se o elemento existir
        this.addCategoryFilter();
    }

    async loadReports() {
        try {
            // Mostrar indicador de carregamento
            this.showLoading();

            // Chamar API para obter boletins
            let apiUrl = 'api/reports/list.php?type=boletins';
            if (this.currentCategory && this.currentCategory !== 'all') {
                apiUrl += `&category=${this.currentCategory}`;
            }

            const response = await fetch(apiUrl, {
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });

            // Verifica se a resposta é JSON
            const contentType = response.headers.get('content-type') || '';
            const isJson = contentType.includes('application/json');

            if (!isJson) {
                // Se não for JSON, pega o texto para ver o que foi retornado
                const textResponse = await response.text();
                throw new Error('O servidor retornou uma resposta inválida. Por favor, verifique os logs do servidor.');
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `Erro ao carregar boletins (${response.status})`);
            }

            // Verifica se a resposta tem a estrutura esperada
            const reports = data.data || [];

            // Limpar container completamente (remover cards estáticos também)
            if (this.reportsContainer) {
                this.reportsContainer.innerHTML = '';

                // Adicionar relatórios ao container
                if (reports.length > 0) {
                    reports.forEach(report => {
                        try {
                            // Garante que o relatório tenha as propriedades necessárias
                            const normalizedReport = {
                                name: report.name || 'Boletim sem nome',
                                path: report.path || '',
                                size: report.size || 0,
                                modified: report.modified || new Date().toISOString(),
                                url: report.url || `/api/reports/download?file=${encodeURIComponent(report.name || '')}`,
                                // Extrai categoria do caminho ou usa padrão
                                category: this.extractCategoryFromPath(report.path) || 'outro'
                            };

                            const card = this.createReportCard(normalizedReport);
                            this.reportsContainer.appendChild(card);
                        } catch (cardError) {
                            // Silently handle card creation errors to prevent console spam
                        }
                    });

                    if (this.reportsContainer.children.length === 0) {
                        this.showEmptyState();
                    }
                } else {
                    this.showEmptyState();
                }
            }
        } catch (error) {
            this.showError(error.message || 'Não foi possível carregar os boletins. Tente novamente mais tarde.');
        } finally {
            // Esconder indicador de carregamento
            this.hideLoading();
        }
    }

    /**
     * Extrai a categoria do caminho do arquivo
     * @param {string} path Caminho do arquivo
     * @returns {string} Categoria do arquivo
     */
    extractCategoryFromPath(path) {
        if (!path) return 'outro';

        const pathLower = path.toLowerCase();

        if (pathLower.includes('boletins') || pathLower.includes('boletim') || pathLower.includes('bulletin')) return 'relatorio';
        if (pathLower.includes('publicacoes') || pathLower.includes('publications')) return 'publicacao';
        if (pathLower.includes('acervo') || pathLower.includes('archive')) return 'acervo';

        return 'outro';
    }

    createReportCard(report) {
        // Criar elemento para o card do boletim
        const article = document.createElement('div');
        article.className = 'bg-gradient-to-br from-white to-[#F5F0E8] rounded-2xl shadow-lg card-hover border border-[#F5F0E8]';

        // Determinar tipo e gradientes com base nas informações do boletim
        let type = report.type || 'boletim'; // 'boletim', 'publicacao', 'acervo', etc.
        let title = report.title || this.formatFileName(report.name);
        let description = report.description || 'Documento em formato PDF';
        let icon = 'picture_as_pdf';
        let date = report.date || this.extractDateFromFilename(report.name) || new Date().toISOString().split('T')[0];

        // Gradientes para diferentes tipos de documentos
        const gradients = {
            'relatorio': 'from-[#8B2635] to-[#992D3D]',
            'boletim': 'from-[#8B2635] to-[#992D3D]',
            'publicacao': 'from-[#4A6B8A] to-[#8B2635]',
            'acervo': 'from-[#D4A574] to-[#A63545]',
            'outro': 'from-[#6B4423] to-[#8B2635]'
        };

        const gradient = gradients[type] || gradients['outro'];

        // Definir ícone com base no tipo
        article.innerHTML = `
            <div class="h-48 bg-gradient-to-br ${gradient} relative flex items-center justify-center">
                <div class="text-center">
                    <span class="material-icons text-white text-6xl">${icon}</span>
                    <div class="mt-2 text-white text-sm capitalize">${type === 'boletim' ? 'Boletim' : type}</div>
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(date)}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${title}</h3>
                <p class="text-[#8B2635] mb-4">${description}</p>
                <div class="flex gap-2">
                    <button onclick="pdfPreview.show('${report.name}', '${title}', 'boletins')"
                            class="flex-1 bg-white text-[#8B2635] py-2 px-4 rounded-lg border border-[#8B2635] hover:bg-[#F5F0E8] transition-colors duration-300 font-medium flex items-center justify-center preview-btn"
                            data-file="${report.name}">
                        <span class="material-icons mr-2 text-sm">visibility</span>
                        Ver PDF
                    </button>
                    <button class="flex-1 bg-[#8B2635] text-white py-2 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium flex items-center justify-center download-report"
                            data-file="${report.name}">
                        <span class="material-icons mr-2 text-sm">download</span>
                        Baixar
                    </button>
                </div>
            </div>
        `;

        // Adicionar evento de clique para download
        const downloadButton = article.querySelector('.download-report');
        if (downloadButton) {
            downloadButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.downloadReport(report.name);
            });
        }

        // Adicionar evento de clique para preview
        const previewButton = article.querySelector('.preview-btn');
        if (previewButton) {
            previewButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (pdfPreview && typeof pdfPreview.show === 'function') {
                    pdfPreview.show(report.name, title, 'boletins');
                } else {
                    console.error('ReportsManager: pdfPreview not available');
                }
            });
        }

        return article;
    }

    downloadReport(fileName) {
        try {
            // Abrir o arquivo em uma nova aba para download
            window.open('api/reports/download.php?file=' + encodeURIComponent(fileName) + '&type=boletins', '_blank');
        } catch (error) {
            console.error('Erro ao baixar boletim:', error);
            alert('Não foi possível baixar o boletim. Tente novamente mais tarde.');
        }
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? 'Data não disponível' : date.toLocaleDateString('pt-BR', options);
    }

    formatFileName(fileName) {
        // Remover extensão .pdf e substituir hífens/underscores por espaços
        return fileName.replace('.pdf', '')
                      .replace(/[-_]/g, ' ')
                      .replace(/\b\w/g, l => l.toUpperCase());
    }

    extractDateFromFilename(filename) {
        // Tentar extrair data do nome do arquivo (formato YYYY-MM-DD ou DD-MM-YYYY)
        const datePatterns = [
            /(\d{4})-(\d{2})-(\d{2})/,
            /(\d{2})-(\d{2})-(\d{4})/
        ];

        for (const pattern of datePatterns) {
            const match = filename.match(pattern);
            if (match) {
                if (match[1].length === 4) {
                    // Formato YYYY-MM-DD
                    return `${match[1]}-${match[2]}-${match[3]}`;
                } else {
                    // Formato DD-MM-YYYY
                    return `${match[3]}-${match[2]}-${match[1]}`;
                }
            }
        }

        return null;
    }

    showLoading() {
        if (this.reportsContainer) {
            this.reportsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#8B2635] mb-4"></div>
                    <p class="text-[#6B4423] text-xl">Carregando boletins...</p>
                </div>
            `;
        }
    }

    hideLoading() {
        // Este método é chamado no finally para garantir que o loading seja removido
        // A remoção do conteúdo é feita individualmente por cada método
    }

    showEmptyState() {
        if (this.reportsContainer) {
            this.reportsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-icons text-5xl text-[#8B2635] mb-4">folder_open</span>
                    <p class="text-[#6B4423] text-xl">Nenhum boletim encontrado</p>
                    <p class="text-[#8B2635] mt-2">Ainda não há boletins disponíveis para download.</p>
                </div>
            `;
        }
    }

    addCategoryFilter() {
        // Verificar se já existe um elemento de filtro na página
        const filterContainer = document.querySelector('.reports-filter');
        if (filterContainer) {
            // Criar elementos de filtro
            const filterHTML = `
                <div class="flex flex-wrap justify-center gap-4 mb-8">
                    <button class="filter-btn bg-[#8B2635] text-white py-2 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300" data-category="all">Todos</button>
                    <button class="filter-btn bg-white text-[#8B2635] py-2 px-4 rounded-lg border border-[#8B2635] hover:bg-[#F5F0E8] transition-colors duration-300" data-category="relatorio">Boletins Informativos</button>
                    <button class="filter-btn bg-white text-[#8B2635] py-2 px-4 rounded-lg border border-[#8B2635] hover:bg-[#F5F0E8] transition-colors duration-300" data-category="publicacao">Publicações</button>
                    <button class="filter-btn bg-white text-[#8B2635] py-2 px-4 rounded-lg border border-[#8B2635] hover:bg-[#F5F0E8] transition-colors duration-300" data-category="acervo">Acervo Histórico</button>
                </div>
            `;

            filterContainer.innerHTML = filterHTML;

            // Adicionar eventos de clique aos botões de filtro
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    // Remover classe ativa de todos os botões
                    filterButtons.forEach(btn => {
                        btn.classList.remove('bg-[#8B2635]', 'text-white');
                        btn.classList.add('bg-white', 'text-[#8B2635]');
                    });

                    // Adicionar classe ativa ao botão clicado
                    e.target.classList.remove('bg-white', 'text-[#8B2635]');
                    e.target.classList.add('bg-[#8B2635]', 'text-white');

                    // Atualizar categoria e recarregar relatórios
                    this.currentCategory = e.target.getAttribute('data-category');
                    this.loadReports();
                });
            });
        }
    }

    showError(message) {
        if (this.reportsContainer) {
            this.reportsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-icons text-5xl text-[#8B2635] mb-4">error</span>
                    <p class="text-[#6B4423] text-xl">${message}</p>
                    <button class="mt-4 bg-[#8B2635] text-white py-2 px-6 rounded-lg hover:bg-[#992D3D] transition-colors duration-300" onclick="reportsManager.loadReports()">
                        Tentar novamente
                    </button>
                </div>
            `;
        }
    }
}

// Inicializar quando o DOM estiver pronto
let reportsManager;
document.addEventListener('DOMContentLoaded', () => {
    reportsManager = new ReportsManager();
});